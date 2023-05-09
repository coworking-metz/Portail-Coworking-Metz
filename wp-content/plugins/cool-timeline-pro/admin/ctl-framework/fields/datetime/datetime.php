<?php if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access pages directly.
/**
 *
 * Field: Date picker
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */

class CTL_Field_datetime extends CTL_Fields {

	public function __construct( $field, $value = '', $unique = '' ) {
		parent::__construct( $field, $value, $unique );
	}

	public function render() {
		add_action('admin_footer', 'wpc_admin_datetimepicker_js_function');
		echo $this->field_before();
		
		echo '<input type="text" name="'. $this->field_name() .'" id="'.$this->field_name().'" value="'. esc_attr( $this->value ) .'" class="ctl-date-picker" ' . $this->field_attributes() .'/>';
		echo $this->field_after();

		wp_register_script( 'ctl-flatpickr-script',CTP_PLUGIN_URL.'/assets/js/flatpickr.js', array('jquery'), CTLPV, true);
		wp_register_style( 'ctl-flatpickr-styles', CTP_PLUGIN_URL.'/assets/css/flatpickr.css' );
		wp_register_script( 'ctl-flatpickr-script-btn',CTP_PLUGIN_URL.'/assets/js/ctl-flatpicker-btn.js', array('jquery'), CTLPV, true);
		wp_register_style( 'ctl-flatpickr-styles-btn', CTP_PLUGIN_URL.'/assets/css/ctl-flatpicker-btn.css' );
		
		wp_enqueue_style('ctl-flatpickr-styles');		
		wp_enqueue_script('ctl-flatpickr-script');
		wp_enqueue_style('ctl-flatpickr-styles-btn');		
		wp_enqueue_script('ctl-flatpickr-script-btn');

	}
}

function wpc_admin_datetimepicker_js_function() {
	echo '<script>jQuery(function() {
			jQuery(".ctl-date-picker").flatpickr({
				dateFormat: "m/d/Y h:i K",
				enableTime: true,
				minuteIncrement:1,
				defaultMinute:0,
				minDate:"01/01/1000",
				maxDate:"12/31/2050",
				plugins: [
					ShortcutButtonsPlugin({
					  button: [		
						
						{
						  label: "Today"
						},
						{
						  label: "Tomorrow"
						},
						{
							label: "Close"
						},
						{
							label: "OK"
						}
					  ],
					  onClick: (index, fp) => {
						let date;
						switch (index) {
						  case 0:
							date = new Date();
							break;
						  case 1:
							date = new Date(Date.now() + 24 * 60 * 60 * 1000);
							break;
						  case 2:
							date = new Date(fp._initialDate);
							break;
							case 3:
							  date = new Date(fp.selectedDates);
							break;
						}
						fp.setDate(date);
						fp.close();
					  }
					 
					})
					
				  ]	 	 
			}); 
		});
	</script>';
}

