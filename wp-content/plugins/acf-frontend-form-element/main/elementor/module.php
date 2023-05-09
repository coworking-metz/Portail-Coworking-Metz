<?php

namespace Frontend_Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if ( ! class_exists( 'Frontend_Admin_Elementor' ) ) :

	class Frontend_Admin_Elementor {


		public $form_widgets         = array();
		public $elementor_categories = array();

		public static function find_element_recursive( $elements, $widget_id ) {
			foreach ( $elements as $element ) {
				if ( $widget_id == $element['id'] ) {
					return $element;
				}

				if ( ! empty( $element['elements'] ) ) {
					$element = self::find_element_recursive( $element['elements'], $widget_id );

					if ( $element ) {
						return $element;
					}
				}
			}

			return false;
		}

		public function widgets() {
			 $widget_list      = array(
				 'general' => array(
					 'frontend-form' => 'ACF_Frontend_Form_Widget',
					 'edit_button'   => 'Edit_Button_Widget',
				 ),
			 );

			$widget_list = array_merge(
				$widget_list,
				array(
					'posts' => array(
						'edit_post'      => 'Edit_Post_Widget',
						'new_post'       => 'New_Post_Widget',
						'duplicate_post' => 'Duplicate_Post_Widget',
						'delete_post'    => 'Delete_Post_Widget',
					),
					'terms' => array(
						'edit_term'   => 'Edit_Term_Widget',
						'new_term'    => 'New_Term_Widget',
						'delete_term' => 'Delete_Term_Widget',
					),
					'users' => array(
						'edit_user'   => 'Edit_User_Widget',
						'new_user'    => 'New_User_Widget',
						'delete_user' => 'Delete_User_Widget',
					),
				)
			);

			$widget_list = apply_filters( 'frontend_admin/elementor/widget_types', $widget_list );

			 $elementor = $this->get_elementor_instance();

			 foreach ( $widget_list as $folder => $widgets ) {
				 foreach ( $widgets as $filename => $classname ) {
					 include_once __DIR__ . "/widgets/$folder/$filename.php";
					 $classname = 'Frontend_Admin\Widgets\\' . $classname;
					 $elementor->widgets_manager->register_widget_type( new $classname() );
				 }
			 }

			 do_action( 'frontend_admin/widget_loaded' );

		}


		public function widget_categories( $elements_manager ) {
			$categories = array(
				'frontend-admin-general' => array(
					'title' => __( 'FRONTEND SITE MANAGEMENT', 'acf-frontend-form-element' ),
					'icon'  => 'fa fa-plug',
				),
			);

			foreach ( $categories as $name => $args ) {
				$this->elementor_categories[ $name ] = $args;
				$elements_manager->add_category( $name, $args );
			}

		}

		public function dynamic_tags( $dynamic_tags ) {
			\Elementor\Plugin::$instance->dynamic_tags->register_group(
				'frontend-admin-user-data',
				array(
					'title' => 'User',
				)
			);
			include_once __DIR__ . '/dynamic-tags/user-local-avatar.php';
			include_once __DIR__ . '/dynamic-tags/author-local-avatar.php';

			$dynamic_tags->register_tag( new DynamicTags\User_Local_Avatar_Tag() );
			$dynamic_tags->register_tag( new DynamicTags\Author_Local_Avatar_Tag() );
		}

		public function frontend_scripts() {
			wp_enqueue_style( 'fea-modal' );
			wp_enqueue_style( 'acf-global' );
			wp_enqueue_script( 'fea-modal' );
		}
		public function editor_scripts() {
			$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '-min';

			wp_enqueue_style( 'fea-icon', FEA_URL . 'assets/css/icon' . $min . '.css', array(), FEA_VERSION );
			wp_enqueue_style( 'fea-editor', FEA_URL . 'assets/css/editor' . $min . '.css', array(), FEA_VERSION );

			wp_enqueue_script( 'fea-editor', FEA_URL . 'assets/js/editor' . $min . '.js', array(), FEA_VERSION, true );

			wp_enqueue_style( 'acf-global' );
		}

		public function get_the_widget( $form_id ) {
			if ( is_array( $form_id ) ) {
				$widget_id = $form_id[0];
				$post_id   = $form_id[1];
			} else {
				return false;
			}

			if ( isset( $post_id ) ) {
				$elementor = $this->get_elementor_instance();

				$document = $elementor->documents->get( $post_id );

				if ( $document ) {
					$form = $this->find_element_recursive( $document->get_elements_data(), $widget_id );
				}

				if ( ! empty( $form['templateID'] ) ) {
					$template = $elementor->documents->get( $form['templateID'] );

					if ( $template ) {
						$global_meta = $template->get_elements_data();
						$form        = $global_meta[0];
					}
				}

				if ( ! $form ) {
					return false;
				}

				$widget = $elementor->elements_manager->create_element_instance( $form );

				return $widget;
			}
		}

		function get_elementor_instance() {
			 return \Elementor\Plugin::$instance;
		}

		function get_current_post_id() {
			$el = $this->get_elementor_instance();
			if ( isset( $el->documents ) ) {
				$current_page = $el->documents->get_current();
				if ( isset( $current_page ) ) {
					return $el->documents->get_current()->get_main_id();
				}
			}
			return get_the_ID();
		}

		public function __construct() {
			include_once __DIR__ . '/classes/content_tab.php';
			include_once __DIR__ . '/classes/modal.php';

			add_action( 'elementor/elements/categories_registered', array( $this, 'widget_categories' ) );
			add_action( 'elementor/widgets/register', array( $this, 'widgets' ) );

			add_action( 'elementor/dynamic_tags/register_tags', array( $this, 'dynamic_tags' ) );

			add_action( 'elementor/frontend/before_enqueue_scripts', array( $this, 'frontend_scripts' ) );
			add_action( 'elementor/editor/before_enqueue_scripts', array( $this, 'editor_scripts' ) );
		}
	}

	fea_instance()->elementor = new Frontend_Admin_Elementor();

endif;
