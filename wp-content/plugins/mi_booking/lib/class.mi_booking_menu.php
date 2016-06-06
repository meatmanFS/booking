<?php

defined( 'ABSPATH' ) or die( '<h3>No access to the file!</h3>' );

if (!class_exists('MI_Booking_Menu'))
{
    class MI_Booking_Menu extends MI_Booking {        
        public function __construct() {
            parent::__construct();
			global $wpdb;
			$this->rooms = $wpdb->get_results('SELECT * FROM '.$this->table_of_rooms.';' );
            $this->admin_menu();
        }
        public function admin_menu() {
            //Dashboard
            $MI_Booking_dashboard = add_menu_page(__('Dashboard', 'mi_booking'), __('Booking', 'mi_booking'), 'install_plugins', 'mi_booking_dashboard', array($this, 'dashboard'), 'dashicons-book-alt');
            add_action( 'admin_print_scripts-' . $MI_Booking_dashboard, array($this, 'add_plugin_script') );
            //Room settings
            $mi_Booking_room = add_submenu_page( 'mi_booking_dashboard', __('Room settings', 'mi_booking'), __('Room settings', 'mi_booking'), 'install_plugins', 'mi_booking_room', array($this, 'room_settings') );
            add_action( 'admin_print_scripts-' . $mi_Booking_room, array($this, 'add_room_scripts') );
            //Time tamplete settings
            $mi_Booking_time_tamplate = add_submenu_page( 'mi_booking_dashboard', __('Time template', 'mi_booking'), __('Time template', 'mi_booking'), 'install_plugins', 'mi_booking_time_template', array($this, 'time_template') );
            add_action( 'admin_print_scripts-' . $mi_Booking_time_tamplate, array($this, 'add_time_tamplate_scripts') );
            //Message settings
            $mi_Booking_message = add_submenu_page( 'mi_booking_dashboard', __('Message settings', 'mi_booking'), __('Message settings', 'mi_booking'), 'install_plugins', 'mi_booking_message', array($this, 'message_settings') );
            add_action( 'admin_print_scripts-' . $mi_Booking_message, array($this, 'add_message_scripts') );
        }
        public function dashboard(){
            global $wpdb;
            $orders = $wpdb->get_results("SELECT * FROM " .$this->booking_table_base."__".$this->mi_booking['room_selected']."  ORDER BY verification;");
            $dash_control = wp_create_nonce( 'dash_control' );
            ?>
                <div class="wrap dashboard"> 
                    <h2 class="pull-left"><?php echo __('Dashboard', 'mi_booking') ?></h2>
                    <div class="pull-right">                        
                        <form class="donate-inline" title="<?php echo __('Give $thank you!', 'mi_booking')?>" action="https://advisor.wmtransfer.com/Spasibo.aspx" method="post" target="_blank">
                            <input type="hidden" name="url" value="http://ivan-mudrik.esy.es/" />
                            <input type="image" name="submit" src="//advisor.wmtransfer.com/img/Spasibo!.png" />
                        </form>
                        <form class="donate-inline" title="<?php echo __('Donate me!', 'mi_booking')?>" action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
                            <input type="hidden" name="cmd" value="_s-xclick">
                            <input type="hidden" name="hosted_button_id" value="23MLURHNJ6AG6">
                            <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" name="submit" alt="<?php echo __('PayPal - The safer, easier way to pay online!', 'mi_booking')?>">
                            <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
                        </form>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    <div class="room-change">
                                            <form method="post" action="<?php echo $_SERVER['PHP_SELF']?>?page=mi_booking_dashboard" id="room-change-form">
                                                <div class="form-inline">
                                                    <div class="col-md-8">
                                                        <select class="form-control" name="change_room_selected" id="room-change">
                                                            <?php foreach ($this->rooms as $room): ?>
                                                                <?php if ($room->id == $this->room):?>
                                                                    <option selected="" value="<?php echo $room->id?>"><?php echo '#'.$room->id.' - '.$room->name_of_room ?></option>	
                                                                <?php else:?>    
                                                                    <option value="<?php echo $room->id?>"><?php echo '#'.$room->id.' - '.$room->name_of_room ?></option>	
                                                                <?php endif;?>
                                                            <?php endforeach;?>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <button type="submit" class="btn btn-success" name="change_room" value="true" form="room-change-form"><?php echo __('Change', 'mi_booking') ?> <i class="fa fa-exchange"></i></button>
                                                    </div>
                                                </div>
                                            </form>
                                    </div>
                                    <div class="pull-right">
                                            <span class="clickable filter" data-toggle="tooltip" title="" data-container="body" data-original-title="<?php echo __('Toggle table filter', 'mi_booking') ?>">
                                                    <i class="glyphicon glyphicon-filter"></i>
                                            </span>
                                    </div>
                                </div>
                                <table class="features-table" id="mi_booking_sortable">
                                    <thead>
                                        <tr>
                                            <td><?php echo __('ID', 'mi_booking') ?></td>
                                            <td><?php echo __('Name of customer', 'mi_booking') ?></td>
                                            <td><?php echo __('Date', 'mi_booking') ?></td>
                                            <td><?php echo __('Time', 'mi_booking') ?></td>
                                            <td><?php echo __('City', 'mi_booking') ?></td>
                                            <td><?php echo __('What is booked', 'mi_booking') ?></td>
                                            <td><?php echo __('E-mail', 'mi_booking') ?></td>
                                            <td><?php echo __('Phone', 'mi_booking') ?></td>
                                            <td class="verification"><?php echo __('Status verification', 'mi_booking') ?></td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                                <?php foreach ($orders as $items): ?>
                                                        <tr>
                                                            <td><?php echo $items->id ?></td>
                                                            <td class="table-break"><?php echo $items->name_of_customer ?></td>
                                                            <td><?php echo $items->date_order ?></td>
                                                            <td><?php echo $items->time_order ?></td>
                                                            <td class="table-break"><?php echo $items->city_order ?></td>
                                                            <td><?php echo $items->what_order ?></td>
                                                            <td class="table-break"><?php echo $items->e_mail ?></td>
                                                            <td><?php echo $items->phone ?></td>
                                                            <td class="dash-control">
                                                                <form method="post" action="<?php echo $_SERVER['PHP_SELF']?>?page=mi_booking_dashboard">
                                                                    <h2 class="<?php 
                                                                            switch ($items->verification)
                                                                            {
                                                                                case '1' : echo 'title_not_confirm';break;
                                                                                case '2' : echo 'title_confirm';break;
                                                                                case '3' : echo 'title_cancel';break;
                                                                            }
                                                                        ?>"><?php echo $items->status_verification ?></h2>
                                                                    <input type="hidden" name="booking_id" value="<?php echo $items->id ?>" />
                                                                    <input type="hidden" name="booking_e_mail" value="<?php echo $items->e_mail ?>" />
                                                                    <input type="hidden" name="dash_control" value="<?php echo $dash_control ?>" />
                                                                    <button type="submit" class="status_confirm" name="status_confirm" ><?php echo __('Confirm', 'mi_booking') ?></button>
                                                                    <button type="submit" class="status_cancel" name="status_cancel" ><?php echo __('Cancel', 'mi_booking') ?></button>
                                                                    <button type="submit" class="status_delete" name="status_delete" ><?php echo __('Delete', 'mi_booking') ?></button>
                                                                </form>  
                                                            </td>
                                                        </tr> 
                                                <?php endforeach;?>    
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>    
                </div>
                <script type="text/javascript">
                    var mi_placeholder = "<?php echo __('Filter Orders', 'mi_booking');?>";
                    var mi_nodata = "<?php echo __('No results found', 'mi_booking');?>";
                    var mi_pagesize = 10;
                </script>
            <?php
        }
        public function jquery2() {
            wp_register_script('jquery-2.0.3', MI_Booking_URL.'js/jquery.min.js' );
            wp_enqueue_script('jquery-2.0.3'); 
        }
        public function font_awesome_4_4_0() {
            wp_register_style('font-awesome-4.4.0', MI_Booking_URL.'css/font-awesome-4.4.0/css/font-awesome.min.css');
            wp_enqueue_style('font-awesome-4.4.0');
        }
        public function add_plugin_script() {            
            wp_enqueue_style('MI_Booking-admin', MI_Booking_URL.'css/style-admin.css');  
            $this->jquery2();  
            $this->font_awesome_4_4_0();
            wp_register_script('pbTable', MI_Booking_URL.'js/pbTable.min.js', array('jquery-2.0.3'));  
            wp_enqueue_script('pbTable');    
            wp_register_script('admin_script', MI_Booking_URL.'js/admin-script.js', array('jquery-2.0.3'));
            wp_enqueue_script('admin_script');
            //bootstrap
            $this->bootstrap();
        }
        public function bootstrap() {
            wp_enqueue_style('bootstrap', MI_Booking_URL.'css/bootstrap/css/bootstrap.min.css');
            wp_enqueue_script('bootstrap_js', MI_Booking_URL.'css/bootstrap/js/bootstrap.min.js', array('jquery-2.0.3'));
        }
        public function datepicker() {
            wp_register_script('datepicker-js', MI_Booking_URL.'js/datepicker.js', array('jquery-2.0.3'));
            wp_enqueue_script('datepicker-js');
            wp_register_style('datepicker-css', MI_Booking_URL.'css/datepicker.css');
            wp_enqueue_style('datepicker-css');
        }
        public function datapicker_internationalize() {
            ?>
                <script type="text/javascript">
                    var MIBookingOptions = {
                        format:'<?php echo get_option( 'date_format' )?>',
                        starts:<?php echo get_option( 'start_of_week' )?>,
                        inlocale: {
                                days: [
                                    "<?php echo __('Sunday', 'mi_booking');?>",
                                    "<?php echo __('Monday', 'mi_booking');?>",
                                    "<?php echo __('Tuesday', 'mi_booking');?>",
                                    "<?php echo __('Wednesday', 'mi_booking');?>", 
                                    "<?php echo __('Thursday', 'mi_booking');?>", 
                                    "<?php echo __('Friday', 'mi_booking');?>", 
                                    "<?php echo __('Saturday', 'mi_booking');?>", 
                                    "<?php echo __('Sunday', 'mi_booking');?>"
                                ],
                                daysShort: [
                                    "<?php echo __('Sun', 'mi_booking');?>",
                                    "<?php echo __('Mon', 'mi_booking');?>", 
                                    "<?php echo __('Tue', 'mi_booking');?>", 
                                    "<?php echo __('Wed', 'mi_booking');?>", 
                                    "<?php echo __('Thu', 'mi_booking');?>", 
                                    "<?php echo __('Fri', 'mi_booking');?>", 
                                    "<?php echo __('Sat', 'mi_booking');?>", 
                                    "<?php echo __('Sun', 'mi_booking');?>"
                                ],
                                daysMin: [
                                    "<?php echo __('Su', 'mi_booking');?>", 
                                    "<?php echo __('Mo', 'mi_booking');?>", 
                                    "<?php echo __('Tu', 'mi_booking');?>", 
                                    "<?php echo __('We', 'mi_booking');?>", 
                                    "<?php echo __('Th', 'mi_booking');?>", 
                                    "<?php echo __('Fr', 'mi_booking');?>", 
                                    "<?php echo __('Sa', 'mi_booking');?>", 
                                    "<?php echo __('Su', 'mi_booking');?>"
                                ],
                                months: [
                                    "<?php echo __('January', 'mi_booking');?>", 
                                    "<?php echo __('February', 'mi_booking');?>", 
                                    "<?php echo __('March', 'mi_booking');?>", 
                                    "<?php echo __('April', 'mi_booking');?>", 
                                    "<?php echo __('May', 'mi_booking');?>", 
                                    "<?php echo __('June', 'mi_booking');?>", 
                                    "<?php echo __('July', 'mi_booking');?>", 
                                    "<?php echo __('August', 'mi_booking');?>", 
                                    "<?php echo __('September', 'mi_booking');?>", 
                                    "<?php echo __('October', 'mi_booking');?>", 
                                    "<?php echo __('November', 'mi_booking');?>", 
                                    "<?php echo __('December', 'mi_booking');?>"
                                ],
                                monthsShort: [
                                    "<?php echo __('Jan', 'mi_booking');?>",
                                    "<?php echo __('Feb', 'mi_booking');?>", 
                                    "<?php echo __('Mar', 'mi_booking');?>", 
                                    "<?php echo __('Apr', 'mi_booking');?>", 
                                    "<?php echo __('May', 'mi_booking');?>", 
                                    "<?php echo __('Jun', 'mi_booking');?>", 
                                    "<?php echo __('Jul', 'mi_booking');?>", 
                                    "<?php echo __('Aug', 'mi_booking');?>",
                                    "<?php echo __('Sep', 'mi_booking');?>",
                                    "<?php echo __('Oct', 'mi_booking');?>", 
                                    "<?php echo __('Nov', 'mi_booking');?>", 
                                    "<?php echo __('Dec', 'mi_booking');?>"
                                ],
                                weekMin: '<?php echo __('w', 'mi_booking');?>'
                            }
                        }                            
                </script>
            <?php    
        }
        public function save_msg() {
            if( isset($_GET['settings-updated']))
            {
                if(!$this->mi_booking['is_delete'])
                {
                    if(!$this->mi_booking['update_error']['state'])
                    {
                        ?>
                            <div id="message" class="updated" style="margin: 5px 0 2px;">
                                <p><strong><?php echo __('Settings saved.', 'mi_booking') ?></strong></p>
                            </div>
                        <?php 
                    }
                    else
                    {
                        foreach ($this->mi_booking['update_error']['problem'] as $problem)
                        {
                            ?>
                                <div id="message" class="error" style="margin: 5px 0 2px;">
                                    <p><strong><?php echo __('Update error.', 'mi_booking') ?> <?php echo $problem ?></strong></p>
                                </div>
                            <?php
                        }
                        $this->mi_booking['update_error']['state'] = FALSE;
                    }
                }
                else
                {
                    if( !empty( $this->mi_booking['delete_status'] ) )
                    {
                        ?>
                            <div id="message" class="updated" style="margin: 5px 0 2px;">
                                <p><strong><?php echo __('Successful delete.', 'mi_booking') ?></strong></p>
                            </div>
                        <?php
                    }
                    else 
                    {
                        ?>
                            <div id="message" class="error" style="margin: 5px 0 2px;">
                                <p><strong><?php echo __('Error removing.', 'mi_booking') ?></strong></p>
                            </div>
                        <?php                    
                    }
                    $this->mi_booking['is_delete'] = FALSE;
                }
            }
            if(isset($_POST['time_tamplates_save']))
            {
                if(!$this->mi_booking['update_error']['state'])
                    {
                        ?>
                            <div id="message" class="updated" style="margin: 5px 0 2px;">
                                <p><strong><?php echo __('Template save.', 'mi_booking') ?></strong></p>
                            </div>
                        <?php 
                    }
                    else
                    {
                        foreach ($this->mi_booking['update_error']['problem'] as $problem)
                        {
                            ?>
                                <div id="message" class="error" style="margin: 5px 0 2px;">
                                    <p><strong><?php echo __('Saving error.', 'mi_booking') ?> <?php echo $problem ?></strong></p>
                                </div>
                            <?php
                        }
                        $this->mi_booking['update_error']['state'] = FALSE;
                    }
            }
            if(isset($_POST['date_tamplates_save']))
            {
                if(!$this->mi_booking['update_error']['state'])
                    {
                        ?>
                            <div id="message" class="updated" style="margin: 5px 0 2px;">
                                <p><strong><?php echo __('Holiday template saved.', 'mi_booking') ?></strong></p>
                            </div>
                        <?php 
                    }
                    else
                    {
                        foreach ($this->mi_booking['update_error']['problem'] as $problem)
                        {
                            ?>
                                <div id="message" class="error" style="margin: 5px 0 2px;">
                                    <p><strong><?php echo __('Saving error.', 'mi_booking') ?> <?php echo $problem?></strong></p>
                                </div>
                            <?php
                        }
                        $this->mi_booking['update_error']['state'] = FALSE;
                    }
            }
            if(isset($_POST['add_room_save']))
            {
                if(!$this->mi_booking['update_error']['state'])
                {
                    ?>
                        <div id="message" class="updated" style="margin: 5px 0 2px;">
                            <p><strong><?php echo __('Room save.', 'mi_booking') ?></strong></p>
                        </div>
                    <?php 
                }
                else
                {
                    foreach ($this->mi_booking['update_error']['problem'] as $problem)
                    {
                        ?>
                            <div id="message" class="error" style="margin: 5px 0 2px;">
                                <p><strong><?php echo __('Saving error.', 'mi_booking') ?> <?php echo $problem ?></strong></p>
                            </div>
                        <?php
                    }
                    $this->mi_booking['update_error']['state'] = FALSE;
                }
            }
            unset($this->mi_booking['update_error']['problem']);
            $this->mi_update();
        }
        public function delete_msg() {
            if (isset($_POST['time_tamplate_delete_btn']) || isset($_POST['time_holiday_delete_btn']))
            {
                $this->mi_booking['is_delete'] = FALSE;
                $this->mi_update();
                if($this->mi_booking['delete_status'])
                {
                    ?>
                        <div id="message" class="updated" style="margin: 5px 0 2px;">
                            <p><strong><?php echo __('Successful delete.', 'mi_booking') ?></strong></p>
                        </div>
                    <?php
                }
                else 
                {
                    ?>
                        <div id="message" class="error" style="margin: 5px 0 2px;">
                            <p><strong><?php echo __('Error removing.', 'mi_booking') ?></strong></p>
                        </div>
                    <?php                    
                }
            }
        }
        public function room_settings() {
            echo "<h2>". __('Room settings page', 'mi_booking') ."</h2>";
            ?>
                <div class="add-room">
                    <span class="btn btn-success">
                        <?php echo __('Add room', 'mi_booking') ?>
                        <i class="fa fa-plus-square"></i>
                    </span>
                    <div class="add-room-inputs">
                        <form method="post" action="<?php echo $_SERVER['PHP_SELF']?>?page=mi_booking_room" id="add-room-form">
                            <div class="form-group">
                                <label for="add-room-name"><?php echo __('Room name', 'mi_booking') ?></label>
                                <input type="text" class="form-control" id="add-room-name" name="add_room_name" placeholder="<?php echo __('Name', 'mi_booking') ?>" maxlength="150">
                            </div>
                            <div class="form-group">
                                <label for="add-room-disp-days"><?php echo __('Display days', 'mi_booking') ?></label>
                                <input type="number" class="form-control" id="add-room-disp-days" name="add_room_disp_days" value="30">
                            </div>
                            <div class="form-group iphone-checkbox">
                                <label for="add-room-show-city"><?php echo __('Show city', 'mi_booking') ?></label>
                                <input type="checkbox" class="form-control" id="add-room-show-city" name="add_room_show_city" value="1">
                            </div>
                            <button type="submit" class="btn btn-primary" name="add_room_save" form="add-room-form"><?php echo __('Save', 'mi_booking') ?> <i class="fa fa-floppy-o"></i></button>
                        </form>    
                    </div>
                </div>
                <div class="room-change">
                    <div class="form-group">
                        <form method="post" action="<?php echo $_SERVER['PHP_SELF']?>?page=mi_booking_room" id="room-change-form">
                            <label for="room-change"><?php echo __('Change room', 'mi_booking') ?></label>
                            <div class="form-inline">
                                <div class="col-md-8">
                                    <select class="form-control" name="change_room_selected" id="room-change">
                                        <?php foreach ($this->rooms as $room): ?>
                                            <?php if ($room->id == $this->room):?>
                                                <option selected="" value="<?php echo $room->id?>"><?php echo '#'.$room->id.' - '.$room->name_of_room ?></option>	
                                            <?php else:?>    
                                                <option value="<?php echo $room->id?>"><?php echo '#'.$room->id.' - '.$room->name_of_room ?></option>	
                                            <?php endif;?>
                                        <?php endforeach;?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-primary" name="change_room" value="true" form="room-change-form"><?php echo __('Change', 'mi_booking') ?> <i class="fa fa-exchange"></i></button>
                                    <button type="submit" class="btn btn-danger" name="delete_room" value="true" form="room-change-form"><?php echo __('Delete', 'mi_booking') ?> <i class="fa fa-times"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <p><?php echo __('Room shortcode', 'mi_booking') ?> [mi_booking id="<?php echo $this->room ?>"]</p>
            <?php
                $this->save_msg();
            ?>                
                <form id="room_settings" method="post" action="options.php">
                        <?php
                                settings_fields( 'mi_booking_room' ); 
                                do_settings_sections( 'mi_booking_room' ); 
                                submit_button(); 
                        ?>
                </form>
                <script type="text/javascript">
                    var deleteMessage = "<?php echo __('Delete?\nAre You sure', 'mi_booking');?>";                    
                </script>
            <?php
        }
        public function add_room_scripts() {
            $this->jquery2();
            $this->bootstrap();
            $this->font_awesome_4_4_0();            
            wp_register_style('admin-room-css', MI_Booking_URL.'css/admin-room-css.css');
            wp_enqueue_style('admin-room-css');
            wp_register_style('slider-checkbox', MI_Booking_URL.'css/slider-checkbox.css');
            wp_enqueue_style('slider-checkbox');
            wp_register_script('iphone-checkbox', MI_Booking_URL.'js/iphone-style-checkboxes.js', array('jquery-2.0.3'));
            wp_enqueue_script('iphone-checkbox');
            wp_register_script('admin-room', MI_Booking_URL.'js/admin-room.js', array('jquery-2.0.3', 'iphone-checkbox'));
            wp_enqueue_script('admin-room');
        }
        public function time_template() {
            ?>
                <h2><?php echo __('Time template settings page', 'mi_booking') ?></h2>
                <div class="add-new-btn-inline">
                    <div class="add-time-tamplates">
                        <span class="btn btn-success">
                            <?php echo __('Add time template', 'mi_booking') ?>
                            <i class="fa fa-plus-square"></i>
                        </span>
                    </div>
                    <div class="add-date-tamplate">
                        <span class="btn btn-success">
                            <?php echo __('Add date template', 'mi_booking') ?>
                            <i class="fa fa-plus-square"></i>
                        </span>
                    </div>
                </div>                
                <div class="add-time-tamplates-inputs">
                    <form method="post" action="<?php echo $_SERVER['PHP_SELF']?>?page=mi_booking_time_template" id="add-time-tamplates-form">
                        <div class="form-group">
                            <label for="mi_booking_what_day_input"><?php echo __('Enter day abbreviation', 'mi_booking') ?></label>
                            <input title="<?php echo __('Format: WD or F or WE or H', 'mi_booking') ?>"  class="form-control what_day_input" type="text" id="mi_booking_what_day_input" name="mi_booking_what_day_input" placeholder="<?php echo __('Format: WD or F or WE or H', 'mi_booking') ?>" />
                        </div>  
                        <div class="form-group">
                            <label for="mi_booking_time_input"><?php echo __('Enter time', 'mi_booking') ?></label>
                            <input title="<?php echo __('Format: HH:MM', 'mi_booking') ?>"  class="form-control time_input" type="time" id="mi_booking_time_input" name="mi_booking_time_input" value="00:00" />
                        </div>
                        <div class="form-group">
                            <label for="mi_booking_cost_input"><?php echo __('Enter cost', 'mi_booking') ?></label>
                            <input title="<?php echo __('Format: 99999999.99', 'mi_booking') ?>"  class="form-control cost_input" type="text" id="mi_booking_cost_input" name="mi_booking_cost_input" maxlength="11" placeholder="99999999.99" />
                        </div> 
                        <button type="submit" class="btn btn-primary" name="time_tamplates_save" form="add-time-tamplates-form"><?php echo __('Save', 'mi_booking') ?> <i class="fa fa-floppy-o"></i></button>
                    </form>    
                </div>
                <div class="add-date-tamplate-inputs">
                    <form method="post" action="<?php echo $_SERVER['PHP_SELF']?>?page=mi_booking_time_template" id="add-date-tamplates-form">
                        <div class="time-holiday form-group">
                            <label for="mi_booking_date_input"><?php echo __('Enter holiday date', 'mi_booking') ?></label>
                            <input title="<?php echo __('Format: YYYY-MM-DD', 'mi_booking') ?>" type="text" data-format-uniq="<?php echo __('Format: YYYY-MM-DD', 'mi_booking') ?>" data-format-ununiq="<?php echo __('Format: MM-DD', 'mi_booking') ?>" name="mi_booking_date_input" class="form-control" id="mi_booking_date_input" data-timeformated="<?php echo date( 'Y-m-d', time()) ?>" value="<?php echo date(get_option( 'date_format' ), time()) ?>">
                        </div>
                        <div class="form-group iphone-checkbox" title="<?php echo __('Apply holiday for every year?', 'mi_booking') ?>">
                            <label for="date-every-year"><?php echo __('Every year?', 'mi_booking') ?></label>
                            <input type="checkbox" class="form-control" id="date-every-year" name="date_every_year" value="1">
                        </div>
                        <button type="submit" class="btn btn-primary" name="date_tamplates_save" form="add-date-tamplates-form"><?php echo __('Save', 'mi_booking') ?> <i class="fa fa-floppy-o"></i></button>
                    </form>
                </div>
                <?php 
                    $this->save_msg();
                    $this->delete_msg();
                ?>
                <div class="room-change">
                        <form method="post" action="<?php echo $_SERVER['PHP_SELF']?>?page=mi_booking_time_template" id="room-change-form">
                            <div class="form-inline">
                                <div class="col-md-8">
                                    <select class="form-control" name="change_room_selected" id="room-change">
                                        <?php foreach ($this->rooms as $room): ?>
                                            <?php if ($room->id == $this->room):?>
                                                <option selected="" value="<?php echo $room->id?>"><?php echo '#'.$room->id.' - '.$room->name_of_room ?></option>	
                                            <?php else:?>    
                                                <option value="<?php echo $room->id?>"><?php echo '#'.$room->id.' - '.$room->name_of_room ?></option>	
                                            <?php endif;?>
                                        <?php endforeach;?>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-success" name="change_room" value="true" form="room-change-form"><?php echo __('Change', 'mi_booking') ?> <i class="fa fa-exchange"></i></button>
                                </div>
                            </div>
                        </form>
                </div>                
                <form action="<?php echo $_SERVER['PHP_SELF']?>?page=mi_booking_time_template" id="time_template_delete" method="post"></form>
                <form action="<?php echo $_SERVER['PHP_SELF']?>?page=mi_booking_time_template" id="time_holiday_delete" method="post"></form>
                <form id="time_template" method="post" action="options.php">
                        <?php
                                settings_fields( 'mi_booking_time_template' ); 
                                do_settings_sections( 'mi_booking_time_template' ); 
                                submit_button(); 
                        ?>
                </form>
            <?php
            $this->datapicker_internationalize();
        }
        public function add_time_tamplate_scripts() {
            $this->jquery2();
            $this->bootstrap();
            $this->font_awesome_4_4_0();
            wp_register_style('admin-time-tamplate-css', MI_Booking_URL.'css/admin-time-tamplate-css.css');
            wp_enqueue_style('admin-time-tamplate-css');
            wp_register_style('slider-checkbox', MI_Booking_URL.'css/slider-checkbox.css');
            wp_enqueue_style('slider-checkbox');
            wp_register_script('iphone-checkbox', MI_Booking_URL.'js/iphone-style-checkboxes.js', array('jquery-2.0.3'));
            wp_enqueue_script('iphone-checkbox');
            wp_register_script('admin-time-tamplate-js', MI_Booking_URL.'js/admin-time-tamplate-js.js', array('jquery-2.0.3', 'iphone-checkbox'));
            wp_enqueue_script('admin-time-tamplate-js');            
            $this->datepicker();
        }
        public function message_settings() {
            ?>
                <h2><?php echo __('Message settings page', 'mi_booking') ?></h2>
                <?php $this->save_msg() ?>
                <form id="message_settings" method="post" action="options.php">
                        <?php
                                settings_fields( 'mi_booking_message' ); 
                                do_settings_sections( 'mi_booking_message' ); 
                                submit_button(); 
                        ?>
                </form>
            <?php
        }
        public function add_message_scripts() {
            $this->bootstrap();
            wp_register_style('admin-message-css', MI_Booking_URL.'css/admin-message-css.css');
            wp_enqueue_style('admin-message-css');
        }
    }    
}