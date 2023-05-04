<?php
namespace Bookmify;

if ( ! defined( 'ABSPATH' ) ) {	exit; }



/**
 * Rolse class.
 * @since 1.0.0
 */

class UserRoles{
	
	/**
     * Initiate Custom Roles
     */
	public static function init()
    {

		self::createCustomRoles(); // new custom roles

        $adminRole = get_role('administrator');
        if ($adminRole !== null) {
            foreach (self::$rolesList as $role) {
                $adminRole->add_cap($role);
            }
        }
    }
	
	/**
     * Generate Custom Roles
     */
    public static function createCustomRoles()
    {
        foreach (self::customRoles() as $customRole) {
			
			remove_role($customRole['name']);
			if( !self::role_exists($customRole['name'])){
				add_role($customRole['name'], $customRole['label'], $customRole['capabilities']);
			}
 			
        }
    }
	
	
	public static function role_exists( $role ) {

	  if( !empty( $role ) ) {
		return $GLOBALS['wp_roles']->is_role( $role );
	  }

	  return false;
	}

    /**
     * Array of all Bookmify roles capabilities
     */
    public static $rolesList = [
        'bookmify_be_read_menu',
        'bookmify_be_read_dashboard',
        'bookmify_be_read_calendar',
        'bookmify_be_read_appointments',
        'bookmify_be_read_employees',
        'bookmify_be_read_services',
        'bookmify_be_read_locations',
        'bookmify_be_read_coupons',
        'bookmify_be_read_customers',
        'bookmify_be_read_payments',
        'bookmify_be_read_notifications',
        'bookmify_be_read_customize',
        'bookmify_be_read_customfields',
        'bookmify_be_read_settings',
        'bookmify_be_read_taxes',
        'bookmify_be_read_shortcodes',

        'bookmify_be_read_others_calendar',
        'bookmify_be_read_others_appointments',
        'bookmify_be_read_others_employees',

        'bookmify_be_write_dashboard',
        'bookmify_be_write_calendar',
        'bookmify_be_write_appointments',
        'bookmify_be_write_employees',
        'bookmify_be_write_services',
        'bookmify_be_write_locations',
        'bookmify_be_write_coupons',
        'bookmify_be_write_customers',
        'bookmify_be_write_payments',
        'bookmify_be_write_notifications',
        'bookmify_be_write_customize',
        'bookmify_be_write_customfields',
        'bookmify_be_write_settings',
        'bookmify_be_write_status',

        'bookmify_be_write_others_calendar',
        'bookmify_be_write_others_appointments',
        'bookmify_be_write_others_employees',
        'bookmify_be_write_others_payments',

        'bookmify_be_delete_dashboard',
        'bookmify_be_delete_calendar',
        'bookmify_be_delete_appointments',
        'bookmify_be_delete_employees',
        'bookmify_be_delete_services',
        'bookmify_be_delete_locations',
        'bookmify_be_delete_coupons',
        'bookmify_be_delete_customers',
        'bookmify_be_delete_payments',
        'bookmify_be_delete_notifications',
        'bookmify_be_delete_customize',
        'bookmify_be_delete_customfields',
        'bookmify_be_delete_settings',
    ];

    /**
     * Array of all bookmify roles with capabilities
     */
    public static function customRoles()
    {
        return [
            [
                'name'         => 'bookmify-customer',
                'label'        => esc_html__('Bookmify Customer', 'bookmify'),
                'capabilities' => [
                    'read'                             		=> true,
                    'bookmify_be_read_menu'                 => true,
                    'bookmify_be_read_user_calendar'        => true,
                    'bookmify_be_read_user_appointments'    => true,
					'bookmify_be_read_user_profile'         => true,
                    'bookmify_be_write_user_profile'        => true,
                    'bookmify_be_write_user_appointments'   => true,
                ]
            ],
            [
                'name'         => 'bookmify-employee',
                'label'        => esc_html__('Bookmify Employee', 'bookmify'),
                'capabilities' => [
                    'read'                             		=> true,
                    'bookmify_be_read_menu'                 => true,
                    'bookmify_be_read_user_calendar'        => true,
                    'bookmify_be_read_user_appointments'    => true,
                    'bookmify_be_read_user_profile'         => true,
                    'bookmify_be_write_user_profile'        => true,
                    'bookmify_be_write_user_appointments'   => true,
                    'upload_files'                     		=> true,
                ]
            ],
            [
                'name'         => 'bookmify-manager',
                'label'        => esc_html__('Bookmify Manager', 'bookmify'),
                'capabilities' => [
                    'read' 									=> true,
                    'bookmify_be_read_menu'                 => true,
                    'bookmify_be_read_dashboard'            => true,
                    'bookmify_be_read_calendar'             => true,
                    'bookmify_be_read_appointments'         => true,
                    'bookmify_be_read_employees'            => true,
                    'bookmify_be_read_services'             => true,
                    'bookmify_be_read_locations'            => true,
                    'bookmify_be_read_coupons'              => true,
					'bookmify_be_read_customfields'			=> true,
                    'bookmify_be_read_customers'            => true,
                    'bookmify_be_read_payments'             => true,
                    'bookmify_be_read_notifications'        => true,
                    'bookmify_be_read_others_calendar'      => true,
                    'bookmify_be_read_others_appointments'  => true,
                    'bookmify_be_read_others_employees'     => true,
                    'bookmify_be_read_settings'  		    => true,
                    'bookmify_be_write_dashboard'           => true,
                    'bookmify_be_write_calendar'            => true,
                    'bookmify_be_write_appointments'        => true,
                    'bookmify_be_write_employees'           => true,
                    'bookmify_be_write_services'            => true,
                    'bookmify_be_write_locations'           => true,
                    'bookmify_be_write_coupons'             => true,
                    'bookmify_be_write_customers'           => true,
                    'bookmify_be_write_payments'            => true,
                    'bookmify_be_write_notifications'       => true,
                    'bookmify_be_write_others_calendar'     => true,
                    'bookmify_be_write_others_appointments' => true,
                    'bookmify_be_write_others_employees'    => true,
                    'bookmify_be_write_others_finance'      => true,
                    'bookmify_be_write_status_appointments' => true,
					'bookmify_be_read_taxes'				=> true,
					'bookmify_be_read_shortcodes'			=> true,
//                    'upload_files'                     		=> true,
                ]
            ],
        ];
    }
}