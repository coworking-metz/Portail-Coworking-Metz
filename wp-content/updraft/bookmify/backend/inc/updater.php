<?php
namespace Bookmify;



// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {exit; }


/**
 * Class Helper
 */
class Updater
{
	
	/**
	 * @since 1.0.0
	 * @access public
	 */
	public static function init(){
		self::updateTables();
		self::changeOptions();
	}
	
	public static function changeOptions(){
		/* since bookmify v1.3.0 */
		$maxTimeToBooking = get_option( 'bookmify_be_maxtime_tobooking', 'disabled' );
		if($maxTimeToBooking == 'disabled' || $maxTimeToBooking == 0){
			update_option('bookmify_be_maxtime_tobooking', 2);
		}
	}
	/**
     * Alter Tables.
	 * @since 1.0.0
     */
    public static function updateTables()
    {
		global $wpdb;
		$charset_collate 	= 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_general_ci';
		
		self::addColumnsQuery(array(
			
				/*** 	new column for payments		***/
				/*** 	tax_ids 					***/
				/*** 	since bookmify v1.0.2 					***/
				array(
					'tablename' 	=> $wpdb->prefix.'bmify_payments',
					'columnname' 	=> 'tax_ids',
					'query' 		=> 'ALTER TABLE `%s` ADD COLUMN `%s` TEXT DEFAULT NULL AFTER `paid`', 
				),
				/*** 	new column for coupons		***/
				/*** 	info 						***/
				/*** 	since bookmify v1.x.x					***/
				array(
					'tablename' 	=> $wpdb->prefix.'bmify_coupons',
					'columnname' 	=> 'info',
					'query' 		=> 'ALTER TABLE `%s` ADD COLUMN `%s` TEXT DEFAULT NULL AFTER `status`', 
				),
				/*** 	new column for coupons		***/
				/*** 	category_ids 				***/
				/*** 	since bookmify v1.x.x					***/
				array(
					'tablename' 	=> $wpdb->prefix.'bmify_coupons',
					'columnname' 	=> 'category_ids',
					'query' 		=> 'ALTER TABLE `%s` ADD COLUMN `%s` TEXT DEFAULT NULL AFTER `status`', 
				),
				/*** 	new column for coupons		***/
				/*** 	service_ids 				***/
				/*** 	since bookmify v1.x.x					***/
				array(
					'tablename' 	=> $wpdb->prefix.'bmify_coupons',
					'columnname' 	=> 'service_ids',
					'query' 		=> 'ALTER TABLE `%s` ADD COLUMN `%s` TEXT DEFAULT NULL AFTER `status`', 
				),
				/*** 	new column for coupons		***/
				/*** 	employee_ids 				***/
				/*** 	since bookmify v1.x.x					***/
				array(
					'tablename' 	=> $wpdb->prefix.'bmify_coupons',
					'columnname' 	=> 'employee_ids',
					'query' 		=> 'ALTER TABLE `%s` ADD COLUMN `%s` TEXT DEFAULT NULL AFTER `status`', 
				),
				/*** 	new column for coupons		***/
				/*** 	location_ids 				***/
				/*** 	since bookmify v1.x.x					***/
				array(
					'tablename' 	=> $wpdb->prefix.'bmify_coupons',
					'columnname' 	=> 'location_ids',
					'query' 		=> 'ALTER TABLE `%s` ADD COLUMN `%s` TEXT DEFAULT NULL AFTER `status`', 
				),
				/*** 	new column for coupons		***/
				/*** 	start_time_limit 			***/
				/*** 	since bookmify v1.x.x					***/
				array(
					'tablename' 	=> $wpdb->prefix.'bmify_coupons',
					'columnname' 	=> 'start_time_limit',
					'query' 		=> 'ALTER TABLE `%s` ADD COLUMN `%s` TIME DEFAULT NULL AFTER `status`', 
				),
				/*** 	new column for coupons		***/
				/*** 	end_time_limit 				***/
				/*** 	since bookmify v1.x.x					***/
				array(
					'tablename' 	=> $wpdb->prefix.'bmify_coupons',
					'columnname' 	=> 'end_time_limit',
					'query' 		=> 'ALTER TABLE `%s` ADD COLUMN `%s` TIME DEFAULT NULL AFTER `status`', 
				),
				/*** 	new column for coupons_used	***/
				/*** 	end_time_limit 				***/
				/*** 	since bookmify v1.x.x					***/
				array(
					'tablename' 	=> $wpdb->prefix.'bmify_coupons_used',
					'columnname' 	=> 'used_date',
					'query' 		=> 'ALTER TABLE `%s` ADD COLUMN `%s` DATETIME DEFAULT NULL AFTER `customer_id`', 
				),

				
			)
		);
		

		
		
		
		$refs = $wpdb->get_results( sprintf(
			'SELECT `constraint_name`, `referenced_table_name` FROM `information_schema`.`key_column_usage`
			WHERE `TABLE_SCHEMA` = SCHEMA() AND `TABLE_NAME` = "%s" AND `REFERENCED_TABLE_NAME` IS NOT NULL',
			$wpdb->prefix.'bmify_payments'
		) );
		
		if ( $refs ) 
		{
			foreach ( $refs as $ref ) 
			{
				$wpdb->query( sprintf( 'ALTER TABLE `%s` DROP FOREIGN KEY `%s`', $wpdb->prefix.'bmify_payments', $ref->constraint_name ) );
			}
		}
		
		
		$wpdb->query( sprintf(
			'ALTER TABLE `%s` ADD CONSTRAINT FOREIGN KEY (coupon_id) REFERENCES %s(id) ON DELETE CASCADE ON UPDATE CASCADE',
			$wpdb->prefix.'bmify_payments',
			$wpdb->prefix.'bmify_coupons'
		) );

		
		/* since 1.x.x */
		$wpdb->query( sprintf(
			'ALTER TABLE `%s` ADD CONSTRAINT FOREIGN KEY (coupon_id) REFERENCES %s(id) ON DELETE CASCADE ON UPDATE CASCADE',
			$wpdb->prefix.'bmify_coupons_used',
			$wpdb->prefix.'bmify_coupons'
		) );
		
		/* since 1.x.x */
		$wpdb->query( sprintf(
			'ALTER TABLE `%s` ADD CONSTRAINT FOREIGN KEY (customer_id) REFERENCES %s(id) ON DELETE CASCADE ON UPDATE CASCADE',
			$wpdb->prefix.'bmify_coupons_used',
			$wpdb->prefix.'bmify_customers'
		) );
		
		self::dropColumnsQuery(array(
			
			/*** 	remove column from payments		***/
			/*** 	tax_id 							***/
			/*** 	since bookmify v1.0.2 						***/
			array(
				'tablename' 	=> $wpdb->prefix.'bmify_payments',
				'columnname' 	=> 'tax_id',
				'query' 		=> 'ALTER TABLE `%s` DROP COLUMN `%s`',
			),
		));
		
		
		$wpdb->query(sprintf('ALTER TABLE %s ENGINE = InnoDB', $wpdb->prefix.'bmify_customer_appointments_extras'));
		$wpdb->query(sprintf('ALTER TABLE %s ENGINE = InnoDB', $wpdb->prefix.'bmify_customer_appointments'));
		$wpdb->query(sprintf('ALTER TABLE %s ENGINE = InnoDB', $wpdb->prefix.'bmify_extra_services'));
		$wpdb->query(sprintf('ALTER TABLE %s ENGINE = InnoDB', $wpdb->prefix.'bmify_dayoff'));
		$wpdb->query(sprintf('ALTER TABLE %s ENGINE = InnoDB', $wpdb->prefix.'bmify_coupons'));
		$wpdb->query(sprintf('ALTER TABLE %s ENGINE = InnoDB', $wpdb->prefix.'bmify_coupons_used'));
		$wpdb->query(sprintf('ALTER TABLE %s ENGINE = InnoDB', $wpdb->prefix.'bmify_taxes'));
		$wpdb->query(sprintf('ALTER TABLE %s ENGINE = InnoDB', $wpdb->prefix.'bmify_payments'));
		$wpdb->query(sprintf('ALTER TABLE %s ENGINE = InnoDB', $wpdb->prefix.'bmify_employees'));
		$wpdb->query(sprintf('ALTER TABLE %s ENGINE = InnoDB', $wpdb->prefix.'bmify_categories'));
		$wpdb->query(sprintf('ALTER TABLE %s ENGINE = InnoDB', $wpdb->prefix.'bmify_services'));
		$wpdb->query(sprintf('ALTER TABLE %s ENGINE = InnoDB', $wpdb->prefix.'bmify_locations'));
		$wpdb->query(sprintf('ALTER TABLE %s ENGINE = InnoDB', $wpdb->prefix.'bmify_employee_locations'));
		$wpdb->query(sprintf('ALTER TABLE %s ENGINE = InnoDB', $wpdb->prefix.'bmify_employee_services'));
		$wpdb->query(sprintf('ALTER TABLE %s ENGINE = InnoDB', $wpdb->prefix.'bmify_customers'));
		$wpdb->query(sprintf('ALTER TABLE %s ENGINE = InnoDB', $wpdb->prefix.'bmify_appointments'));
		$wpdb->query(sprintf('ALTER TABLE %s ENGINE = InnoDB', $wpdb->prefix.'bmify_business_hours_breaks'));
		$wpdb->query(sprintf('ALTER TABLE %s ENGINE = InnoDB', $wpdb->prefix.'bmify_employee_business_hours'));
		$wpdb->query(sprintf('ALTER TABLE %s ENGINE = InnoDB', $wpdb->prefix.'bmify_employee_business_hours_breaks'));
		$wpdb->query(sprintf('ALTER TABLE %s ENGINE = InnoDB', $wpdb->prefix.'bmify_notifications'));
		$wpdb->query(sprintf('ALTER TABLE %s ENGINE = InnoDB', $wpdb->prefix.'bmify_notifications_sent'));
		$wpdb->query(sprintf('ALTER TABLE %s ENGINE = InnoDB', $wpdb->prefix.'bmify_customfields'));

		
		
		
		self::modifyColumnsQuery(array(
			/*** 	change column type in payments from INT 	***/
			/*** 	rate 										***/
			/*** 	since bookmify v1.0.2 									***/
			array(
				'tablename' 	=> $wpdb->prefix.'bmify_taxes',
				'columnname' 	=> 'rate',
				'datatype' 		=> 'DECIMAL(13,2) NOT NULL DEFAULT 0',
				'query' 		=> 'ALTER TABLE `%s` MODIFY `%s` %s',
			),
			/*** 	change column type in coupons from INT 		***/
			/*** 	rate 										***/
			/*** 	since bookmify v1.3.3 									***/
			array(
				'tablename' 	=> $wpdb->prefix.'bmify_coupons',
				'columnname' 	=> 'deduction',
				'datatype' 		=> 'DECIMAL(13,2) NOT NULL DEFAULT 0',
				'query' 		=> 'ALTER TABLE `%s` MODIFY `%s` %s',
			),
		));
		
		/*** 	add new table  	***/
		/*** 	services_taxes 	***/
		/*** 	since bookmify v1.0.2 		***/
		$query = '';
		$table_name	 		= $wpdb->prefix.'bmify_services_taxes';
		$sql = "CREATE TABLE `$table_name`(
				`id`						INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
				`tax_id`  	 				INT UNSIGNED NOT NULL,
				`service_id`     			INT UNSIGNED NOT NULL,
                UNIQUE KEY unique_ids_idx (tax_id, service_id),
                CONSTRAINT
                    FOREIGN KEY (service_id)
                    REFERENCES {$wpdb->prefix}bmify_services(id)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE,
                CONSTRAINT
                    FOREIGN KEY (tax_id)
                    REFERENCES {$wpdb->prefix}bmify_taxes(id)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE
		) {$charset_collate};";
		if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table_name}'" ) != $table_name ) { $query .= $sql; }
		if($query != ''){$wpdb->query( $query );}
		
		
		/*** 	add new table  	***/
		/*** 	shortcodes 		***/
		/*** 	since bookmify v1.0.2 		***/
		$query = '';
		$table_name	 		= $wpdb->prefix.'bmify_shortcodes';
		$sql = "CREATE TABLE `$table_name`(
				`id`						INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
				`title`  	 				VARCHAR(255) DEFAULT '',
				`shortcode`     			TEXT DEFAULT NULL
		) {$charset_collate};";
		if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table_name}'" ) != $table_name ) { $query .= $sql; }
		if($query != ''){$wpdb->query( $query );}
		
		
    }
	
	
	/**
	 * @since 1.0.0
	 * @access private
	 */
	
	public static function columnExists( $tablename , $fieldname) {
        global $wpdb;
        if ( (! empty($wpdb->prefix) ) && ( strpos($tablename, $wpdb->prefix) === false ) ) $tablename = $wpdb->prefix . $tablename ;
        $sql_check_table = "SHOW COLUMNS FROM {$tablename}" ;

        $res = $wpdb->get_results( $sql_check_table );

        foreach ($res as $fld) {
            if ($fld->Field == $fieldname) return 1;
        }

        return 0;
    }
	
	
  	/**
	 * @since 1.0.0
	 * @access private
	 */
    private static function addColumnsQuery( array $data )
    {
        global $wpdb;
		
		foreach ( $data as $item ) {
			
			$result = self::columnExists($item['tablename'], $item['columnname']);
			
			if($result == 0){
				$wpdb->query( sprintf( $item['query'], $item['tablename'], $item['columnname'] ) );
			}

		}   
    }
	
	
  	/**
	 * @since 1.0.0
	 * @access private
	 */
    private static function modifyColumnsQuery( array $data )
    {
        global $wpdb;
		
		foreach ( $data as $item ) {
			$wpdb->query( sprintf( $item['query'], $item['tablename'], $item['columnname'], $item['datatype'] ) );
		}   
    }
	
	
	/**
	 * @since 1.0.0
	 * @access private
	 */
    private static function dropColumnsQuery( array $data )
    {
        global $wpdb;
		
		foreach ( $data as $item ) {
			
			$result = self::columnExists($item['tablename'], $item['columnname']);
			
			if($result == 1){
				$wpdb->query( sprintf( $item['query'], $item['tablename'], $item['columnname'] ) );
			}

		}   
    }
	

	
    
}

