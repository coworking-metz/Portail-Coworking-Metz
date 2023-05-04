<?php
namespace Bookmify;



// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {exit; }


/**
 * Class DatabaseTables
 */
class DatabaseTables
{
	
	/**
     * Add Tables in Database
	 * @since 1.0.0
     */
	public static function add_tables()
	{
		// Do not show errors
		//$wpdb->hide_errors();
		//error_reporting( 0 );
		//ini_set( 'display_errors', 0 );
		
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' ); // need this file for dbDelta() function to work
		$bookmify_db_version = '1.0'; // This will help in the future if the database needs to be modified during an update.
		
		if ( $schema = self::_get_schema() ) {
			dbDelta( $schema );
			add_option( 'bookmify_db_version', $bookmify_db_version );
		}
	}
	
	
	/**
     * Construct Schema
	 * @since 1.0.0
     */
    public static function _get_schema()
    {
        
		global $wpdb;
		
		$charset_collate = 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_general_ci';
		$query  = '';
		
		// EMPLOYEES
		$table_name = $wpdb->prefix.'bmify_employees';
		$sql = "CREATE TABLE `$table_name`(
				`id`						INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `wp_user_id`     			BIGINT(20) UNSIGNED DEFAULT NULL,
                `attachment_id`  			INT UNSIGNED DEFAULT NULL,
               	`first_name`      	    	VARCHAR(255) DEFAULT NULL,
               	`last_name`      	    	VARCHAR(255) DEFAULT NULL,
                `email`          			VARCHAR(255) DEFAULT NULL,
                `phone`          			VARCHAR(255) DEFAULT NULL,
                `info`           			TEXT DEFAULT NULL,
                `visibility`     			ENUM('public','private') NOT NULL DEFAULT 'public',
                `position`       			INT NOT NULL DEFAULT 9999,
                `google_data`    			TEXT DEFAULT NULL
		) {$charset_collate};";
		
		if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table_name}'" ) != $table_name ) { $query .= $sql; }
		
		
		// CATEGORIES
		$table_name = $wpdb->prefix.'bmify_categories';
		$sql = "CREATE TABLE `$table_name`(
				`id`						INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `title`    					VARCHAR(255) DEFAULT NULL,
                `attachment_id`  			INT UNSIGNED DEFAULT NULL,
                `color`   					VARCHAR(255) DEFAULT NULL,
                `icon`          			VARCHAR(255) DEFAULT NULL,
                `position`       			INT NOT NULL DEFAULT 9999
		) {$charset_collate};";
		
		if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table_name}'" ) != $table_name ) { $query .= $sql; }
		
		
		
		// SERVICES
		$table_name = $wpdb->prefix.'bmify_services';
		$sql = "CREATE TABLE `$table_name`(
				`id`						INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
				`category_id`         		INT UNSIGNED DEFAULT NULL,
				`title`               		VARCHAR(255) DEFAULT '',
				`price`               		DECIMAL(10,2) NOT NULL DEFAULT 0.00,
				`duration`            		INT NOT NULL DEFAULT 900,
				`buffer_before`        		INT NOT NULL DEFAULT 0,
				`buffer_after`       		INT NOT NULL DEFAULT 0,
				`capacity_min`        		INT NOT NULL DEFAULT 1,
				`capacity_max`        		INT NOT NULL DEFAULT 1,
				`info`                		TEXT DEFAULT NULL,
				`limit_period`           	ENUM('disabled', 'per_day','per_week','per_month','per_year') NOT NULL DEFAULT 'disabled',
				`appointments_limit`  		INT DEFAULT NULL,
				`recurrence_enabled`     	TINYINT(1) NOT NULL DEFAULT 1,
				`recurrence_frequencies` 	SET('daily','weekly','biweekly','monthly') NOT NULL DEFAULT 'daily,weekly,biweekly,monthly',
				`visibility`             	ENUM('public','private') NOT NULL DEFAULT 'public',
				`attachment_id`  			INT UNSIGNED DEFAULT NULL,
				`color`               		VARCHAR(255) NOT NULL DEFAULT '#1e73be',
				`position`               	INT NOT NULL DEFAULT 9999,
				`gallery_ids`          		TEXT DEFAULT NULL,
				CONSTRAINT
					FOREIGN KEY (category_id)
					REFERENCES {$wpdb->prefix}bmify_categories(id)
					ON DELETE SET NULL
					ON UPDATE CASCADE
		) {$charset_collate};";
		
		if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table_name}'" ) != $table_name ) { $query .= $sql; }
		
		
		// LOCATIONS
		$table_name = $wpdb->prefix.'bmify_locations';
		$sql = "CREATE TABLE `$table_name`(
				`id`						INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
				`title`    		           	VARCHAR(255) DEFAULT '',
				`address`     			   	VARCHAR(255) DEFAULT '',
				`info`  		        	TEXT DEFAULT NULL,
				`attachment_id`  		   	INT UNSIGNED DEFAULT NULL
		) {$charset_collate};";
		
		if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table_name}'" ) != $table_name ) { $query .= $sql; }
		
		
		
		// EMPLOYEE LOCATIONS
		$table_name = $wpdb->prefix.'bmify_employee_locations';
		$sql = "CREATE TABLE `$table_name`(
				`id`						INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
				`employee_id`     			INT UNSIGNED NOT NULL,
                `location_id`  				INT UNSIGNED NOT NULL,
				UNIQUE KEY unique_ids_idx (employee_id, location_id),
				CONSTRAINT
                    FOREIGN KEY (employee_id)
                    REFERENCES {$wpdb->prefix}bmify_employees(id)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE,
                CONSTRAINT
                    FOREIGN KEY (location_id)
                    REFERENCES {$wpdb->prefix}bmify_locations(id)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE
		) {$charset_collate};";
		
		if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table_name}'" ) != $table_name ) { $query .= $sql; }
		
		
		// EMPLOYEES SERVICES
		$table_name = $wpdb->prefix.'bmify_employee_services';
		$sql = "CREATE TABLE `$table_name`(
				`id`						INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
				`service_id`   				INT UNSIGNED NOT NULL,
				`employee_id`     			INT UNSIGNED NOT NULL,
                `location_id`  				INT UNSIGNED DEFAULT NULL,
				`price`        				DECIMAL(10,2) NOT NULL DEFAULT 0.00,
				`deposit`      				VARCHAR(100) NOT NULL DEFAULT '100%',
                `capacity_min` 				INT NOT NULL DEFAULT 1,
                `capacity_max` 				INT NOT NULL DEFAULT 1,
                UNIQUE KEY unique_ids_idx (service_id, employee_id, location_id),
                CONSTRAINT
                    FOREIGN KEY (service_id)
                    REFERENCES {$wpdb->prefix}bmify_services(id)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE,
                CONSTRAINT
                    FOREIGN KEY (employee_id)
                    REFERENCES {$wpdb->prefix}bmify_employees(id)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE,
                CONSTRAINT
                    FOREIGN KEY (location_id)
                    REFERENCES {$wpdb->prefix}bmify_locations(id)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE
		) {$charset_collate};";
		
		if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table_name}'" ) != $table_name ) { $query .= $sql; }
		

		
		// DAY OFF
		$table_name = $wpdb->prefix.'bmify_dayoff';
		$sql = "CREATE TABLE `$table_name`(
				`id`						INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
				`employee_id`         		INT UNSIGNED DEFAULT NULL,
				`parent_id`         		INT UNSIGNED DEFAULT NULL,
                `title`   					VARCHAR(255) NOT NULL,
                `date`   					DATE NOT NULL,
                `every_year`          		TINYINT(1) NOT NULL DEFAULT 0,
				CONSTRAINT
					FOREIGN KEY (employee_id)
					REFERENCES {$wpdb->prefix}bmify_employees(id)
					ON DELETE CASCADE
					ON UPDATE CASCADE
		) {$charset_collate};";
		
		if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table_name}'" ) != $table_name ) { $query .= $sql; }
		
		
		// CUSTOMERS
		$table_name = $wpdb->prefix.'bmify_customers';
		$sql = "CREATE TABLE `$table_name`(
				`id`						INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `wp_user_id`     			BIGINT(20) UNSIGNED DEFAULT NULL,
               	`first_name`      	    	VARCHAR(255) DEFAULT NULL,
               	`last_name`      	    	VARCHAR(255) DEFAULT NULL,
                `email`          			VARCHAR(255) DEFAULT NULL,
                `phone`          			VARCHAR(255) DEFAULT NULL,
                `gender`          			ENUM('male','female') DEFAULT NULL,
                `birthday` 					DATE DEFAULT NULL,
                `country`          			VARCHAR(255) DEFAULT NULL,
                `state`          			VARCHAR(255) DEFAULT NULL,
                `city`          			VARCHAR(255) DEFAULT NULL,
                `address`          			VARCHAR(255) DEFAULT NULL,
				`post_code`         		INT UNSIGNED,
				`info`                		TEXT DEFAULT NULL,
                `registration_date`			DATETIME DEFAULT NULL
		) {$charset_collate};";
		
		if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table_name}'" ) != $table_name ) { $query .= $sql; }
		
		// SERVICES TAXES
		$table_name = $wpdb->prefix.'bmify_services_taxes';
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
		
		// TAXES
		$table_name = $wpdb->prefix.'bmify_taxes';
		$sql = "CREATE TABLE `$table_name`(
				`id`						INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
				`rate`      		   		DECIMAL(13,3) NOT NULL DEFAULT 0,
                `title`    					VARCHAR(255) DEFAULT NULL
		) {$charset_collate};";
		
		if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table_name}'" ) != $table_name ) { $query .= $sql; }
		
		
		// COUPONS
		$table_name = $wpdb->prefix.'bmify_coupons';
		$sql = "CREATE TABLE `$table_name`(
				`id`						INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `title`    					VARCHAR(255) DEFAULT NULL,
                `code`    					VARCHAR(255) DEFAULT NULL,
				`discount`     		   		INT UNSIGNED DEFAULT NULL,
				`deduction`    		   		INT UNSIGNED DEFAULT NULL,
				`usage_limit`    	   		INT UNSIGNED DEFAULT NULL,
				`per_customer_limit`  		INT DEFAULT NULL,
                `date_limit_start`			DATETIME DEFAULT NULL,
                `date_limit_end`			DATETIME DEFAULT NULL,
				`info`                		TEXT DEFAULT NULL,
				`status`		        	ENUM('active', 'overdue') NOT NULL DEFAULT 'active'
		) {$charset_collate};";
		
		if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table_name}'" ) != $table_name ) { $query .= $sql; }
		
		
		// USAGE COUPONS
		$table_name = $wpdb->prefix.'bmify_coupons_used';
		$sql = "CREATE TABLE `$table_name`(
				`id`						INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
				`coupon_id`     		   	INT UNSIGNED DEFAULT NULL,
				`customer_id`     		   	INT UNSIGNED DEFAULT NULL
		) {$charset_collate};";
		
		if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table_name}'" ) != $table_name ) { $query .= $sql; }
		
		
		
		// PAYMENTS
		$table_name = $wpdb->prefix.'bmify_payments';
		$sql = "CREATE TABLE `$table_name`(
				`id`						INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
				`coupon_id`         		INT UNSIGNED DEFAULT NULL,
				`paid_type` 	        	ENUM('local','paypal','stripe') NOT NULL DEFAULT 'local',
				`total_price`         		DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                `paid`						INT UNSIGNED DEFAULT NULL,
                `tax_ids`					TEXT DEFAULT NULL,
                `created_date`				DATETIME DEFAULT NULL,
				`status` 	     		   	ENUM('not','partly','full') NOT NULL DEFAULT 'not',
                CONSTRAINT
                    FOREIGN KEY (coupon_id)
                    REFERENCES {$wpdb->prefix}bmify_coupons(id)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE
		) {$charset_collate};";
		
		if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table_name}'" ) != $table_name ) { $query .= $sql; }
		
		
		
		
		// APPOINTMENTS
		$table_name = $wpdb->prefix.'bmify_appointments';
		$sql = "CREATE TABLE `$table_name`(
				`id`						INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
				`service_id`         		INT UNSIGNED DEFAULT NULL,
				`employee_id`         		INT UNSIGNED DEFAULT NULL,
				`location_id`         		INT UNSIGNED DEFAULT NULL,
				`status`     	        	ENUM('approved', 'pending', 'canceled', 'rejected') NOT NULL DEFAULT 'pending',
                `start_date`				DATETIME DEFAULT NULL,
                `end_date`					DATETIME DEFAULT NULL,
                `info`           			TEXT DEFAULT NULL,
                `google_calendar_event_id`  VARCHAR(255) DEFAULT NULL,
				`created_from` 	        	ENUM('bookmify', 'google') NOT NULL DEFAULT 'bookmify',
                UNIQUE KEY unique_ids_idx (service_id, employee_id, location_id),
                CONSTRAINT
                    FOREIGN KEY (service_id)
                    REFERENCES {$wpdb->prefix}bmify_services(id)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE,
                CONSTRAINT
                    FOREIGN KEY (employee_id)
                    REFERENCES {$wpdb->prefix}bmify_employees(id)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE,
				CONSTRAINT
                    FOREIGN KEY (location_id)
                    REFERENCES {$wpdb->prefix}bmify_locations(id)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE
				
		) {$charset_collate};";
		
		if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table_name}'" ) != $table_name ) { $query .= $sql; }
		
		
		
		// CUSTOMER APPOINTMENTS
		$table_name = $wpdb->prefix.'bmify_customer_appointments';
		$sql = "CREATE TABLE `$table_name`(
				`id`						INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
				`customer_id`         		INT UNSIGNED DEFAULT NULL,
				`appointment_id`         	INT UNSIGNED DEFAULT NULL,
				`payment_id`         		INT UNSIGNED DEFAULT NULL,
				`number_of_people`     		INT UNSIGNED DEFAULT NULL,
				`price`         			DECIMAL(10,2) NOT NULL DEFAULT 0.00,
				`status`     	        	ENUM('approved', 'pending', 'canceled', 'rejected') NOT NULL DEFAULT 'pending',
				`status_changed_at`			DATETIME DEFAULT NULL,
				`created_from` 	        	ENUM('backend', 'frontend') NOT NULL DEFAULT 'frontend',
				`created_date`				DATETIME DEFAULT NULL,
				`cf`   						TEXT DEFAULT NULL,
				`time_zone`					VARCHAR(255) DEFAULT NULL,
				`time_zone_offset`			INT DEFAULT NULL,
				`rating`					INT UNSIGNED DEFAULT NULL,
				`rating_text`				TEXT DEFAULT NULL,
				`locale`					VARCHAR(9) DEFAULT NULL,
                UNIQUE KEY unique_ids_idx (customer_id, appointment_id, payment_id),
                CONSTRAINT
                    FOREIGN KEY (customer_id)
                    REFERENCES {$wpdb->prefix}bmify_customers(id)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE,
                CONSTRAINT
                    FOREIGN KEY (appointment_id)
                    REFERENCES {$wpdb->prefix}bmify_appointments(id)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE,
				CONSTRAINT
                    FOREIGN KEY (payment_id)
                    REFERENCES {$wpdb->prefix}bmify_payments(id)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE
				
		) {$charset_collate};";
		
		if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table_name}'" ) != $table_name ) { $query .= $sql; }

		
		
		// EXTRA SERVICES
		$table_name = $wpdb->prefix.'bmify_extra_services';
		$sql = "CREATE TABLE `$table_name`(
				`id`						INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
				`service_id`         		INT UNSIGNED DEFAULT NULL,
                `title`   					VARCHAR(255) NOT NULL,
				`price`         			DECIMAL(10,2) NOT NULL DEFAULT 0.00,
				`duration`            		INT NOT NULL DEFAULT 0,
                `attachment_id`  			INT UNSIGNED DEFAULT NULL,
                `capacity_max` 				INT NOT NULL DEFAULT 1,
                `info`           			TEXT DEFAULT NULL,
                `position`       			INT NOT NULL DEFAULT 9999,
                CONSTRAINT
                    FOREIGN KEY (service_id)
                    REFERENCES {$wpdb->prefix}bmify_services(id)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE
		) {$charset_collate};";
		
		if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table_name}'" ) != $table_name ) { $query .= $sql; }
		
		// CUSTOMER APPOINTMENTS TO EXTRAS
		$table_name = $wpdb->prefix.'bmify_customer_appointments_extras';
		$sql = "CREATE TABLE `$table_name`(
				`id`						INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
				`customer_appointment_id`   INT UNSIGNED DEFAULT NULL,
				`extra_id`     		    	INT UNSIGNED DEFAULT NULL,
				`quantity`      	   		INT UNSIGNED DEFAULT NULL,
				`price`  			   		DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                UNIQUE KEY unique_ids_idx (customer_appointment_id, extra_id),
                CONSTRAINT
                    FOREIGN KEY (customer_appointment_id)
                    REFERENCES {$wpdb->prefix}bmify_customer_appointments(id)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE,
                CONSTRAINT
                    FOREIGN KEY (extra_id)
                    REFERENCES {$wpdb->prefix}bmify_extra_services(id)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE
				
		) {$charset_collate};";
		
		if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table_name}'" ) != $table_name ) { $query .= $sql; }
		
		
		// BUSINESS HOURS BREAKS
		$table_name = $wpdb->prefix.'bmify_business_hours_breaks';
		$sql = "CREATE TABLE `$table_name`(
				`id`						INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `start_time`				TIME DEFAULT NULL,
                `end_time`					TIME DEFAULT NULL,
                `day_index`          		TINYINT(7) NOT NULL DEFAULT 1
		) {$charset_collate};";
		
		if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table_name}'" ) != $table_name ) { $query .= $sql; }
		
		
		// EMPLOYEE BUSINESS HOURS
		$table_name = $wpdb->prefix.'bmify_employee_business_hours';
		$sql = "CREATE TABLE `$table_name`(
				`id`						INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
				`employee_id`         		INT UNSIGNED DEFAULT NULL,
                `day_index`          		TINYINT(7) NOT NULL DEFAULT 1,
                `start_time`				TIME DEFAULT NULL,
                `end_time`					TIME DEFAULT NULL,
                CONSTRAINT
                    FOREIGN KEY (employee_id)
                    REFERENCES {$wpdb->prefix}bmify_employees(id)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE
		) {$charset_collate};";
		
		if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table_name}'" ) != $table_name ) { $query .= $sql; }
		
		
		// EMPLOYEE BUSINESS HOURS BREAKS
		$table_name = $wpdb->prefix.'bmify_employee_business_hours_breaks';
		$sql = "CREATE TABLE `$table_name`(
				`id`						INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
				`employee_id`         		INT UNSIGNED DEFAULT NULL,
				`parent_id`         		INT UNSIGNED DEFAULT NULL,
                `day_index`          		TINYINT(7) NOT NULL DEFAULT 1,
                `start_time`				TIME DEFAULT NULL,
                `end_time`					TIME DEFAULT NULL,
                CONSTRAINT
                    FOREIGN KEY (employee_id)
                    REFERENCES {$wpdb->prefix}bmify_employees(id)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE
		) {$charset_collate};";
		
		if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table_name}'" ) != $table_name ) { $query .= $sql; }
		
		
		
		
		
		// NOTIFICATIONS
		$table_name = $wpdb->prefix.'bmify_notifications';
		$sql = "CREATE TABLE `$table_name`(
				`id`						INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
				`platform` 			       	ENUM('email', 'sms') NOT NULL DEFAULT 'email',
				`status` 			       	TINYINT(1) NOT NULL DEFAULT 0,
				`type`           			VARCHAR(255) NOT NULL DEFAULT '',
				`subject`               	VARCHAR(255) DEFAULT '',
                `message`           		TEXT DEFAULT NULL,
                `to_employee`          		TINYINT(1) NOT NULL DEFAULT 0,
                `to_customer`          		TINYINT(1) NOT NULL DEFAULT 0,
                `to_admin`          		TINYINT(1) NOT NULL DEFAULT 0,
                `cron`          			TINYINT(1) NOT NULL DEFAULT 0,
                `custom`	       			TINYINT(1) NOT NULL DEFAULT 0,
                `ics`          				TINYINT(1) NOT NULL DEFAULT 0,
                `invoice`          			TINYINT(1) NOT NULL DEFAULT 0,
                `time_interval`    			INT NOT NULL DEFAULT 0,
                `check_time`				TIME DEFAULT NULL,
				`options`          		 	TEXT DEFAULT NULL
		) {$charset_collate};";
		
		if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table_name}'" ) != $table_name ) { $query .= $sql; }
		
		
		// SENT NOTIFICATIONS
		$table_name = $wpdb->prefix.'bmify_notifications_sent';
		$sql = "CREATE TABLE `$table_name`(
				`id`						INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
				`notification_id`  		   	INT UNSIGNED DEFAULT NULL,
				`appointment_id`  		   	INT UNSIGNED DEFAULT NULL,
				`customer_id`  			   	INT UNSIGNED DEFAULT NULL,
				`employee_id`  			   	INT UNSIGNED DEFAULT NULL,
                `sent_date`					DATETIME NOT NULL,
				UNIQUE KEY unique_ids_idx (notification_id, appointment_id, customer_id, employee_id),
                CONSTRAINT
                    FOREIGN KEY (notification_id)
                    REFERENCES {$wpdb->prefix}bmify_notifications(id)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE,
                CONSTRAINT
                    FOREIGN KEY (appointment_id)
                    REFERENCES {$wpdb->prefix}bmify_appointments(id)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE,
                CONSTRAINT
                    FOREIGN KEY (customer_id)
                    REFERENCES {$wpdb->prefix}bmify_customers(id)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE,
                CONSTRAINT
                    FOREIGN KEY (employee_id)
                    REFERENCES {$wpdb->prefix}bmify_employees(id)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE
		) {$charset_collate};";
		
		if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table_name}'" ) != $table_name ) { $query .= $sql; }
		
		
		// CUSTOM FIELDS
		$table_name = $wpdb->prefix.'bmify_customfields';
        $sql = "CREATE TABLE `$table_name`(
				`id`						INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
				`cf_label`     			   	VARCHAR(255) DEFAULT '',
				`cf_key`     			   	VARCHAR(255) DEFAULT '',
				`cf_type`     			   	VARCHAR(255) DEFAULT '',
				`cf_required`          		TINYINT(1) NOT NULL DEFAULT 0,
				`cf_value`          		TEXT DEFAULT NULL,
				`services_ids`          	TEXT DEFAULT NULL,
                `created_at`				DATETIME DEFAULT NULL,
                `updated_at`				DATETIME DEFAULT NULL,
				`position`       			INT NOT NULL DEFAULT 999
		) {$charset_collate};";
		
		if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table_name}'" ) != $table_name ) { $query .= $sql; }
		
		// RETURN ALL QUERIES
		return $query;

    }

	
}

