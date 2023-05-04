<?php
namespace Frontend_Admin\Field_Types;

if ( ! class_exists( 'Frontend_Admin\Field_Types\plans' ) ) :


	class plans extends Field_Base {


		/*
		*  __construct
		*
		*  This function will setup the field type data
		*
		*  @type    function
		*  @date    5/03/2014
		*  @since   5.0.0
		*
		*  @param   n/a
		*  @return  n/a
		*/

		function initialize() {

			// vars
			$this->name     = 'fea_plans';
			$this->label    = __( 'Plans', 'acf-frontend-form-element' );
			$this->category = 'relational';
			$this->public = false;
			$this->defaults = array(
				'allow_null'    => 0,
				'multiple'      => 0,
				'return_format' => 'object',
				'ui'            => 1,
			);


		}

		/*
		*  render_field()
		*
		*  Create the HTML interface for your field
		*
		*  @param   $field - an array holding all the field's data
		*
		*  @type    action
		*  @since   3.6
		*  @date    23/01/13
		*/

		function render_field( $field ) {

			// convert
			$value   = acf_get_array( $field['value'] );
			$plans = fea_instance()->plans_handler->get_plans();

			$choices = [];

			foreach( $plans as $plan ){
				$choices[$plan->ID] = $plan->title;
			}

			// placeholder
			if ( empty( $field['placeholder'] ) ) {
				$field['placeholder'] = _x( 'Select', 'verb', 'acf' );
			}

			// add empty value (allows '' to be selected)
			if ( empty( $value ) ) {
				$value = array( '' );
			}

			// prepend empty choice
			// - only for single selects
			// - have tried array_merge but this causes keys to re-index if is numeric (post ID's)
			if ( $field['allow_null'] && ! $field['multiple'] ) {
				$choices = array( '' => "- {$field['placeholder']} -" ) + $choices;
			}

		/* 	// clean up choices if using ajax
			if ( $field['ui'] && $field['ajax'] ) {
				$minimal = array();
				foreach ( $value as $key ) {
					if ( isset( $choices[ $key ] ) ) {
						$minimal[ $key ] = $choices[ $key ];
					}
				}
				$choices = $minimal;
			}
*/
			// vars
			$select = array(
				'id'               => $field['id'],
				'class'            => $field['class'],
				'name'             => $field['name'],
				'data-placeholder' => $field['placeholder'],
				'data-allow_null'  => $field['allow_null'],
			);

		

			// special atts
			if ( ! empty( $field['readonly'] ) ) {
				$select['readonly'] = 'readonly';
			}
			if ( ! empty( $field['disabled'] ) ) {
				$select['disabled'] = 'disabled';
			}
			if ( ! empty( $field['ajax_action'] ) ) {
				$select['data-ajax_action'] = $field['ajax_action'];
			}				


			// append
			$select['value']   = $value;
			$select['choices'] = $choices;

			// render
			acf_select_input( $select );
			?>
			<button type="button" class="acf-btn acf-btn-secondary add-plan"><?php esc_html_e( 'Add New Plan', 'acf-frontend-form-element' ); ?></button>

			<?php
		}


	}

endif; // class_exists check


