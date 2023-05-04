<?php
namespace Frontend_Admin\Widgets;

use Frontend_Admin\Widgets\ACF_Elementor_Form_Base;



/**

 *
 * @since 1.0.0
 */
class Edit_User_Widget extends ACF_Frontend_Form_Widget {


	/**
	 * Get widget name.
	 *
	 * Retrieve acf ele form widget name.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'edit_user';
	}

	public function hide_on_search() {
		if ( $this->get_name() == 'acf_ele_form' ) {
			return false;
		}
		return true;
	}
	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the acf ele form widget belongs to.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return array( '' );
	}

}
