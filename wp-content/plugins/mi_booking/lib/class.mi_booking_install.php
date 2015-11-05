<?php

defined( 'ABSPATH' ) or die( '<h3>No access to the file!</h3>' );

if (!class_exists('MI_Install'))
{
    class MI_Install {
        static $booking_table_base;
        static $booking_time_base;
        static $booking_time_tamplate_base;
        static $table_of_rooms;
        static $table_of_holidays;
            
        static function install() {            
            global $wpdb;
            self::$booking_table_base           = $wpdb->prefix.'booking_table';
            self::$booking_time_base            = $wpdb->prefix.'booking_time';
            self::$booking_time_tamplate_base   = $wpdb->prefix.'booking_time_tamplate';
            self::$table_of_rooms               = $wpdb->prefix.'booking_table_of_rooms';
            self::$table_of_holidays            = $wpdb->prefix.'booking_table_of_holidays';
            
            
            $sql_table_of_holidays = "
                    CREATE TABLE IF NOT EXISTS `".self::$table_of_holidays."` (
                        `id` INT(11) NOT NULL AUTO_INCREMENT,
                        `date_of_holiday` DATE NOT NULL DEFAULT '1920-01-01',
                        `ununique` INT(1) UNSIGNED NOT NULL DEFAULT '0',
                        PRIMARY KEY (`id`)
                    )
                    ENGINE=InnoDB;";
            $sql_table_of_rooms = "
                    CREATE TABLE IF NOT EXISTS `".self::$table_of_rooms."` (
                        `id` INT(11) NOT NULL AUTO_INCREMENT,
                        `name_of_room` CHAR(150) NOT NULL DEFAULT '0',
                        `show_town` INT NULL DEFAULT '0',
                        `number_of_days` INT(11) NOT NULL DEFAULT '30',
                        PRIMARY KEY (`id`)
                    )
                    ENGINE=InnoDB
                    AUTO_INCREMENT=2;";            
            $sql_booking_table = "
                    CREATE TABLE IF NOT EXISTS `".self::$booking_table_base.'__1'."` (
                        `id` INT(11) NOT NULL AUTO_INCREMENT,
                        `name_of_customer` VARCHAR(40) NOT NULL,
                        `date_order` CHAR(50) NOT NULL,
                        `time_order` CHAR(50) NOT NULL,
                        `city_order` VARCHAR(40) NOT NULL,
                        `what_order` VARCHAR(40) NOT NULL,
                        `e_mail` VARCHAR(40) NOT NULL,
                        `phone` VARCHAR(40) NOT NULL,
                        `status_verification` VARCHAR(50) NOT NULL DEFAULT '".__('Booking not confirmed', 'mi_booking')."',
                        `verification` TINYINT(4) NULL DEFAULT '1',
                        PRIMARY KEY (`id`)
                    )
                    COLLATE='utf8_general_ci'
                    ENGINE=InnoDB
                    AUTO_INCREMENT=2;";
            $sql_booking_time = "
                    CREATE TABLE IF NOT EXISTS`".self::$booking_time_base.'__1'."` (
                        `id` INT(4) NOT NULL AUTO_INCREMENT,
                        `day` DATE NOT NULL,
                        `time` CHAR(50) NOT NULL,
                        `available` INT(11) NOT NULL DEFAULT '0',
                        PRIMARY KEY (`id`)
                    )
                    COLLATE='utf8_general_ci'
                    ENGINE=InnoDB
                    AUTO_INCREMENT=2;";
            $sql_time_tamplate = "
                    CREATE TABLE IF NOT EXISTS`".self::$booking_time_tamplate_base.'__1'."` (
                        `id` INT(11) NOT NULL AUTO_INCREMENT,
                        `what_day` CHAR(50) NOT NULL,
                        `time` TIME NOT NULL DEFAULT '12:00:00',
                        `cost` DOUBLE UNSIGNED NOT NULL DEFAULT '0',
                    PRIMARY KEY (`id`)
                    )
                    ENGINE=InnoDB
                    AUTO_INCREMENT=2;";
            
            $wpdb->query($sql_table_of_holidays);
            $wpdb->query($sql_table_of_rooms);
            $wpdb->query($sql_booking_table);
            $wpdb->query($sql_booking_time);
            $wpdb->query($sql_time_tamplate);
            
            $booking_time_tamplate_data = array(
                array(1, 'B', '17:00', '1500'),
		array(2, 'B', '18:15', '1500'),
		array(3, 'B', '19:30', '1500'),
		array(4, 'B', '20:45', '1500'),
		array(5, 'B', '22:00', '1500'),
		array(6, 'BP', '17:00', '2000'),
		array(7, 'BP', '18:15', '2000'),
		array(8, 'BP', '19:30', '2000'),
		array(9, 'BP', '20:45', '2000'),
		array(10, 'BP', '22:00', '2000'),
		array(11, 'V', '12:00', '2000'),
		array(12, 'V', '13:15', '2000'),
		array(13, 'V', '14:30', '2000'),
		array(14, 'V', '15:45', '2000'),
		array(15, 'V', '17:00', '2000'),
		array(16, 'V', '18:15', '2000'),
		array(17, 'V', '19:30', '2000'),
		array(18, 'V', '20:45', '2000'),
		array(19, 'V', '22:00', '2000'),               
            );
            foreach ($booking_time_tamplate_data as $data)
            {
                list($id,$what_day,$time,$cost) = $data;
                $wpdb->insert(
                    self::$booking_time_tamplate_base.'__1',
                    array( 
                        'id'        => $id, 
                        'what_day'  => $what_day, 
                        'time'      => $time, 
                        'cost'      => $cost,
                    ),
                    array( '%d', '%s', '%s', '%s'  )
                );
            }
            $wpdb->insert(
                    self::$table_of_rooms,
                    array( 
                        'id'                    => 1, 
                        'name_of_room'          => __('Room 1', 'mi_booking'), 
                        'name_of_order_table'   =>'booking_table__1', 
                        'name_of_time_table'    =>'booking_time__1',
                        'name_of_time_tamplate' =>'booking_time_tamplate__1',
                    ),
                    array( '%d', '%s', '%s', '%s', '%s'  )
            );
            
            add_option('mi_booking');
            $mi_booking = get_option('mi_booking');
            $mi_booking['room_selected'] = 1;
            $mi_booking['update_error']['state'] = FALSE;
            $mi_booking['update_error']['problem'] = '';
            $mi_booking['is_delete'] = true;
            $mi_booking['booking_email'] = get_option('admin_email');
            $mi_booking['subject_confirm'] = 'The booking confirmation';
            $mi_booking['subject_cancel_delete'] = 'The booking cancel';
            $mi_booking['tamplate_confirmation'] =  __("Dear [name_of_customer], our team has confirmed the booking of [what_booked] by You, on [order_date] in [order_time] o'clock. Also You indicated [order_city]phone [phone]. If any details are incorrect, please contact the Us.", 'mi_booking');
            $mi_booking['tamplate_cancel'] = __("Dear [name_of_customer], our team has cancel the booking of [what_booked] , on [order_date] in [order_time] o'clock", 'mi_booking');
            $mi_booking['tamplate_booking_conf'] = __('Your order is accepted. We will contact You within 2 hours. Thank you for your interest to our company!', 'mi_booking');
            $mi_booking['tamplate_booking_mail'] = __('Please enter the e-mail address, so We can contact You.', 'mi_booking');
            $mi_booking['tamplate_booking__tel'] = __('Please enter your phone number with country code at the beginning and 10 digits,so We can contact You.', 'mi_booking');
            update_option('mi_booking', $mi_booking);
        }
    }
}

