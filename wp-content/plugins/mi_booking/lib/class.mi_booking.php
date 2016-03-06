<?php

defined( 'ABSPATH' ) or die( '<h3>No access to the file</h3>' );

if (!class_exists('MI_Booking'))
{
    class MI_Booking {
        public $booking_table_base;
        public $booking_time_base;
        public $booking_time_tamplate_base;
        public $table_of_rooms;
        public $table_of_holidays;
        public $room;
        public $rooms;
        public $mi_booking;
        public $room_name;
        public $disp_days;
        public $show_city;
		
        public function __construct() {
            global $wpdb;
            $this->booking_table_base           = $wpdb->prefix.'booking_table';
            $this->booking_time_base            = $wpdb->prefix.'booking_time';
            $this->booking_time_tamplate_base   = $wpdb->prefix.'booking_time_tamplate';
            $this->table_of_rooms               = $wpdb->prefix.'booking_table_of_rooms';
            $this->table_of_holidays            = $wpdb->prefix.'booking_table_of_holidays';
            
            $this->mi_booking = get_option('mi_booking');
            $this->room = $this->mi_booking['room_selected'];
        }
		public function init() {
			if( !is_admin() ){
				$this->front_booking();
			}
		}
        public function admin_init() {
            global $wpdb;
            /*-------------handle dashboard------------*/
            $dash_control = filter_input(INPUT_POST, 'dash_control', FILTER_SANITIZE_SPECIAL_CHARS);
            if(isset($dash_control))
            {
                if( wp_verify_nonce( $dash_control, 'dash_control' ) )
                {
                    $status_confirm = filter_input(INPUT_POST, 'status_confirm', FILTER_SANITIZE_SPECIAL_CHARS);
                    $status_cancel = filter_input(INPUT_POST, 'status_cancel', FILTER_SANITIZE_SPECIAL_CHARS);
                    $status_delete = filter_input(INPUT_POST, 'status_delete', FILTER_SANITIZE_SPECIAL_CHARS);
                    $booking_id = filter_input(INPUT_POST, 'booking_id', FILTER_SANITIZE_SPECIAL_CHARS);
                    $booking_e_mail = filter_input(INPUT_POST, 'booking_e_mail', FILTER_SANITIZE_SPECIAL_CHARS);

                    if(isset($status_confirm))
                    {
                        $confirm = $wpdb->update
						(
                            $this->booking_table_base."__".$this->room,
							array(
												'status_verification'   => __('Booking confirmed', 'mi_booking'),
												'verification'          => 2,
											),
							array('id' => $booking_id),
							array('%s', '%d'),
							array('%d')
						);
                        if($confirm)
                        {
                            $this->send_user_email(array(
                                'email'     => $booking_e_mail,
                                'subject'   => $this->mi_booking['subject_confirm'],
                                'message'   => $this->mi_booking['tamplate_confirmation']
                            ), $booking_id);
                        }
                    }
                    if(isset($status_cancel))
                    {
                        $cancel = $wpdb->update
							(
                                $this->booking_table_base."__".$this->room,
								array(
                                    'status_verification'   => __('Booking canceled', 'mi_booking'),
                                    'verification'          => 3,
                                ),
								array('id' => $booking_id),
								array('%s', '%d'),
								array('%d')
							);
                        if($cancel)
                        {
                            $this->send_user_email(array(
                                'email'     => $booking_e_mail,
                                'subject'   => $this->mi_booking['subject_cancel_delete'],
                                'message'   => $this->mi_booking['tamplate_cancel']
                            ), $booking_id);
                        }
                    }
                    if(isset($status_delete))
                    {
                        $wpdb->query("DELETE FROM ".$this->booking_table_base."__$this->room WHERE id = $booking_id");
                    }
                }
            }
            /*------------save room-------------------*/
            $add_room_save = filter_input(INPUT_POST, 'add_room_save', FILTER_SANITIZE_SPECIAL_CHARS);
            if(isset($add_room_save))
            {
                $add_room_name = filter_input(INPUT_POST, 'add_room_name', FILTER_SANITIZE_SPECIAL_CHARS);//add_room_name
                $add_room_disp_days = filter_input(INPUT_POST, 'add_room_disp_days', FILTER_SANITIZE_SPECIAL_CHARS);//add_room_disp_days
                $add_room_show_city = (isset($_REQUEST['add_room_show_city']))? 1:0; //add_room_show_city
                if(preg_match('/^(.){1,}$/i', $add_room_name) && preg_match('/^(\d){1,}$/i', $add_room_disp_days) )
                {
                    $room_saved = $wpdb->insert
                    (
                        $this->table_of_rooms,
                        array(
                            'name_of_room'      => $add_room_name,
                            'show_town'         => $add_room_show_city,
                            'number_of_days'    => $add_room_disp_days,
                        ),
                        array('%s', '%d', '%d')
                    );
                    if($room_saved)
                    {
                        $this->create_tables($wpdb->insert_id);
                        $this->room_selected_update($wpdb->insert_id);
                    }
                    else
                    {
                        $this->mi_booking['update_error']['state'] = true;
                        $this->mi_booking['update_error']['problem'][4] = '<span class="problem">'. __('Room insert error!', 'mi_booking') .'</span>';
                    }
                }
                else
                {
                    $this->mi_booking['update_error']['state'] = true;
                    $this->mi_booking['update_error']['problem'][5] = '<span class="problem">'. __('Some empty Room name or Display days values!', 'mi_booking') .'</span>';
                    $this->mi_update();
                }      
            }
            /*-------------change room-----------------*/
            $change_room = filter_input(INPUT_POST, 'change_room', FILTER_SANITIZE_SPECIAL_CHARS);//change_room
            if(isset($change_room))
            {
                $change_room_selected = filter_input(INPUT_POST, 'change_room_selected', FILTER_SANITIZE_SPECIAL_CHARS);//change_room_selected
                $this->room_selected_update($change_room_selected);
            }
            $delete_room = filter_input(INPUT_POST, 'delete_room', FILTER_SANITIZE_SPECIAL_CHARS);//delete_room
            $confirm_del = filter_input(INPUT_POST, 'confirm_del', FILTER_SANITIZE_SPECIAL_CHARS);//confirm_del
            if(isset($delete_room) || isset($confirm_del))
            {
                $change_room_selected = filter_input(INPUT_POST, 'change_room_selected', FILTER_SANITIZE_SPECIAL_CHARS);//change_room_selected
                $line_next = $this->room_next_to_it($change_room_selected);
                $delete_room_res = $wpdb->query("DELETE FROM $this->table_of_rooms WHERE id = $change_room_selected");
                if($delete_room_res)
                {
                    $this->delete_tables($change_room_selected);
                    $this->room_selected_update($line_next);
                }
            }
            /*---------------save time tamplate--------*/
            $time_tamplates_save = filter_input(INPUT_POST, 'time_tamplates_save', FILTER_SANITIZE_SPECIAL_CHARS); //time_tamplates_save
            if(isset($time_tamplates_save))
            {
                $mi_booking_what_day_input = filter_input(INPUT_POST, 'mi_booking_what_day_input', FILTER_SANITIZE_SPECIAL_CHARS);//mi_booking_what_day_input
                $mi_booking_time_input = filter_input(INPUT_POST, 'mi_booking_time_input', FILTER_SANITIZE_SPECIAL_CHARS);//mi_booking_time_input
                $mi_booking_cost_input = filter_input(INPUT_POST, 'mi_booking_cost_input', FILTER_SANITIZE_SPECIAL_CHARS);//mi_booking_cost_input
                $allowed_what_day = array('WD','F','WE','H');
                if(
                    !empty($mi_booking_cost_input) 
                    && (!empty($mi_booking_what_day_input) 
                    && in_array(strtoupper($mi_booking_what_day_input), $allowed_what_day)) 
                    && (
                            preg_match('/^([0-2]){1}([0-3]){1}:([0-5]){1}([0-9]){1}:([0-5]){1}([0-9]){1}$/i', $mi_booking_time_input) 
                            ||  preg_match('/^([0-2]){1}([0-3]){1}:([0-5]){1}([0-9]){1}$/i', $mi_booking_time_input)
                    )
                )
                {
                    $unique_time_template = $wpdb->query("SELECT id FROM ".$this->booking_time_tamplate_base."__".$this->mi_booking['room_selected']." WHERE what_day='".strtoupper($mi_booking_what_day_input)."' AND time='$mi_booking_time_input';");
                    if(!$unique_time_template)
                    {
                        $tt_insert_stat = $wpdb->insert(
                            $this->booking_time_tamplate_base."__".$this->mi_booking['room_selected'],
                            array(
                                'what_day'      => strtoupper($mi_booking_what_day_input),
                                'time'          => $mi_booking_time_input,
                                'cost'          => $mi_booking_cost_input,
                            ),
                            array('%s', '%s', '%d')
                        );
                        if(!$tt_insert_stat)
                        {
                            $this->mi_booking['update_error']['state'] = true;
                            $this->mi_booking['update_error']['problem'][6] = '<span class="problem">'. __('Insert error!', 'mi_booking') .'</span>';
                        }
                    }
                    else
                    {
                        $this->mi_booking['update_error']['state'] = true;
                        $this->mi_booking['update_error']['problem'][7] = '<span class="problem">'. __('Time template already exist!', 'mi_booking') .'</span>';
                        $this->mi_update();
                    } 
                }
                else
                {
                    $this->mi_booking['update_error']['state'] = true;
                    $this->mi_booking['update_error']['problem'][8] = '<span class="problem">'. __('Some not valid cost, time or description values!', 'mi_booking') .'</span>';
                    $this->mi_update();
                }  
            }
            /*------------save date tamplate----------*/
            $date_tamplates_save = filter_input(INPUT_POST, 'date_tamplates_save', FILTER_SANITIZE_SPECIAL_CHARS); //date_tamplates_save
            if(isset($date_tamplates_save))
            {
                $mi_booking_date_input = date('Y-m-d', strtotime(filter_input(INPUT_POST, 'mi_booking_date_input', FILTER_SANITIZE_SPECIAL_CHARS)));;//mi_booking_date_input
                $date_every_year = (isset($_REQUEST['date_every_year']))? 1:0; //date_every_year
                if(preg_match('/^(19|20)\d\d-((0[1-9]|1[012])-(0[1-9]|[12]\d)|(0[13-9]|1[012])-30|(0[1-9]|1[0-2])-31)$/i', $mi_booking_date_input))
                {
                    $unique_time_holiday = $wpdb->query("SELECT id FROM $this->table_of_holidays WHERE date_of_holiday='$mi_booking_date_input';");
                    if(!$unique_time_holiday)
                    {
                        $tt_insert_stat = $wpdb->insert(
                            $this->table_of_holidays,
                            array(
                                'date_of_holiday'   => $mi_booking_date_input,
                                'ununique'          => $date_every_year,
                            ),
                            array('%s', '%d')
                        );
                        if(!$tt_insert_stat)
                        {
                            $this->mi_booking['update_error']['state'] = true;
                            $this->mi_booking['update_error']['problem'][9] = '<span class="problem">'. __('Holiday template insert error!', 'mi_booking') .'</span>';
                        }
                    }
                    else
                    {
                        $this->mi_booking['update_error']['state'] = true;
                        $this->mi_booking['update_error']['problem'][10] = '<span class="problem">'. __('Holiday template already exist!', 'mi_booking') .'</span>';
                        $this->mi_update();
                    }                    
                }
                else
                {
                    $this->mi_booking['update_error']['state'] = true;
                    $this->mi_booking['update_error']['problem'][11] = '<span class="problem">'. __('Not valid date value!', 'mi_booking') .'</span>';
                    $this->mi_update();
                }  
            }
            /*-------------delete time row-------------*/
            $delete_time_table_row = filter_input(INPUT_POST, 'time_tamplate_delete_btn', FILTER_SANITIZE_SPECIAL_CHARS);//time_tamplate_delete_btn
            if(isset($delete_time_table_row))
            {
                $this->mi_booking['delete_status'] = $wpdb->query("DELETE FROM ".$this->booking_time_tamplate_base."__$this->room WHERE id = $delete_time_table_row");
                $this->mi_booking['is_delete'] = true;
                $this->mi_update();
            }
            /*---------------delete holiday------------*/
            $time_holiday_delete_btn = filter_input(INPUT_POST, 'time_holiday_delete_btn', FILTER_SANITIZE_SPECIAL_CHARS);//time_holiday_delete_btn
            if(isset($time_holiday_delete_btn))
            {
                $this->mi_booking['delete_status'] = $wpdb->query("DELETE FROM $this->table_of_holidays WHERE id = $time_holiday_delete_btn");
                $this->mi_booking['is_delete'] = true;
                $this->mi_update();
            }
        }
        public function send_user_email( $set, $id = false ) {
			
			if( $id !== false ){
				$set['message'] = $this->parse_message( $set['message'], $id );
			}
			
            $headers = 
            'From: '.$this->mi_booking['booking_email']."\r\n".
			'Reply-To: '.$this->mi_booking['booking_email'].'' . "\r\n" .
			'X-Mailer: PHP/' . phpversion();		
            $wp_mail = wp_mail($set['email'], $set['subject'], $set['message'], $headers);            
            if(!$wp_mail)
            {
                $mail = mail($set['email'], $set['subject'], $set['message'], $headers);
                return $mail;
            }
            return $wp_mail;
        }
		public function parse_message( $message, $id ) {
			$message_parts = explode('-', $message );
			$data = $this->get_single_data( $this->booking_table_base . '__' . $this->room , $id );
			$parsed_message = '';
			foreach ( $message_parts as $item ){
				switch ( $item )
				{
					case 'name_of_customer': $parsed_message .= $data->name_of_customer; break;
					case 'what_booked': $parsed_message .= $data->what_order; break;
					case 'order_date': $parsed_message .= $data->date_order; break;
					case 'order_time':  $parsed_message .= $data->time_order; break;
					case 'order_city':
						$rooms = $this->get_single_data( $this->table_of_rooms , $this->room );
						if ( $rooms->disp_days == "1" ){
							$parsed_message .= $data->city_order;
						}
					break;
					case 'phone': $parsed_message .= $item->phone; break;

					default: $parsed_message .= $item;
				}
			}
			return $parsed_message;
		}
		public function get_single_data( $from, $where ) {
			global $wpdb;
			$data = $wpdb->get_results("SELECT * FROM $from WHERE id = $where ");
			return array_shift( $data ); 
		}
		
        public function room_selected_update($ID) {
			$this->room = $ID;
			$this->room_selected_name();
            $this->mi_booking['room_selected'] = $ID;
            $this->mi_update();
        }
        public function mi_update() {
            update_option('mi_booking', $this->mi_booking);
        }
        public function room_next_to_it($room_id) {
            global $wpdb;
            $get_next = $wpdb->get_results(
                    "SELECT id
                    FROM $this->table_of_rooms
                    WHERE `id` > $room_id
                    ORDER BY `id` ASC
                    LIMIT 1"
            );
            if(!$get_next)
            {
                $get_prev = $wpdb->get_results(
                    "SELECT id
                    FROM $this->table_of_rooms
                    WHERE `id` < $room_id
                    ORDER BY `id` DESC
                    LIMIT 1" 
                );
                return $get_prev[0]->id;
            }
            return $get_next[0]->id;
        }
        public function create_tables($room_id) {
            global $wpdb;
            $sql_booking_table = "
                    CREATE TABLE IF NOT EXISTS `".$this->booking_table_base.'__'.$room_id."` (
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
                    CREATE TABLE IF NOT EXISTS`".$this->booking_time_base.'__'.$room_id."` (
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
                    CREATE TABLE IF NOT EXISTS`".$this->booking_time_tamplate_base.'__'.$room_id."` (
                        `id` INT(11) NOT NULL AUTO_INCREMENT,
                        `what_day` CHAR(50) NOT NULL,
                        `time` CHAR(50) NOT NULL DEFAULT '0',
                        `cost`  CHAR(50) NOT NULL DEFAULT '0',
                    PRIMARY KEY (`id`)
                    )
                    ENGINE=InnoDB
                    AUTO_INCREMENT=2;";
            
            $wpdb->query($sql_booking_table);
            $wpdb->query($sql_booking_time);
            $wpdb->query($sql_time_tamplate);
        }
        public function delete_tables($room_id) {
            global $wpdb;
            $wpdb->query("DROP TABLE IF EXISTS `".$this->booking_table_base.'__'.$room_id."`;");
            $wpdb->query("DROP TABLE IF EXISTS `".$this->booking_time_base.'__'.$room_id."`;");
            $wpdb->query("DROP TABLE IF EXISTS `".$this->booking_time_tamplate_base.'__'.$room_id."`;");
        }
		public function front_booking() {
			include_once('class.mi_booking_front.php');
            new MI_Booking_Front();
		}
        public function textdomain() {
            load_plugin_textdomain( 'mi_booking', false, MI_Booking_DIR.'/lang/' ); 
        }
        public function admin_menu() {
            include_once('class.mi_booking_menu.php');
            new MI_Booking_Menu();
        }
        public function plugin_settings() {            
            include_once('class.mi_booking_settings.php');         
            new MI_Booking_Settings();
        }
        static function install(){
            include_once('class.mi_booking_install.php');
            MI_Install::install();            
        }
        public function uninstall(){
            include_once('class.mi_booking_uninstall.php');
            MI_Uninstall::uninstall();
        }
    }
}
