<?php
namespace Frontend_Admin\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if( ! class_exists( 'Frontend_Admin\Admin\Plans_Crud' ) ) :

	class Plans_Crud{
        public function create_plans() {
			global $wpdb;
			$charset_collate = $wpdb->get_charset_collate();
			$table_name = $wpdb->prefix . 'fea_plans';
			$sql = "CREATE TABLE $table_name (
				id mediumint(9) NOT NULL AUTO_INCREMENT,
				created_at datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
				expires_after text NOT NULL,
				title text NOT NULL,
				slug text NOT NULL,
				description text NULL,
				pricing int NOT NULL,
				currency text NOT NULL,
				plan_parent int NOT NULL,
				menu_order int NOT NULL,
				UNIQUE KEY id (id)
			) $charset_collate;";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            maybe_create_table( $table_name, $sql );
		}

		public function insert_plan( $args ){
			global $wpdb;
			$wpdb->insert( $wpdb->prefix . 'fea_plans', $args );
			return $wpdb->insert_id;
		}

		public function update_plan( $id, $args ){
			global $wpdb;
			$wpdb->update( 
				$wpdb->prefix . 'fea_plans', 
				$args,		
				array( 'id' => $id )			
			);
		}


		public function get_plan( $id = 0 ){
			if( ! $id ) return $id;

			global $wpdb;
			$plan = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}fea_plans WHERE id = %d", $id ) );

            if( $plan->$by == $id ) return $plan;

            return false;
		}

		/**
		 * Retrieve plans data from the database
		 *
		 * @param array $args query arguments
		 *
		 * @return mixed
		 */
		public static function get_plans( $args = array() ) {
			global $wpdb;

			$args = feadmin_parse_args( $args, array(
				'per_page' => 20,
				'current_page' => 1,
			) );

			$sql = "SELECT * FROM {$wpdb->prefix}fea_plans";

			if( ! empty( $_REQUEST['s'] ) ){
				$value = $_REQUEST['s'] . '%';
				$sql .= $wpdb->prepare( ' WHERE title LIKE %s', $value );
			}

			if ( ! empty( $_REQUEST['orderby'] ) ) {
				$sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
				$sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
			}else{
				$sql .= ' ORDER BY ' . sanitize_sql_orderby( 'created_at DESC' );
			}

			$sql .= $wpdb->prepare( " LIMIT %d", $args['per_page'] );
			$sql .= $wpdb->prepare( " OFFSET %d", ( $args['current_page'] - 1 ) * $args['per_page'] );	


			$result = $wpdb->get_results( $sql, 'ARRAY_A' );

			return $result;
		}

		/**
		 * Returns the count of records in the database.
		 *
		 * @return null|string
		 */
		public static function record_count() {
			global $wpdb;

			$sql = "SELECT COUNT(*) FROM {$wpdb->prefix}fea_plans";

			return $wpdb->get_var( $sql );
		}

		public function delete_plan( $id = 0 ){
			if( $id == 0 ) return $id;
			global $wpdb;
			$wpdb->delete( $wpdb->prefix.'fea_plans', array( 'id' => $id ) );
			return 1;
		}

        public function plans_page_options(){
			if( isset( $_REQUEST['page'] ) && $_REQUEST['page'] == 'fea-plans' ){
				$option = 'per_page';
				$args   = [
					'label'   => 'Plans',
					'default' => 20,
					'option'  => 'plans_per_page'
				];
				add_screen_option( $option, $args );
			}
		}	
		function set_plans_per_page($status, $option, $value) {
			if ( 'plans_per_page' == $option ) return $value;
            return $status;
		}
        public function plans_list(){
            global $fa_plans_page;
            $fa_plans_page = add_submenu_page( FEA_PRE.'-settings', __( 'Plans', 'acf-frontend-form-element' ), __( 'Plans', 'acf-frontend-form-element' ), 'manage_options', 'frontend-admin-plans', [ $this, 'admin_plans_page'], 82 );
            add_action( "load-$fa_plans_page", array( $this, 'plans_page_options' ) );
        }

		public function get_plan_form( $plan_id ) {
			if ( is_numeric( $plan_id ) ) {
				$plan = $this->get_plan( $plan_id );
				if ( ! $plan ) {
					esc_html_e( 'Plan not found. Did you erase it?', 'acf-frontend-form-element' );
					return false;
				}
			}


			if ( 'add_item' == $plan_id ) {
				$submit_value    = __( 'Create Plan', 'acf-frontend-form-element' );
				$success_message = __( 'Plan has been created successfully.', 'acf-frontend-form-element' );
				$update          = true;
				$defaults = [
					'title' => __( 'Basic', 'acf-frontend-form-element' ),
					'expires_after' => 'never',
					'pricing' => 1,
					'currency' => 'USD',
				];
			} else {
				$submit_value    = __( 'Updated', 'acf-frontend-form-element' );
				$success_message = __( 'Plan has been updated successfully.', 'acf-frontend-form-element' );
				$defaults = [
					'title' => $plan->title,
					'expires_after' => $plan->expires_after,
					'pricing' => $plan->pricing,
					'currency' => $plan->currency,
				];
			}

			if( function_exists( 'feadmin_get_stripe_currencies' ) ){					$currencies = feadmin_get_stripe_currencies();
			}else{
				$currencies = [];
			}		

			$form = array(
				'id'                  => 'fea_plan_form',
				'submit_value'        => $submit_value,
				'kses'                => 0,
				'no_cookies'          => 1,
				'no_record'           => 1,
				'ajax_submit'         => 0,
				'update_message'      => $success_message,
				'show_update_message' => 1,
				'custom_fields_save'  => 'plan', 
				'plan_id'			  => $plan_id,	
				'default_submit_button' => 1,			  
				'fields'			  => [
					array(
						'key'               => 'title',
						'name'               => 'title',
						'label'             => __( 'Title', 'acf-frontend-form-element' ),
						'type'              => 'text',
						'instructions'      => '',
						'default_value'     => $defaults['title'],
					),
					array(
						'key'               => 'expires_after',
						'name'              => 'expires_after',
						'label'             => __( 'Expires After', 'acf-frontend-form-element' ),
						'type'              => 'select',
						'choices'			=> [
							'never' => __( 'Never', 'acf-frontend-form-element' ),
							'week' => __( 'Week', 'acf-frontend-form-element' ),
							'month' => __( 'Month', 'acf-frontend-form-element' ),
							'year' => __( 'Year', 'acf-frontend-form-element' ),
						],
						'instructions'      => '',
						'default_value'     => $defaults['expires_after'],
						'multiple'	=> 0
					),
					array(
						'key'               => 'pricing',
						'name'               => 'pricing',
						'label'             => __( 'Price', 'acf-frontend-form-element' ),
						'type'              => 'number',
						'instructions'      => '',
						'min'    			=> 1,
						'default_value'     => $defaults['pricing'],
					),
					array(
						'key'               => 'currency',
						'name'               => 'currency',
						'label'             => __( 'Currency', 'acf-frontend-form-element' ),
						'type'              => 'select',
						'choices'			=> $currencies,
						'instructions'      => '',
						'default_value'     => $defaults['currency'],
						'multiple'	=> 0
					),
				],
			);

			return $form;
		}

        public function admin_plans_page(){ 
			require_once( 'list.php');
			$option = 'per_page';
			$args   = [
				'label'   => 'Plans',
				'default' => 20,
				'option'  => 'plans_per_page'
			];

			add_screen_option( $option, $args );

			?>
				<h2><?php echo __( 'Plans', 'acf-frontend-form-element' ) ?></h2>
				<a href="?page=frontend-admin-plans&action=add-new" type="button" class="button add-plan"><?php esc_html_e( 'Add New Plan', 'acf-frontend-form-element' ); ?></a>

				<?php
				fea_instance()->plans_list->prepare_items();
				fea_instance()->plans_list->display();
		}

		function save_plan( $form ){
			$record = $form['record'];
			if ( empty( $form['plan_id'] ) || empty( $record['fields']['plan'] ) ) {
				return $form;
			}

			if( is_numeric( $form['plan_id'] ) ){
				$this->update_plan( $form['plan_id'], $record['fields']['plan'] );
			}else{
				$this->insert_plan( $record['fields']['plan'] );
			}			


		}
       
        public function __construct() {
            $this->create_plans();	

			add_action( 'frontend_admin/form/on_submit', [ $this, 'save_plan' ] );

          // add_action( 'admin_menu', array( $this, 'plans_list' ), 20 );	
		//	add_filter( 'set-screen-option', array( $this, 'set_plans_per_page' ), 11, 3 );
        }
    }
    fea_instance()->plans_handler = new Plans_Crud;

endif;