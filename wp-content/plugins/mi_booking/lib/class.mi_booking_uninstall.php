<?php

defined( 'ABSPATH' ) or die( '<h3>No access to the file!</h3>' );

if (!class_exists('MI_Uninstall'))
{
    class MI_Uninstall {
        static $table_of_rooms;
        static $table_of_holidays;
        
        static function uninstall(){
            global $wpdb;
            self::$table_of_rooms               = $wpdb->prefix.'booking_table_of_rooms';
            self::$table_of_holidays            = $wpdb->prefix.'booking_table_of_holidays';

            $booking_table_of_rooms_res_string = $wpdb->get_results('SELECT id FROM '.self::$table_of_rooms.';');

            foreach ($booking_table_of_rooms_res_string as $items_res_string)
            {
                $sql_drop1 = "DROP TABLE IF EXISTS `".$wpdb->prefix."booking_table__".$items_res_string->id."`;";
                $wpdb->query($sql_drop1);
                $sql_drop2 = "DROP TABLE IF EXISTS `".$wpdb->prefix."booking_time_tamplate__".$items_res_string->id."`;";
                $wpdb->query($sql_drop2);
                $sql_drop3 = "DROP TABLE IF EXISTS `".$wpdb->prefix."booking_time__".$items_res_string->id."`;";
                $wpdb->query($sql_drop3);
            }	

            $sql1 = "DROP TABLE IF EXISTS `".self::$table_of_rooms."`;";
            $sql2 = "DROP TABLE IF EXISTS `".self::$table_of_holidays."`;";

            $wpdb->query($sql1);
            $wpdb->query($sql2);
            
            delete_option('mi_booking');
            delete_option('mi_booking_room');            
        }
    }
}
