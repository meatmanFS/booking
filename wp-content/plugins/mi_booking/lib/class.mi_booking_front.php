<?php

defined( 'ABSPATH' ) or die( '<h3>No access to the file!</h3>' );

if (!class_exists('MI_Booking_Front'))
{
    class MI_Booking_Front extends MI_Booking {    
		
		public $data;


		public function __construct() {
            parent::__construct();
			add_action( 'wp_enqueue_scripts', array( $this, 'script' ) );
			$this->save_booking();
			add_shortcode( 'mi_booking', array( $this, 'front') );			
        }
		
		public function front( $attr ){
			$this->get_data( $attr );
			global $wpdb;	
			add_action( 'wp_footer', array( $this, 'footer' ) );
			ob_start();
			if ( $this->data['has_room'] ){
				?>
				<table width="100%">
				<?php 
					$room_id = intval( $attr['id'] );
					list( $year, $month, $day, $day_of_week ) = explode('-', date('Y-m-d-w'));
					$day_for_check = $day;
					$month_for_check = $month;
					$timestamp = mktime(0, 0, 0, $month, $day, $year);
					list( $month_name, $days_in_month ) = explode('-', date('F-t', $timestamp));
					if ($day_of_week == 0) {
						$day_of_week = 7;
					}
					$time_tamplate	= $this->booking_time_tamplate_base . '__' . $room_id;
					$time			= $this->booking_time_base . '__' . $room_id;
					
					$this->clean_db( $room_id );
					
					$end_of_calendar = true;
					$number_of_days_add_now_day = $this->data['room']->number_of_days + $day;
					$number_of_days_add_now_day -= 1;
					$number_of_days_calc = $number_of_days_add_now_day;

					$month_count = 0;

					for ($i = $month; $end_of_calendar; $i++) {
						if ($i > 12) {
							$i = 1;
							$year++;
						}
						$timestamp = mktime(0, 0, 0, $i, $day, $year);
						list($month_name, $days_in_month) = explode('-', date('F-t', $timestamp));
						$res_now_month = $this->now_month($month++);

						if ($number_of_days_add_now_day <= $days_in_month) {
							$end_of_calendar = false;
							$days_in_month = $number_of_days_add_now_day;
						} else {
							if ($number_of_days_calc > $days_in_month) {
								$get_month_count = $month_count + 1;
							} else {
								$days_in_month = $number_of_days_calc;
							}
							$number_of_days_calc = $number_of_days_calc - $days_in_month;
						}

						if ($month_count == $get_month_count) {
							$end_of_calendar = false;
						}
						$month_count++;

						for ($j = $day; $j <= $days_in_month; $j++) {
							$res_now_week = $this->day_of_week($day_of_week);

							?>
							<tr>
							<td align="left">
								<?php echo $j;?> <?php echo $res_now_month;?><br><i> <?php echo $res_now_week;?></i>
							</td>
							<?php
							if ($j < 10) {
								if ($j == $day_for_check && $i == $month_for_check) {
									$current_day = $j;
								} else {
									$current_day = '0' . $j;
								}
							} else {
								$current_day = $j;
							}
							if (($i < 10) && ($i != $month_for_check)) {
								$current_month = '0' . $i;
							} else {
								$current_month = $i;
							}
							$rasp_now_day_comp = $current_day . '.' . $current_month;
							$holiday_result = $wpdb->query("SELECT * FROM {$this->table_of_holidays} WHERE date_of_holiday='$rasp_now_day_comp';");

							if ($holiday_result == false) {
								if ($day_of_week > 7) {
									if ($day_of_week % 7 == 5 || $day_of_week % 7 == 6 || $day_of_week % 7 == 0) {
										if ($day_of_week % 7 == 5) {
											$time_tamplate_data = $wpdb->get_results("SELECT * FROM $time_tamplate where what_day = 'F' ORDER BY time ");
											foreach ($time_tamplate_data as $items) {
												$result = $wpdb->query('SELECT * FROM ' . $time . ' WHERE day = "' . $year . '-' . $i . '-' . $j . '" && time = "' . $items->time . '" && available = 0;');
												if ($result != false) {
													?>
													<td class="avelable" align="center" time="<?php echo $items->time;?>" date="<?php echo $year . '-' . $i . '-' . $j;?>"><?php echo $items->time;?><br><hr style="margin-bottom:0px"><?php echo $items->cost;?></td>
													<?php
												} else {
													$result = $wpdb->query('SELECT * FROM ' . $time . ' WHERE day = "' . $year . '-' . $i . '-' . $j . '" && time = "' . $items->time . '" && available = 1;');

													if ($result != false) {
														?>
														<td class="" align="center" time="<?php echo $items->time;?>" date="<?php echo $year . '-' . $i . '-' . $j;?>"><?php echo $items->time;?><br><hr style="margin-bottom:0px"><?php echo $items->cost;?></td>
														<?php
													} else {
														?>
														<td class="avelable" align="center" time="<?php echo $items->time;?>" date="<?php echo $year . '-' . $i . '-' . $j;?>"><?php echo $items->time;?><br><hr style="margin-bottom:0px"><?php echo $items->cost;?></td>
														<?php
														$data = $year . '-' . $i . '-' . $j;
														$items_time = $items->time;
														$wpdb->insert(
																$time, array('day' => $data, 'time' => $items_time), array('%s', '%s')
														);
													}
												}
											}
										} else {
											$time_tamplate_data = $wpdb->get_results("SELECT * FROM $time_tamplate where what_day = 'WE' ORDER BY time ");
											foreach ($time_tamplate_data as $items) {
												$result = $wpdb->query('SELECT * FROM ' . $time . ' WHERE day = "' . $year . '-' . $i . '-' . $j . '" && time = "' . $items->time . '" && available = 0;');
												if ($result != false) {
													?>
													<td class="avelable" align="center" time="<?php echo $items->time;?>" date="<?php echo $year . '-' . $i . '-' . $j;?>"><?php echo $items->time;?><br><hr style="margin-bottom:0px"><?php echo $items->cost;?></td>
													<?php
												} else {
													$result = $wpdb->query('SELECT * FROM ' . $time . ' WHERE day = "' . $year . '-' . $i . '-' . $j . '" && time = "' . $items->time . '" && available = 1;');

													if ($result != false) {
														?>
														<td class="" align="center" time="<?php echo $items->time;?>" date="<?php echo $year . '-' . $i . '-' . $j;?>"><?php echo $items->time;?><br><hr style="margin-bottom:0px"><?php echo $items->cost;?></td>
														<?php
													} else {
														?>
														<td class="avelable" align="center" time="<?php echo $items->time;?>" date="<?php echo $year . '-' . $i . '-' . $j;?>"><?php echo $items->time;?><br><hr style="margin-bottom:0px"><?php echo $items->cost;?></td>
														<?php
														$data = '' . $year . '-' . $i . '-' . $j . '';
														$items_time = '' . $items->time . '';
														$wpdb->insert(
																$time, array('day' => $data, 'time' => $items_time), array('%s', '%s')
														);
													}
												}
											}
										}
									} else {
										$time_tamplate_data = $wpdb->get_results("SELECT * FROM $time_tamplate where what_day = 'WD' ORDER BY time ");
										foreach ($time_tamplate_data as $items) {
											$result = $wpdb->query('SELECT * FROM ' . $time . ' WHERE day = "' . $year . '-' . $i . '-' . $j . '" && time = "' . $items->time . '" && available = 0;');
											if ($result != false) {
												?>
													<td class="avelable" align="center" time="<?php echo $items->time;?>" date="<?php echo $year . '-' . $i . '-' . $j;?>"><?php echo $items->time;?><br><hr style="margin-bottom:0px"><?php echo $items->cost;?></td>
												<?php
											} else {
												$result = $wpdb->query('SELECT * FROM ' . $time . ' WHERE day = "' . $year . '-' . $i . '-' . $j . '" && time = "' . $items->time . '" && available = 1;');

												if ($result != false) {
													?>
													<td class="" align="center" time="<?php echo $items->time;?>" date="<?php echo $year . '-' . $i . '-' . $j;?>"><?php echo $items->time;?><br><hr style="margin-bottom:0px"><?php echo $items->cost;?></td>
													<?php
												} else {
													?>
													<td class="avelable" align="center" time="<?php echo $items->time;?>" date="<?php echo $year . '-' . $i . '-' . $j;?>"><?php echo $items->time;?><br><hr style="margin-bottom:0px"><?php echo $items->cost;?></td>
													<?php
													$data = '' . $year . '-' . $i . '-' . $j . '';
													$items_time = '' . $items->time . '';
													$wpdb->insert(
															$time, array('day' => $data, 'time' => $items_time), array('%s', '%s')
													);
												}
											}
										}
									}
								} else {
									if ($day_of_week == 5 || $day_of_week == 6 || $day_of_week == 7) {
										if ($day_of_week == 5) {
											$time_tamplate_data = $wpdb->get_results("SELECT * FROM $time_tamplate where what_day = 'F' ORDER BY time ");

											foreach ($time_tamplate_data as $items) {
												$result = $wpdb->query('SELECT * FROM ' . $time . ' WHERE day = "' . $year . '-' . $i . '-' . $j . '" && time = "' . $items->time . '" && available = 0;');
												if ($result != false) {
													?>
													<td class="avelable" align="center" time="<?php echo $items->time;?>" date="<?php echo $year . '-' . $i . '-' . $j;?>"><?php echo $items->time;?><br><hr style="margin-bottom:0px"><?php echo $items->cost;?></td>
													<?php
												} else {
													$result = $wpdb->query('SELECT * FROM ' . $time . ' WHERE day = "' . $year . '-' . $i . '-' . $j . '" && time = "' . $items->time . '" && available = 1;');

													if ($result != false) {
														?>
														<td class="" align="center" time="<?php echo $items->time;?>" date="<?php echo $year . '-' . $i . '-' . $j;?>"><?php echo $items->time;?><br><hr style="margin-bottom:0px"><?php echo $items->cost;?></td>
														<?php
													} else {
														?>
														<td class="avelable" align="center" time="<?php echo $items->time;?>" date="<?php echo $year . '-' . $i . '-' . $j;?>"><?php echo $items->time;?><br><hr style="margin-bottom:0px"><?php echo $items->cost;?></td>
														<?php
														$data = '' . $year . '-' . $i . '-' . $j . '';
														$items_time = '' . $items->time . '';
														$wpdb->insert(
																$time, array('day' => $data, 'time' => $items_time), array('%s', '%s')
														);
													}
												}
											}
										} else {
											$time_tamplate_data = $wpdb->get_results("SELECT * FROM $time_tamplate where what_day = 'WE' ORDER BY time ");
											foreach ($time_tamplate_data as $items) {
												$result = $wpdb->query('SELECT * FROM ' . $time . ' WHERE day = "' . $year . '-' . $i . '-' . $j . '" && time = "' . $items->time . '" && available = 0;');
												if ($result != false) {
													?>
													<td class="avelable" align="center" time="<?php echo $items->time;?>" date="<?php echo $year . '-' . $i . '-' . $j;?>"><?php echo $items->time;?><br><hr style="margin-bottom:0px"><?php echo $items->cost;?></td>
													<?php
												} else {
													$result = $wpdb->query('SELECT * FROM ' . $time . ' WHERE day = "' . $year . '-' . $i . '-' . $j . '" && time = "' . $items->time . '" && available = 1;');

													if ($result != false) {
														?>
														<td class="" align="center" time="<?php echo $items->time;?>" date="<?php echo $year . '-' . $i . '-' . $j;?>"><?php echo $items->time;?><br><hr style="margin-bottom:0px"><?php echo $items->cost;?></td>
														<?php
													} else {
														?>
														<td class="avelable" align="center" time="<?php echo $items->time;?>" date="<?php echo $year . '-' . $i . '-' . $j;?>"><?php echo $items->time;?><br><hr style="margin-bottom:0px"><?php echo $items->cost;?></td>
														<?php

														$data = '' . $year . '-' . $i . '-' . $j . '';
														$items_time = '' . $items->time . '';
														$wpdb->insert(
																$time, array('day' => $data, 'time' => $items_time), array('%s', '%s')
														);
													}
												}
											}
										}
									} else {
										$time_tamplate_data = $wpdb->get_results("SELECT * FROM $time_tamplate where what_day = 'WD' ORDER BY time ");
										foreach ($time_tamplate_data as $items) {
											$result = $wpdb->query('SELECT * FROM ' . $time . ' WHERE day = "' . $year . '-' . $i . '-' . $j . '" && time = "' . $items->time . '" && available = 0;');
											if ($result != false) {
												?>
												<td class="avelable" align="center" time="<?php echo $items->time;?>" date="<?php echo $year . '-' . $i . '-' . $j;?>"><?php echo $items->time;?><br><hr style="margin-bottom:0px"><?php echo $items->cost;?></td>
												<?php
											} else {
												$result = $wpdb->query('SELECT * FROM ' . $time . ' WHERE day = "' . $year . '-' . $i . '-' . $j . '" && time = "' . $items->time . '" && available = 1;');

												if ($result != false) {
													?>
													<td class="" align="center" time="<?php echo $items->time;?>" date="<?php echo $year . '-' . $i . '-' . $j;?>"><?php echo $items->time;?><br><hr style="margin-bottom:0px"><?php echo $items->cost;?></td>
													<?php
												} else {
													?>
													<td class="avelable" align="center" time="<?php echo $items->time;?>" date="<?php echo $year . '-' . $i . '-' . $j;?>"><?php echo $items->time;?><br><hr style="margin-bottom:0px"><?php echo $items->cost;?></td>
													<?php

													$data = '' . $year . '-' . $i . '-' . $j . '';
													$items_time = '' . $items->time . '';
													$wpdb->insert(
															$time, array('day' => $data, 'time' => $items_time), array('%s', '%s')
													);
												}
											}
										}
									}
								}
								?>
								</tr>
								<?php
								$day_of_week++;
							} else {
								$time_holiday = $wpdb->get_results("SELECT * FROM $time_tamplate where what_day = 'H' ORDER BY time");
								foreach ($time_holiday as $items) {
									$result = $wpdb->query('SELECT * FROM ' . $time . ' WHERE day = "' . $year . '-' . $i . '-' . $j . '" && time = "' . $items->time . '" && available = 0;');
									if ($result != false) {
										?>
										<td class="avelable" align="center" time="<?php echo $items->time;?>" date="<?php echo $year . '-' . $i . '-' . $j;?>"><?php echo $items->time;?><br><hr style="margin-bottom:0px"><?php echo $items->cost;?></td>
										<?php
									} else {
										$result = $wpdb->query('SELECT * FROM ' . $time . ' WHERE day = "' . $year . '-' . $i . '-' . $j . '" && time = "' . $items->time . '" && available = 1;');

										if ($result != false) {
											?>
											<td class="" align="center" time="<?php echo $items->time;?>" date="<?php echo $year . '-' . $i . '-' . $j;?>"><?php echo $items->time;?><br><hr style="margin-bottom:0px"><?php echo $items->cost;?></td>
											<?php
										} else {
											?>
											<td class="avelable" align="center" time="<?php echo $items->time;?>" date="<?php echo $year . '-' . $i . '-' . $j;?>"><?php echo $items->time;?><br><hr style="margin-bottom:0px"><?php echo $items->cost;?></td>
											<?php
											$data = '' . $year . '-' . $i . '-' . $j . '';
											$items_time = '' . $items->time . '';
											$wpdb->insert(
													$time, array('day' => $data, 'time' => $items_time), array('%s', '%s')
											);
										}
									}
								}$day_of_week++;
							}
						}//end for
						$day = 1;
					}
				?>
				</table>
				<?php 
			} else {
				?>
				<h2><b><?php _e( 'Rooms does not exist!', 'mi_booking' ); ?></b></h2>
				<?php 
			}
			return ob_get_clean();
		}
		
		public function now_month( $now_month ) {
			if ( $now_month > 12){
				switch( $now_month % 12){
				case '1': 
						$return = __( 'January', 'mi_booking' );
						break;
				case '2':
						$return = __( 'Ferbruary', 'mi_booking' );
						break;
				case '3':
						$return = __( 'Manrch', 'mi_booking' );
						break;
				case '4':
						$return = __( 'April', 'mi_booking' );
						break;
				case '5': 
						$return = __( 'May', 'mi_booking' );
						break;
				case '6': 
						$return = __( 'June', 'mi_booking' );
						break;
				case '7':
						$return = __( 'July', 'mi_booking' );
						break;
				case '8':
						$return = __( 'August', 'mi_booking' );
						break;
				case '9':
						$return = __( 'September', 'mi_booking' );
						break;
				case '10': 
						$return = __( 'October', 'mi_booking' );
						break;
				case '11': 
						$return = __( 'November', 'mi_booking' );
						break;
				case '0':
						$return = __( 'Devember', 'mi_booking' );
						break;
				}
			} else {
				switch( $now_month ){
				case '1': 
						$return = __( 'January', 'mi_booking' );
						break;
				case '2':
						$return = __( 'Ferbruary', 'mi_booking' );
						break;
				case '3':
						$return = __( 'Manrch', 'mi_booking' );
						break;
				case '4':
						$return = __( 'April', 'mi_booking' );
						break;
				case '5': 
						$return = __( 'May', 'mi_booking' );
						break;
				case '6': 
						$return = __( 'June', 'mi_booking' );
						break;
				case '7':
						$return = __( 'July', 'mi_booking' );
						break;
				case '8':
						$return = __( 'August', 'mi_booking' );
						break;
				case '9':
						$return = __( 'September', 'mi_booking' );
						break;
				case '10': 
						$return = __( 'October', 'mi_booking' );
						break;
				case '11': 
						$return = __( 'November', 'mi_booking' );
						break;
				case '12':
						$return = __( 'Devember', 'mi_booking' );
						break;
				}
			}
			return $return;
		}
		
		public function day_of_week( $day_of_week ){
			$days = array( 
				__( 'Monday', 'mi_booking' ),
				__( 'Tuesday', 'mi_booking' ),
				__( 'Wednesday', 'mi_booking' ),
				__( 'Thursday', 'mi_booking' ),
				__( 'Friday', 'mi_booking' ),
				__( 'Saturday', 'mi_booking' ),
				__( 'Sunday', 'mi_booking' ),
			);
			if ($day_of_week > 7){
				$day_of_week = $day_of_week % 7;
				switch($day_of_week){
				case 1: 
						$return = $days[0];
						break;
				case 2:
						$return = $days[1];
						break;
				case 3:
						$return = $days[2];
						break;
				case 4:
						$return = $days[3];
						break;
				case 5: 
						$return = $days[4];
						break;
				case 6: 
						$return = $days[5];
						break;
				case 0:
						$return = $days[6];
						break;
				}
			} else {
				switch($day_of_week){
				case 1: 
						$return = $days[0];
						break;
				case 2:
						$return = $days[1];
						break;
				case 3:
						$return = $days[2];
						break;
				case 4:
						$return = $days[3];
						break;
				case 5: 
						$return = $days[4];
						break;
				case 6: 
						$return = $days[5];
						break;
				case 7:
						$return = $days[6];
						break;
				}
			}
			return $return;
		}
		
		public function clean_db( $room_id ) {
			global $wpdb;	
			$date = date('Y-m-d');
			
			$wpdb->query("DELETE FROM {$this->booking_time_base}__$room_id WHERE day < $date");
		}
		
		public function get_data( $attr ) {
			global $wpdb;
			$data = array();
			$room_id = intval( $attr['id'] );

			$room = $wpdb->get_results("SELECT * FROM {$this->table_of_rooms} WHERE id = $room_id");
			
			if( !empty( $room ) ){
				$data['id']				= $room_id;
				$data['room']			= array_shift( $room );
				$data['has_room']		= true;
			} else {
				$data['has_room']	= false;	
			}
			$this->data = $data;
		}
		
		public function save_booking() {
			if( isset( $_POST['mi_booking_submit'] ) ) {
				global $wpdb;
				$room		= !empty( $_POST['booking_room'] ) ? esc_attr( $_POST['booking_room'] ) : "";
				$date		= !empty( $_POST['booking_date'] ) ? esc_attr( $_POST['booking_date'] ) : "";
				$time		= !empty( $_POST['booking_time'] ) ? esc_attr( $_POST['booking_time'] ) : "";
				$name		= !empty( $_POST['booking_name'] ) ? esc_attr( $_POST['booking_name'] ) : "";
				$tel		= !empty( $_POST['booking_tel'] ) ? esc_attr( $_POST['booking_tel'] ) : "";
				$email		= !empty( $_POST['booking_email'] ) ? esc_attr( $_POST['booking_email'] ) : "";
				$city		= !empty( $_POST['booking_city'] ) ? esc_attr( $_POST['booking_city'] ) : "";
				$ordered	= !empty( $_POST['booking_ordered'] ) ? esc_attr( $_POST['booking_ordered'] ) : "";
				
				$booking_table	= $this->booking_table_base . '__' . $room;
				$booking_time	= $this->booking_time_base . '__' . $room;

				$booking_result_mode = $wpdb->query('SELECT * FROM '.$booking_time.' WHERE day = "'.$date.'" && time = "'.$time.'" && available = 0;');

				if ($booking_result_mode != false)
				{
					$booking_res_string = $wpdb->get_results('SELECT * FROM '.$booking_time.' WHERE day = "'.$date.'" && time = "'.$time.'" && available = 0;');
					foreach ($booking_res_string as $items_res_string){
						$wpdb->update
						(
							$booking_time,
							array('available' => 1),
							array('id' => $items_res_string->id),
							array('%d'),
							array('%d')
						);
					}
					$wpdb->insert(
						$booking_table,
						array( 'name_of_customer' => $name, 'date_order' => $date, 'time_order' => $time, 'city_order' => $city, 'what_order' => $ordered, 'e_mail' => $email, 'phone' => $tel ),
						array( '%s', '%s', '%s', '%s', '%s', '%s', '%s'  )
					);
					
					$message = __( 'Name', 'mi_booking' ).": ".$name."\n\r".
					__( 'Email', 'mi_booking' ).": ".$email."\n\r".
					__( 'Phone', 'mi_booking' ).": ".$tel."\n\r".
					__( 'City', 'mi_booking' ).": ".$city."\n\r".
					__( 'Ordered', 'mi_booking' ).": ".$ordered." ".__( 'on', 'mi_booking' )." ".$date." ".__( 'in', 'mi_booking' )." ".$time;
					$set = array(
						'email'		=> $this->mi_booking['booking_email'],
						'subject'	=> __( 'Booking on site', 'mi_booking' ). ': ' . get_site_url(),
						'message'	=> $message,
					);
					$this->send_user_email( $set );
					
				}


			}
		}
		
		public function script() {
			wp_register_script( 'mi-front-script', MI_Booking_URL . 'js/script.js', array( 'jquery' ) );
			
			$translation_array = array(
				'tamplate_booking_conf' => $this->mi_booking['tamplate_booking_conf'],
				'tamplate_booking_mail' => $this->mi_booking['tamplate_booking_mail'],
				'tamplate_booking__tel' => $this->mi_booking['tamplate_booking__tel'],
			);
			wp_localize_script( 'mi-front-script', 'mi_booking', $translation_array );

			wp_enqueue_script( 'mi-front-script' );
		}
		
		public function footer() {
			$room_id	= !empty( $this->data['id'] ) ? $this->data['id'] : -1;
			$show_town	= !empty( $this->data['room'] ) ? $this->data['room']->show_town : '';
			?>
			<div id="envelope" class="envelope" style="background:#fff;z-index: 1002; display: none; position: fixed; top: 25%; padding: 20px; margin: 0px auto;">
				<p class="close-btn" title="<?php _e( 'Close', 'mi_booking' ); ?>" style="text-align: right;" ><?php _e( 'Close', 'mi_booking' ); ?> </p>
				<form id="booking_form" method="post" action="">
					<input type="hidden" name="booking_room" value="<?php echo $room_id;?>" />
					<table class="forma">
						<tbody>
							<tr>
								<td><?php _e( 'Date', 'mi_booking' ); ?></td>
								<td><span><input type="text" class="booking_date" name="booking_date" value="" size="30" readonly="readonly"></span></td>
							</tr>
							<tr>
								<td><?php _e( 'Picked date', 'mi_booking' ); ?></td>
								<td><span ><input type="text" class="booking_time" name="booking_time" value="" size="30" readonly="readonly"></span></td>
							</tr>
							<tr>
								<td><?php _e( 'Name', 'mi_booking' ); ?></td>
								<td><span><input type="text" name="booking_name" value="" size="30" ></span></td>
							</tr>
							<tr>
								<td><?php _e( 'Phone', 'mi_booking' ); ?></td>
								<td><span ><input type="tel" class="booking_tel" name="booking_tel" value="" size="30"></span></td>
							</tr>
							<tr>
								<td><?php _e( 'Е-mail', 'mi_booking' ); ?></td>
								<td><span><input type="email" class="booking_email" name="booking_email" value="" size="30" ></span></td>
							</tr>
							<?php if ( $show_town == '1'): ?>
							<tr>
								<td><?php _e( 'City', 'mi_booking' ); ?></td>
								<td><span><input type="text" name="booking_city" value="" size="30" ></span></td>
							</tr>
							<?php endif; ?>
							<tr>
								<td><?php _e( 'Ordered', 'mi_booking' ); ?></td>
								<td><span><input type="text" name="booking_ordered" value="<?php the_title(); ?>" size="30" readonly="readonly"></span></td>
							</tr>
							<tr>
								<td></td>
								<td><input class="booking_submit" type="submit" name="mi_booking_submit" value="<?php _e( 'Book', 'mi_booking' ); ?>" ></td>
							</tr>
						</tbody>
					</table>
				</form>
			</div>
			<div id="fade" style="background-color: black;bottom: 0;display: none;height: 100%;left: 0;opacity: 0.7;position: fixed;right: 0;top: 0;width: 100%;z-index: 1001;"></div>
			<?php 
		}
		
	}

}