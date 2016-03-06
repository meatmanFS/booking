<?php

defined( 'ABSPATH' ) or die( '<h3>No access to the file!</h3>' );

if (!class_exists('MI_Booking_Settings'))
{
    class MI_Booking_Settings extends MI_Booking {
		public $room_name;
        public $disp_days;
        public $show_city;
		public $rooms;
        public function __construct() {
            parent::__construct();
			$this->rooms = $wpdb->get_results('SELECT * FROM '.$this->table_of_rooms.';' );
            $this->plugin_settings();            
        }
        public function plugin_settings(){ 
            /*------------Room settings------------*/
            register_setting( 'mi_booking_room', 'mi_booking_room', array($this, 'save_settings') );
            add_settings_section( 'mi_booking_room_id', '', '', 'mi_booking_room' ); 
            add_settings_field('mi_booking_room_change_name', __('Change room name', 'mi_booking'), array($this, 'display_room_change_name' ), 'mi_booking_room', 'mi_booking_room_id' );
            add_settings_field('mi_booking_room_change_disp_days', __('Change displayed days', 'mi_booking'), array($this, 'change_disp_days' ), 'mi_booking_room', 'mi_booking_room_id' );
            add_settings_field('mi_booking_room_change_show_city', __('Show city', 'mi_booking'), array($this, 'show_city' ), 'mi_booking_room', 'mi_booking_room_id' );
            /*-----------Time tamplate-------------*/
            register_setting('mi_booking_time_template','mi_booking_time_template', array($this, 'save_time_template'));
            add_settings_section('mi_booking_time_template_id', '', '', 'mi_booking_time_template');
            $time_tamplate_map = 
                '<br><br><div class="message-map bg-info">'.__('To mark day, use abbreviations', 'mi_booking').': <br>
                <p>'.__('Weekday', 'mi_booking').': <span class="pull-right">WD</span></p>
                <p>'.__('Friday', 'mi_booking').': <span class="pull-right">F</span></p>
                <p>'.__('Weekend', 'mi_booking').': <span class="pull-right">WE</span></p>
                <p>'.__('Holidays', 'mi_booking').': <span class="pull-right">H</span></p>';
            add_settings_field('time_table_cost',  __('Setting a time table and the cost'.$time_tamplate_map, 'mi_booking'), array($this, 'time_table_cost'), 'mi_booking_time_template', 'mi_booking_time_template_id');
            add_settings_field('holidays',  __('Setting holidays', 'mi_booking'), array($this, 'time_holiday'), 'mi_booking_time_template', 'mi_booking_time_template_id');
            /*------------Room message-------------*/
            register_setting('mi_booking_message', 'mi_booking_message', array($this, 'save_message'));
            add_settings_section('mi_booking_message_id', '', '', 'mi_booking_message');
            $customer_message_map = 
                '<br><br><div class="message-map bg-info">'.__('To add data, use', 'mi_booking').': <br>
                '.__('Name of customer', 'mi_booking').':<br> <span>-name_of_customer-</span><br>
                '.__('What is booked', 'mi_booking').':<br> <span>-what_booked-</span><br>
                '.__('Date, time', 'mi_booking').':<br><span>-order_date-</span> , <span>-order_time-</span> <br>
                '.__('City', 'mi_booking').':<br><span>-order_city-</span> <br>
                '.__('Phone', 'mi_booking').':<br><span>-phone-</span> <br>
                '.__('Use the character square brackets at the beginning and the end.', 'mi_booking').'</div>';
            add_settings_field('mi_booking_message_customer', __('Emails to customer'.$customer_message_map, 'mi_booking'), array($this, 'customer_message'), 'mi_booking_message', 'mi_booking_message_id');
            add_settings_field('success_booking', __('Successful booking message', 'mi_booking'), array($this, 'success_booking'), 'mi_booking_message', 'mi_booking_message_id');
            add_settings_field('invalid_email', __('Invalid email message', 'mi_booking'), array($this, 'invalid_email'), 'mi_booking_message', 'mi_booking_message_id');
            add_settings_field('invalid_phone', __('Invalid phone message', 'mi_booking'), array($this, 'invalid_phone'), 'mi_booking_message', 'mi_booking_message_id');
            add_settings_field('email', __('Admin Email', 'mi_booking'), array($this, 'admin_email'), 'mi_booking_message', 'mi_booking_message_id');
            add_settings_field('subject_confirm', __('Subject: Confirm', 'mi_booking'), array($this, 'subject_confirm'), 'mi_booking_message', 'mi_booking_message_id');
            add_settings_field('subject_cancel_delete', __('Subject: Cancel and Delete', 'mi_booking'), array($this, 'subject_cancel_delete'), 'mi_booking_message', 'mi_booking_message_id');
        }
        /*---------Save all settings--------*/
        public function save_settings($input) {
            global $wpdb;
            if(
                !empty($input['change_name']) 
                && (preg_match('/^(\d){1,}$/i', $input['disp_days'])) 
                && $input['disp_days'] > 0
            )
            {
                $wpdb->update(
                        $this->table_of_rooms,
                        array(
                                'name_of_room'      => $input['change_name'],
                                'number_of_days'    => $input['disp_days']
                            ),
                        array('id' => $this->room),
                        array('%s','%d'),
                        array('%d')
                    );
            }
            else
            {
                $this->mi_booking['update_error']['state'] = true;
                $this->mi_booking['update_error']['problem'][12] = '<span class="problem">'. __('Empty name of room or not valid displayed days!', 'mi_booking') .'</span>';
                $this->mi_update();
            }     
            $show_city = (isset($input['show_city']))? $input['show_city'] : false;
            if($show_city)
            {
                $wpdb->update(
                    $this->table_of_rooms,
                    array('show_town' => 1),
                    array('id' => $this->room),
                    array('%d'),
                    array('%d')    
                );
            }
            else
            {
                $wpdb->update(
                    $this->table_of_rooms,
                    array('show_town' => 0),
                    array('id' => $this->room),
                    array('%d'),
                    array('%d')    
                );
            }
            return $input;
        }
        public function save_time_template($input) {
            global $wpdb;
            foreach ($input['time_table_cost'] as $key=>$value)
            {
                $allowed_what_day = array('WD','F','WE','H');
                if(
                    (
                        !empty($value['cost']) 
                        && preg_match('/^([0-9]*\.[0-9]{1,2}|[0-9]*)$/i', $value['cost'])
                    )
                        && (!empty($value['what_day']) 
                        && in_array(strtoupper($value['what_day']), $allowed_what_day)) 
                        && (
                                preg_match('/^([0-2]){1}([0-3]){1}:([0-5]){1}([0-9]){1}:([0-5]){1}([0-9]){1}$/i', $value['time']) 
                                ||  preg_match('/^([0-2]){1}([0-3]){1}:([0-5]){1}([0-9]){1}$/i', $value['time'])
                        )
                )
                {
                    $unique_time_template = $wpdb->query("SELECT id FROM ".$this->booking_time_tamplate_base."__".$this->mi_booking['room_selected']." WHERE what_day='".strtoupper($value['what_day'])."' AND time='".$value['time']."' AND id!=$key;");
                    if(!$unique_time_template)
                    {
                        $wpdb->update(
                                $this->booking_time_tamplate_base."__".$this->mi_booking['room_selected'],
                                array(
                                        'what_day'  => strtoupper($value['what_day']),
                                        'time'      => $value['time'],
                                        'cost'      => number_format($value['cost'], 2, '.', ''),
                                    ),
                                array('id' => $key),
                                array('%s','%s','%s'),
                                array('%d')
                            );
                    }
                    else
                    {
                        $this->mi_booking['update_error']['state'] = true;
                        $this->mi_booking['update_error']['problem'][0] = '<span class="problem">'. __('Time template already exist!', 'mi_booking') .'</span>';
                        $this->mi_update();
                    } 
                }
                else
                {
                    $this->mi_booking['update_error']['state'] = true;
                    $this->mi_booking['update_error']['problem'][1] = '<span class="problem">'. __('Some not valid cost, time or description values!', 'mi_booking') .'</span>';
                    $this->mi_update();
                }
            }
            foreach ($input['time_holiday'] as $key=>$value)
            {
                $date = date('Y-m-d', strtotime($value));
                if(
                    !empty($date) 
                    && preg_match('/(19|20)\d\d-((0[1-9]|1[012])-(0[1-9]|[12]\d)|(0[13-9]|1[012])-30|(0[1-9]|1[0-2])-31)/i', $date)
                )
                {
                    $unique_time_holiday = $wpdb->query("SELECT id FROM $this->table_of_holidays WHERE date_of_holiday='$date' AND id!=$key;");
                    if(!$unique_time_holiday)
                    {
                        $show_city = (isset($input['date_every_year'][$key]))? 1 : 0;
                        $wpdb->update(
                                $this->table_of_holidays,
                                array(
                                        'date_of_holiday'  => $date,
                                        'ununique'         => $show_city,
                                    ),
                                array('id' => $key),
                                array('%s', '%d'),
                                array('%d')
                            );
                    }
                    else
                    {
                        $this->mi_booking['update_error']['state'] = true;
                        $this->mi_booking['update_error']['problem'][2] = '<span class="problem">'. __('Holidays templates already exist!', 'mi_booking') .'</span>';
                        $this->mi_update();
                    }
                }
                else
                {
                    $this->mi_booking['update_error']['state'] = true;
                    $this->mi_booking['update_error']['problem'][3] = '<span class="problem">'. __('Some not valid holidays values!', 'mi_booking') .'</span>';
                    $this->mi_update();
                }
            }
        }
        public function save_message($input) {
            $this->mi_booking['tamplate_confirmation']    = $input['confirm'];
            $this->mi_booking['tamplate_cancel']          = $input['cancel'];
            $this->mi_booking['tamplate_booking_conf']    = $input['booking_conf'];
            $this->mi_booking['tamplate_booking_mail']    = $input['booking_mail'];
            $this->mi_booking['tamplate_booking__tel']    = $input['booking_phone'];
            $this->mi_booking['booking_email']            = $input['admin_email'];
            $this->mi_booking['subject_confirm']          = $input['subject_confirm'];
            $this->mi_booking['subject_cancel_delete']    = $input['subject_cancel_delete'];
            $this->mi_update();
        }
        /*-----End save all settings--------*/
        /*-----------Room settings----------*/
		public function room_selected_name() {
            foreach ($this->rooms as $room)
            {
                if ($room->id == $this->room)
                {
                    $this->room_name = $room->name_of_room;
                    $this->disp_days = $room->number_of_days;
                    $this->show_city = $room->show_town;
                }
            }
        }
        public function display_room_change_name() {
            $this->room_selected_name();
            ?>
                <div>
                    <input class="form-control" type="text" name="mi_booking_room[change_name]" value="<?php echo $this->room_name ?>" maxlength="150" />
                </div>
            <?php
        }
        public function change_disp_days() {
            $this->room_selected_name();
            ?>
                <div>
                    <input class="form-control" type="number" name="mi_booking_room[disp_days]" value="<?php echo $this->disp_days ?>" maxlength="10" />
                </div>
            <?php
        }
        public function show_city() {
            $this->room_selected_name();
            $show_city = (isset($this->show_city)) ? $this->show_city : false;
            ?>
                <div class="iphone-checkbox">
                    <input id="show_city" class="form-control" type="checkbox" name="mi_booking_room[show_city]" value="1" <?php if($show_city) checked(1, $show_city) ?>/>
                </div>
            <?php
        }
        /*-------End room settings----------*/
        /*-------Time table settings--------*/
        public function time_table_cost() {
            global $wpdb;
            $time_tamplate = $wpdb->get_results("SELECT * FROM $this->booking_time_tamplate_base"."__".$this->room." ORDER BY time;");
            if(!empty($time_tamplate))
            {
                ?>
                    <table class="time-tamplate-table">
                        <thead>
                            <tr>
                                <td><?php echo __('Description', 'mi_booking') ?></td>
                                <td><?php echo __('Time', 'mi_booking') ?></td>
                                <td><?php echo __('Cost', 'mi_booking') ?></td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($time_tamplate as $time_row): ?>
                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <input title="<?php echo __('Format: WD or F or WE or H', 'mi_booking') ?>"  class="form-control what_day_input" type="text" name="mi_booking_time_template[time_table_cost][<?php echo $time_row->id ?>][what_day]" value="<?php echo $time_row->what_day ?>" />
                                        </div>    
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <input title="<?php echo __('Format: HH:MM', 'mi_booking') ?>"  class="form-control time_input" type="time" name="mi_booking_time_template[time_table_cost][<?php echo $time_row->id ?>][time]" value="<?php echo substr($time_row->time, 0, 5); ?>" />
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-inline">
                                            <div class="form-group">
                                                <input title="<?php echo __('Format: 99999999.99', 'mi_booking') ?>"  class="form-control cost_input" type="text" name="mi_booking_time_template[time_table_cost][<?php echo $time_row->id ?>][cost]" maxlength="11" value="<?php echo $time_row->cost ?>" />
                                            </div>    
                                            <button class="btn btn-danger" form="time_template_delete" type="submit" name="time_tamplate_delete_btn" value="<?php echo $time_row->id ?>"><?php echo __('Delete', 'mi_booking') ?> <i class="fa fa-times"></i></button>
                                        </div>    
                                    </td>
                                </tr>
                            <?php endforeach; ?>    
                        </tbody>
                    </table>
                <?php
            }
            else
            {
                ?>
                    <div class="message-notice bg-primary">
                        <h2><?php echo __('There are no time template for current room!<br>Add some, using "Add time template" button.', 'mi_booking') ?></h2>
                    </div>                    
                <?php
            }
        }
        public function time_holiday() {
            global $wpdb;
            $time_holiday = $wpdb->get_results("SELECT * FROM $this->table_of_holidays ORDER BY date_of_holiday;");
            foreach ($time_holiday as $item)
            {
                $every_year = ($item->ununique === '1') ? true : false;
                list($year, $month, $day) = explode('-', $item->date_of_holiday);
                ?>
                    <div class="form-inline time-holiday form-group">
                        
                        <input title="<?php echo __('Format: '.get_option( 'date_format' ), 'mi_booking') ?>" type="text" name="mi_booking_time_template[time_holiday][<?php echo $item->id ?>]" class="form-control" data-timeformated="<?php echo $item->date_of_holiday ?>" value="<?php echo date(get_option( 'date_format' ), mktime(0, 0, 0, $month, $day, $year))?>" />
                        <input title="<?php echo __('Unique year?', 'mi_booking') ?>" type="checkbox" class="form-control" name="mi_booking_time_template[date_every_year][<?php echo $item->id ?>]" value="1" <?php if($every_year) checked(1, $every_year) ?>>
                        <button class="btn btn-danger" type="submit" name="time_holiday_delete_btn" form="time_holiday_delete" value="<?php echo $item->id?>"><?php echo __('Delete', 'mi_booking') ?> <i class="fa fa-times"></i></button>
                    </div>
                <?php
            }
        }
        /*----End time table settings-------*/
        /*----------Message settings--------*/
        public function customer_message() {
            echo '<h4>The message template when booking confirmed.</h4>';
            wp_editor( 
                $this->mi_booking['tamplate_confirmation'],
                'mi_booking_message_confirm' , 
                array(
                    'textarea_name' => 'mi_booking_message[confirm]',
                    'textarea_rows' => 6,
                    )
            );
            echo '<br><h4>The message template when booking canceled.</h4>';
            wp_editor( 
                $this->mi_booking['tamplate_cancel'], 
                'mi_booking_message_cancel', 
                array(
                    'textarea_name' => 'mi_booking_message[cancel]',
                    'textarea_rows' => 6,
                )
            );
        }
        public function success_booking() {
            wp_editor( 
                $this->mi_booking['tamplate_booking_conf'],
                'mi_booking_message_booking_conf' , 
                array(
                    'textarea_name' => 'mi_booking_message[booking_conf]',
                    'textarea_rows' => 6,
                    )
            );
        }
        public function invalid_email() {
            wp_editor( 
                $this->mi_booking['tamplate_booking_mail'],
                'mi_booking_message_mail' , 
                array(
                    'textarea_name' => 'mi_booking_message[booking_mail]',
                    'textarea_rows' => 6,
                    )
            );
        }
        public function invalid_phone() {
            wp_editor( 
                $this->mi_booking['tamplate_booking__tel'],
                'mi_booking_message_phone' , 
                array(
                    'textarea_name' => 'mi_booking_message[booking_phone]',
                    'textarea_rows' => 6,
                    )
            );
        }
         public function admin_email() {
            ?>
                <input type="text" name="mi_booking_message[admin_email]" class="form-control" value="<?php echo $this->mi_booking['booking_email']?>" />                                        
            <?php
        }
        public function subject_confirm() {            
            ?>
                <input type="text" name="mi_booking_message[subject_confirm]" class="form-control" value="<?php echo $this->mi_booking['subject_confirm']?>" />                                        
            <?php
        }
        public function subject_cancel_delete() {
            ?>
                <input type="text" name="mi_booking_message[subject_cancel_delete]" class="form-control" value="<?php echo $this->mi_booking['subject_cancel_delete']?>" />                                        
            <?php
        }
        /*------End message settings--------*/
    }
}
























