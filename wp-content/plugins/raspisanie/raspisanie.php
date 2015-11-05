<?php
/*
Plugin Name: Расписание
Plugin URI: 
Description: Плагин расписания
Version: 1.0
Author: Мудрик Иван
Author URI: https://www.fl.ru/users/FlashSkyline/
License: 
*/

if ( ! defined( 'ABSPATH' ) ) die('<h3>Нет доступа к файлу!</h3>');



function raspisanie_NowDayOfWeek($NowDayOfWeek)
{
	$DaysOfWeek = array("Понедельник", "Вторник", "Среда", "Четверг", "Пятница", "Суббота", "Воскресенье");
	if ($NowDayOfWeek > 7)
	{
		$NowDayOfWeek = $NowDayOfWeek % 7;
		switch($NowDayOfWeek){
		case 1: 
				$iNowDayOfWeek = $DaysOfWeek[0];
				break;
		case 2:
				$iNowDayOfWeek = $DaysOfWeek[1];
				break;
		case 3:
				$iNowDayOfWeek = $DaysOfWeek[2];
				break;
		case 4:
				$iNowDayOfWeek = $DaysOfWeek[3];
				break;
		case 5: 
				$iNowDayOfWeek = $DaysOfWeek[4];
				break;
		case 6: 
				$iNowDayOfWeek = $DaysOfWeek[5];
				break;
		case 0:
				$iNowDayOfWeek = $DaysOfWeek[6];
				break;
		}
	}
	else 
	{
		switch($NowDayOfWeek){
		case 1: 
				$iNowDayOfWeek = $DaysOfWeek[0];
				break;
		case 2:
				$iNowDayOfWeek = $DaysOfWeek[1];
				break;
		case 3:
				$iNowDayOfWeek = $DaysOfWeek[2];
				break;
		case 4:
				$iNowDayOfWeek = $DaysOfWeek[3];
				break;
		case 5: 
				$iNowDayOfWeek = $DaysOfWeek[4];
				break;
		case 6: 
				$iNowDayOfWeek = $DaysOfWeek[5];
				break;
		case 7:
				$iNowDayOfWeek = $DaysOfWeek[6];
				break;
		}
	}
	
	
	return $iNowDayOfWeek;
}
function raspisanie_NowMonth($iNowMonth)
{
	if ($iNowMonth > 12)
	{
		switch($iNowMonth % 12){
		case '1': 
				$NowMonth = 'Января';
				break;
		case '2':
				$NowMonth = 'Февраля';
				break;
		case '3':
				$NowMonth ='Марта';
				break;
		case '4':
				$NowMonth = 'Апреля';
				break;
		case '5': 
				$NowMonth ='Мая';
				break;
		case '6': 
				$NowMonth = 'Июня';
				break;
		case '7':
				$NowMonth = 'Июля';
				break;
		case '8':
				$NowMonth ='Августа';
				break;
		case '9':
				$NowMonth = 'Сентября';
				break;
		case '10': 
				$NowMonth ='Октября';
				break;
		case '11': 
				$NowMonth = 'Ноября';
				break;
		case '0':
				$NowMonth = 'Декабря';
				break;
		}
	}
	else 
	{
		switch($iNowMonth){
		case '1': 
				$NowMonth = 'Января';
				break;
		case '2':
				$NowMonth = 'Февраля';
				break;
		case '3':
				$NowMonth ='Марта';
				break;
		case '4':
				$NowMonth = 'Апреля';
				break;
		case '5': 
				$NowMonth ='Мая';
				break;
		case '6': 
				$NowMonth = 'Июня';
				break;
		case '7':
				$NowMonth = 'Июля';
				break;
		case '8':
				$NowMonth ='Августа';
				break;
		case '9':
				$NowMonth = 'Сентября';
				break;
		case '10': 
				$NowMonth ='Октября';
				break;
		case '11': 
				$NowMonth = 'Ноября';
				break;
		case '12':
				$NowMonth = 'Декабря';
				break;
		}
	}
	return $NowMonth;
}

function raspisanie_ocistka_DB($raspisanie_time)
{
	global $wpdb;
	
	
	$NowDate = date('Y-m-d');
	$wpdb->query('DELETE FROM '.$raspisanie_time.' WHERE day < "'.$NowDate.'"');
}

function raspisanie_ocistka_DB_zakazow($raspisanie_table)
{
	global $wpdb;
	
	
	$NowDate = date('Y-m-d');
	$wpdb->query('DELETE FROM '.$raspisanie_table.' WHERE data_zakaza < "'.$NowDate.'"');
}



function raspisanie_func($attr) {
	global $wpdb;

	
	
	$raspiasnie_table_of_holidays = $wpdb->prefix."raspiasnie_table_of_holidays";
	$raspisanie_table_of_rooms = $wpdb->prefix."raspisanie_table_of_rooms";
	$raspisanie_table_of_rooms_id = $attr['id'];
	
	$raspisanie_table_of_rooms_result = $wpdb->get_results("SELECT * FROM $raspisanie_table_of_rooms WHERE id = $raspisanie_table_of_rooms_id");
	$raspisanie_table_of_rooms_result_query = $wpdb->query("SELECT * FROM $raspisanie_table_of_rooms WHERE id = $raspisanie_table_of_rooms_id");
	$raspisanie_table = $wpdb->prefix.$raspisanie_table_of_rooms_result[0]->name_of_zakaz_table;
	$raspisanie_time = $wpdb->prefix.$raspisanie_table_of_rooms_result[0]->name_of_time_table;
	$raspisanie_time_tamplate = $wpdb->prefix.$raspisanie_table_of_rooms_result[0]->name_of_time_tamplate;
	
	if ($raspisanie_table_of_rooms_result_query != false )
	{
	
	$raspisanie_number_of_days = $raspisanie_table_of_rooms_result[0]->number_of_days;
	list($iNowYear, $iNowMonth, $iNowDay, $NowDayOfWeek) = explode('-', date('Y-m-d-w'));

	$iNowDay_for_check = $iNowDay;
	$iNowMonth_for_check = $iNowMonth;

	$iTimestamp = mktime(0, 0, 0, $iNowMonth, $iNowDay, $iNowYear);
	list($sMonthName, $iDaysInMonth) = explode('-', date('F-t', $iTimestamp));
	
	$raspisanie_time_tamplate_data = $wpdb->get_results("SELECT * FROM $raspisanie_time_tamplate ORDER BY what_day,time;");	
	
	if ($NowDayOfWeek == 0)
	{
		$NowDayOfWeek = 7;
	}
	
	raspisanie_ocistka_DB_zakazow($raspisanie_table);
	raspisanie_ocistka_DB($raspisanie_time);
	$raspisanie_total_result .= '<table width="100%">';
	

$end_of_calendar = true;
$raspisanie_number_of_days_add_iNowDay = $raspisanie_number_of_days + $iNowDay;
$raspisanie_number_of_days_add_iNowDay -= 1;
$raspisanie_number_of_days_calc = $raspisanie_number_of_days_add_iNowDay;


$month_count = 0;

for ($i = $iNowMonth; $end_of_calendar; $i++) {
		if ($i > 12 )
		{
			$i = 1;
			$iNowYear++;
		}
		$iTimestamp = mktime(0, 0, 0, $i, $iNowDay, $iNowYear);
		list($sMonthName, $iDaysInMonth) = explode('-', date('F-t', $iTimestamp));
		$res_NowMonth = raspisanie_NowMonth($iNowMonth++);
		
		if ($raspisanie_number_of_days_add_iNowDay <= $iDaysInMonth)
		{
			$end_of_calendar = false;
			$iDaysInMonth = $raspisanie_number_of_days_add_iNowDay;
		}
		else
		{
			
			
			
			
			if ($raspisanie_number_of_days_calc > $iDaysInMonth)
			{
				$get_month_count = $month_count+1;
				
			}
			else
			{
				$iDaysInMonth = $raspisanie_number_of_days_calc;
			}
			$raspisanie_number_of_days_calc = $raspisanie_number_of_days_calc - $iDaysInMonth;
		}
		
		if ($month_count == $get_month_count)
		{
			$end_of_calendar = false;
			
		}
		$month_count++;
		
		
		
		for ($j = $iNowDay; $j <= $iDaysInMonth; $j++) {
		$res_now_week = raspisanie_NowDayOfWeek($NowDayOfWeek);
		
	
		$raspisanie_total_result .= '
			  <tr>
				<td align="left">'.$j.' '. $res_NowMonth.'<br><i>'.$res_now_week.'</i></td>';
				if ($j<10)
				{
					if ($j == $iNowDay_for_check && $i == $iNowMonth_for_check)
					{
						$current_day = $j;
					}
					else
					{
						$current_day = '0'.$j;
					}
				}
				else
				{
					$current_day = $j;
				}
				if (($i<10) && ($i != $iNowMonth_for_check))
				{
					$current_month = '0'.$i;
				}
				else
				{
					$current_month = $i;
				}
				$rasp_now_day_comp = $current_day.'.'.$current_month;	
				$raspisanie_holiday_result = $wpdb->query("SELECT * FROM $raspiasnie_table_of_holidays WHERE date_of_holiday='$rasp_now_day_comp';");
				
				if ($raspisanie_holiday_result == false)
				{
				if ($NowDayOfWeek > 7 ) 
				{
					if ($NowDayOfWeek % 7 == 5 || $NowDayOfWeek % 7  == 6 || $NowDayOfWeek % 7  == 0 )
					{
						if ($NowDayOfWeek % 7 == 5)
						{
							$raspisanie_time_tamplate_data = $wpdb->get_results("SELECT * FROM $raspisanie_time_tamplate where what_day = 'BP' ORDER BY time 
");
							foreach ($raspisanie_time_tamplate_data as $items)
							{
								$result = $wpdb->query('SELECT * FROM '.$raspisanie_time.' WHERE day = "'.$iNowYear.'-'.$i.'-'.$j.'" && time = "'.$items->time.'" && aveliable = 0;');
								if ($result != false)
								{
									$raspisanie_total_result .= '
									<td class="avelable" align="center" time="'.$items->time.'" date="'.$iNowYear.'-'.$i.'-'.$j.'">'.$items->time.'<br><hr style="margin-bottom:0px">'.$items->cost.'</td>';
								}
								else
								{
									$result = $wpdb->query('SELECT * FROM '.$raspisanie_time.' WHERE day = "'.$iNowYear.'-'.$i.'-'.$j.'" && time = "'.$items->time.'" && aveliable = 1;');
									
									if ($result != false)
									{
										$raspisanie_total_result .= '
										<td class="" align="center" time="'.$items->time.'" date="'.$iNowYear.'-'.$i.'-'.$j.'">'.$items->time.'<br><hr style="margin-bottom:0px">'.$items->cost.'</td>';
									}
									else
									{
										$raspisanie_total_result .= '
										<td class="avelable" align="center" time="'.$items->time.'" date="'.$iNowYear.'-'.$i.'-'.$j.'">'.$items->time.'<br><hr style="margin-bottom:0px">'.$items->cost.'</td>';
										
										$raspisanie_data = ''.$iNowYear.'-'.$i.'-'.$j.'';
										$items_time = ''.$items->time.'';
										$wpdb->insert(
											$raspisanie_time,
											array( 'day' => $raspisanie_data, 'time' => $items_time ),
											array( '%s', '%s'  )
										);
									}
								}
							}
						}
						else {
							$raspisanie_time_tamplate_data = $wpdb->get_results("SELECT * FROM $raspisanie_time_tamplate where what_day = 'V' ORDER BY time 
");
							foreach ($raspisanie_time_tamplate_data as $items)
							{
								$result = $wpdb->query('SELECT * FROM '.$raspisanie_time.' WHERE day = "'.$iNowYear.'-'.$i.'-'.$j.'" && time = "'.$items->time.'" && aveliable = 0;');
								if ($result != false)
								{
									$raspisanie_total_result .= '
									<td class="avelable" align="center" time="'.$items->time.'" date="'.$iNowYear.'-'.$i.'-'.$j.'">'.$items->time.'<br><hr style="margin-bottom:0px">'.$items->cost.'</td>';
								}
								else
								{
									$result = $wpdb->query('SELECT * FROM '.$raspisanie_time.' WHERE day = "'.$iNowYear.'-'.$i.'-'.$j.'" && time = "'.$items->time.'" && aveliable = 1;');
									
									if ($result != false)
									{
										$raspisanie_total_result .= '
										<td class="" align="center" time="'.$items->time.'" date="'.$iNowYear.'-'.$i.'-'.$j.'">'.$items->time.'<br><hr style="margin-bottom:0px">'.$items->cost.'</td>';
									}
									else
									{
										$raspisanie_total_result .= '
										<td class="avelable" align="center" time="'.$items->time.'" date="'.$iNowYear.'-'.$i.'-'.$j.'">'.$items->time.'<br><hr style="margin-bottom:0px">'.$items->cost.'</td>';
										
										$raspisanie_data = ''.$iNowYear.'-'.$i.'-'.$j.'';
										$items_time = ''.$items->time.'';
										$wpdb->insert(
											$raspisanie_time,
											array( 'day' => $raspisanie_data, 'time' => $items_time ),
											array( '%s', '%s'  )
										);
									}
								}
							}
						}
					}
					else
					{
							$raspisanie_time_tamplate_data = $wpdb->get_results("SELECT * FROM $raspisanie_time_tamplate where what_day = 'B' ORDER BY time 
");
							foreach ($raspisanie_time_tamplate_data as $items)
							{
								$result = $wpdb->query('SELECT * FROM '.$raspisanie_time.' WHERE day = "'.$iNowYear.'-'.$i.'-'.$j.'" && time = "'.$items->time.'" && aveliable = 0;');
								if ($result != false)
								{
									$raspisanie_total_result .= '
									<td class="avelable" align="center" time="'.$items->time.'" date="'.$iNowYear.'-'.$i.'-'.$j.'">'.$items->time.'<br><hr style="margin-bottom:0px">'.$items->cost.'</td>';
								}
								else
								{
									$result = $wpdb->query('SELECT * FROM '.$raspisanie_time.' WHERE day = "'.$iNowYear.'-'.$i.'-'.$j.'" && time = "'.$items->time.'" && aveliable = 1;');
									
									if ($result != false)
									{
										$raspisanie_total_result .= '
										<td class="" align="center" time="'.$items->time.'" date="'.$iNowYear.'-'.$i.'-'.$j.'">'.$items->time.'<br><hr style="margin-bottom:0px">'.$items->cost.'</td>';
									}
									else
									{
										$raspisanie_total_result .= '
										<td class="avelable" align="center" time="'.$items->time.'" date="'.$iNowYear.'-'.$i.'-'.$j.'">'.$items->time.'<br><hr style="margin-bottom:0px">'.$items->cost.'</td>';
										
										$raspisanie_data = ''.$iNowYear.'-'.$i.'-'.$j.'';
										$items_time = ''.$items->time.'';
										$wpdb->insert(
											$raspisanie_time,
											array( 'day' => $raspisanie_data, 'time' => $items_time ),
											array( '%s', '%s'  )
										);
									}
								}
							}
					}
				}
				else 
				{
					if ($NowDayOfWeek == 5 || $NowDayOfWeek == 6 || $NowDayOfWeek == 7 )
					{
						if ($NowDayOfWeek == 5)
						{
							$raspisanie_time_tamplate_data = $wpdb->get_results("SELECT * FROM $raspisanie_time_tamplate where what_day = 'BP' ORDER BY time ");
							
							foreach ($raspisanie_time_tamplate_data as $items)
							{
								$result = $wpdb->query('SELECT * FROM '.$raspisanie_time.' WHERE day = "'.$iNowYear.'-'.$i.'-'.$j.'" && time = "'.$items->time.'" && aveliable = 0;');
								if ($result != false)
								{
									$raspisanie_total_result .= '
									<td class="avelable" align="center" time="'.$items->time.'" date="'.$iNowYear.'-'.$i.'-'.$j.'">'.$items->time.'<br><hr style="margin-bottom:0px">'.$items->cost.'</td>';
								}
								else
								{
									$result = $wpdb->query('SELECT * FROM '.$raspisanie_time.' WHERE day = "'.$iNowYear.'-'.$i.'-'.$j.'" && time = "'.$items->time.'" && aveliable = 1;');
									
									if ($result != false)
									{
										$raspisanie_total_result .= '
										<td class="" align="center" time="'.$items->time.'" date="'.$iNowYear.'-'.$i.'-'.$j.'">'.$items->time.'<br><hr style="margin-bottom:0px">'.$items->cost.'</td>';
									}
									else
									{
										$raspisanie_total_result .= '
										<td class="avelable" align="center" time="'.$items->time.'" date="'.$iNowYear.'-'.$i.'-'.$j.'">'.$items->time.'<br><hr style="margin-bottom:0px">'.$items->cost.'</td>';
										
										$raspisanie_data = ''.$iNowYear.'-'.$i.'-'.$j.'';
										$items_time = ''.$items->time.'';
										$wpdb->insert(
											$raspisanie_time,
											array( 'day' => $raspisanie_data, 'time' => $items_time ),
											array( '%s', '%s'  )
										);
									}
								}
							}
						}
						else {
							$raspisanie_time_tamplate_data = $wpdb->get_results("SELECT * FROM $raspisanie_time_tamplate where what_day = 'V' ORDER BY time 
");
							foreach ($raspisanie_time_tamplate_data as $items)
							{
								$result = $wpdb->query('SELECT * FROM '.$raspisanie_time.' WHERE day = "'.$iNowYear.'-'.$i.'-'.$j.'" && time = "'.$items->time.'" && aveliable = 0;');
								if ($result != false)
								{
									$raspisanie_total_result .= '
									<td class="avelable" align="center" time="'.$items->time.'" date="'.$iNowYear.'-'.$i.'-'.$j.'">'.$items->time.'<br><hr style="margin-bottom:0px">'.$items->cost.'</td>';
								}
								else
								{
									$result = $wpdb->query('SELECT * FROM '.$raspisanie_time.' WHERE day = "'.$iNowYear.'-'.$i.'-'.$j.'" && time = "'.$items->time.'" && aveliable = 1;');
									
									if ($result != false)
									{
										$raspisanie_total_result .= '
										<td class="" align="center" time="'.$items->time.'" date="'.$iNowYear.'-'.$i.'-'.$j.'">'.$items->time.'<br><hr style="margin-bottom:0px">'.$items->cost.'</td>';
									}
									else
									{
										$raspisanie_total_result .= '
										<td class="avelable" align="center" time="'.$items->time.'" date="'.$iNowYear.'-'.$i.'-'.$j.'">'.$items->time.'<br><hr style="margin-bottom:0px">'.$items->cost.'</td>';
										
										$raspisanie_data = ''.$iNowYear.'-'.$i.'-'.$j.'';
										$items_time = ''.$items->time.'';
										$wpdb->insert(
											$raspisanie_time,
											array( 'day' => $raspisanie_data, 'time' => $items_time ),
											array( '%s', '%s'  )
										);
									}
								}
							}
						}
					}
					else
					{
						$raspisanie_time_tamplate_data = $wpdb->get_results("SELECT * FROM $raspisanie_time_tamplate where what_day = 'B' ORDER BY time 
");
							foreach ($raspisanie_time_tamplate_data as $items)
							{
								$result = $wpdb->query('SELECT * FROM '.$raspisanie_time.' WHERE day = "'.$iNowYear.'-'.$i.'-'.$j.'" && time = "'.$items->time.'" && aveliable = 0;');
								if ($result != false)
								{
									$raspisanie_total_result .= '
									<td class="avelable" align="center" time="'.$items->time.'" date="'.$iNowYear.'-'.$i.'-'.$j.'">'.$items->time.'<br><hr style="margin-bottom:0px">'.$items->cost.'</td>';
								}
								else
								{
									$result = $wpdb->query('SELECT * FROM '.$raspisanie_time.' WHERE day = "'.$iNowYear.'-'.$i.'-'.$j.'" && time = "'.$items->time.'" && aveliable = 1;');
									
									if ($result != false)
									{
										$raspisanie_total_result .= '
										<td class="" align="center" time="'.$items->time.'" date="'.$iNowYear.'-'.$i.'-'.$j.'">'.$items->time.'<br><hr style="margin-bottom:0px">'.$items->cost.'</td>';
									}
									else
									{
										$raspisanie_total_result .= '
										<td class="avelable" align="center" time="'.$items->time.'" date="'.$iNowYear.'-'.$i.'-'.$j.'">'.$items->time.'<br><hr style="margin-bottom:0px">'.$items->cost.'</td>';
										
										$raspisanie_data = ''.$iNowYear.'-'.$i.'-'.$j.'';
										$items_time = ''.$items->time.'';
										$wpdb->insert(
											$raspisanie_time,
											array( 'day' => $raspisanie_data, 'time' => $items_time ),
											array( '%s', '%s'  )
										);
									}
								}
							}
					}
				}
				$raspisanie_total_result .= '</tr>';
				$NowDayOfWeek++;
				}
				else
				{
					$raspisanie_time_holiday = $wpdb->get_results("SELECT * FROM $raspisanie_time_tamplate where what_day = 'P' ORDER BY time");
						foreach ($raspisanie_time_holiday as $items)
							{
								$result = $wpdb->query('SELECT * FROM '.$raspisanie_time.' WHERE day = "'.$iNowYear.'-'.$i.'-'.$j.'" && time = "'.$items->time.'" && aveliable = 0;');
								if ($result != false)
								{
									$raspisanie_total_result .= '
									<td class="avelable" align="center" time="'.$items->time.'" date="'.$iNowYear.'-'.$i.'-'.$j.'">'.$items->time.'<br><hr style="margin-bottom:0px">'.$items->cost.'</td>';
								}
								else
								{
									$result = $wpdb->query('SELECT * FROM '.$raspisanie_time.' WHERE day = "'.$iNowYear.'-'.$i.'-'.$j.'" && time = "'.$items->time.'" && aveliable = 1;');
									
									if ($result != false)
									{
										$raspisanie_total_result .= '
										<td class="" align="center" time="'.$items->time.'" date="'.$iNowYear.'-'.$i.'-'.$j.'">'.$items->time.'<br><hr style="margin-bottom:0px">'.$items->cost.'</td>';
									}
									else
									{
										$raspisanie_total_result .= '
										<td class="avelable" align="center" time="'.$items->time.'" date="'.$iNowYear.'-'.$i.'-'.$j.'">'.$items->time.'<br><hr style="margin-bottom:0px">'.$items->cost.'</td>';
										
										$raspisanie_data = ''.$iNowYear.'-'.$i.'-'.$j.'';
										$items_time = ''.$items->time.'';
										$wpdb->insert(
											$raspisanie_time,
											array( 'day' => $raspisanie_data, 'time' => $items_time ),
											array( '%s', '%s'  )
										);
									}
								}
							}$NowDayOfWeek++;
				}
				
		}//end for
		$iNowDay = 1;
	}
	$raspisanie_total_result .= '
	</table>';

	$raspisanie_total_result .= "
		<script src='http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js' type='text/javascript'></script> ";
		
		$raspisanie_total_result .= '<script type="text/javascript">
			 var this_avelable = undefined;
			$(document).ready(function(){
				var jVal = {
			"check" : function() {
					var ele1 = $(".raspisanie_email");
					var pos1 = ele1.offset();
					var ele2 = $(".raspisanie_tel");
					var pos2 = ele2.offset();
					
					
					var patt1 = /^.+@.+[.].{2,}$/i;
					var patt2 = /^\+7+[0-9]{10}$/i;
					
					if(!patt1.test(ele1.val()))  {
						jVal.errors = true;
							alert("'.get_option('raspisanie_bron_mail').'");	
							
					} else {
						if(!patt2.test(ele2.val()))  {
							jVal.errors = true;
							alert("'.get_option('raspisanie_bron_tel').'");
						}
						else 
						{
							alert("'.get_option('raspisanie_bron_conf').'");
						}
					}
				},
				
				"sendIt" : function (){
					if(!jVal.errors) {
						$("#raspisanie_form").submit();
						this_avelable.removeClass();
						
						}
					}
				};
			
				
				$(".avelable").click(function(){
					this_avelable = $(this);
					$(".data_zakaza").attr("value" ,$(this).attr("date"));
					$(".vremya_zakaza").attr("value" , $(this).attr("time"));
					document.getElementById("envelope").style.display="block";document.getElementById("fade").style.display="block";
					
				});
				$(".raspisanie_submit").click(function(){
					jVal.errors = false;
					jVal.check();
					
					jVal.sendIt();
					if(jVal.errors) {
					return false;
					
					}
					
				});
				$(".avelable").css({
				  "cursor" : "pointer",
				  "color" : "green"
				});
				$(".close-btn").click(function(){

					document.getElementById("envelope").style.display="none";document.getElementById("fade").style.display="none"
				});
				$(".close-btn").css({
				  "cursor" : "pointer",
				});
				
				 
				
			});
		</script>
		
	';
	
	$raspisanie_raspisanie_chto_zakazal = wp_title("", false);
	$raspisanie_total_result .= '
		<div id="envelope" class="envelope" style="background:#fff;z-index: 1002; display: none; position: fixed; top: 25%; padding: 20px; margin: 0px auto;">
			<p class="close-btn" title="Закрыть" style="text-align: right;" >Закрыть </p>
			<form id="raspisanie_form" method="post" action="">
				<table class="forma">
					<tbody><tr>
					<td>Дата</td>
					<td><span><input type="text" class="data_zakaza" name="raspisanie_date" value="" size="30" readonly="readonly"></span></td>
					</tr>
					<tr>
					<td>Время которое выбрали</td>
					<td><span ><input type="text" class="vremya_zakaza" name="raspisanie_time" value="" size="30" readonly="readonly"></span></td>
					</tr>
					<tr>
					<td>Имя</td>
					<td><span><input type="text" name="raspisanie_name" value="" size="30" ></span></td>
					</tr>
					<tr>
					<td>Телефон</td>
					<td><span ><input type="tel" class="raspisanie_tel" name="raspisanie_tel" value="+7" size="30"></span></td>
					</tr>
					<tr>
					<td>Е-mail</td>
					<td><span><input type="email" class="raspisanie_email" name="raspisanie_email" value="" size="30" ></span></td>
					</tr>';
					if ($raspisanie_table_of_rooms_result[0]->show_town == '1')
					{
						$raspisanie_total_result .= '<tr>
						<td>Город</td>
						<td><span><input type="text" name="raspisanie_gorod" value="" size="30" ></span></td>
						</tr>';
					}
					$raspisanie_total_result .= '
					<tr>
					<td>Что заказано</td>
					<td><span><input type="text" name="raspisanie_chto_zakazal" value="'.$raspisanie_raspisanie_chto_zakazal.'" size="30" readonly="readonly"></span></td>
					</tr>
					<tr>
					<td></td>
					<td><input class="raspisanie_submit" type="submit" name="raspisanie_text_zakaz" value="Забронировать" ></td>
					</tr>
					</tbody></table>
			</form>
		</div>
		<div id="fade" style="background-color: black;bottom: 0;display: none;height: 100%;left: 0;opacity: 0.7;position: fixed;right: 0;top: 0;width: 100%;z-index: 1001;"></div>
	';
	
	
	if (isset($_POST['raspisanie_text_zakaz']))
	{

		$raspisanie_data_zakaza = $_POST['raspisanie_date'];
		$raspisanie_time_from_form = $_POST['raspisanie_time'];
		$raspisanie_name  = $_POST['raspisanie_name'];
		$raspisanie_tel  = $_POST['raspisanie_tel'];
		$raspisanie_email_from_form  = $_POST['raspisanie_email'];
		$raspisanie_gorod  = $_POST['raspisanie_gorod'];
		$raspisanie_chto_zakazal  = $_POST['raspisanie_chto_zakazal'];
		
		
		$raspisanie_result_mode = $wpdb->query('SELECT * FROM '.$raspisanie_time.' WHERE day = "'.$raspisanie_data_zakaza.'" && time = "'.$raspisanie_time_from_form.'" && aveliable = 0;');
		$raspisanie_res_string = $wpdb->get_results('SELECT * FROM '.$raspisanie_time.' WHERE day = "'.$raspisanie_data_zakaza.'" && time = "'.$raspisanie_time_from_form.'" && aveliable = 0;');
		
		if ($raspisanie_result_mode != false)
		{
			foreach ($raspisanie_res_string as $items_res_string)
			{
				$wpdb->update
				(
					$raspisanie_time,
					array('aveliable' => 1),
					array('id' => $items_res_string->id),
					array('%d'),
					array('%d')
				);
			}
			$wpdb->insert(
					$raspisanie_table,
					array( 'name_of_zakazchik' => $raspisanie_name, 'data_zakaza' => $raspisanie_data_zakaza, 'time_zakaza' => $raspisanie_time_from_form, 'gorod_zakaza' => $raspisanie_gorod, 'chto_zakazano' => $raspisanie_chto_zakazal, 'e_mail' => $raspisanie_email_from_form, 'telefon' => $raspisanie_tel ),
					array( '%s', '%s', '%s', '%s', '%s', '%s', '%s'  )
					);
		}
		$raspisanie_message = "Имя: ".$raspisanie_name."\n\r".
		"Email: ".$raspisanie_email_from_form."\n\r".
		"Телефон: ".$raspisanie_tel."\n\r".
		"Город: ".$raspisanie_gorod."\n\r".
		"Заказано: ".$raspisanie_chto_zakazal." на ".$raspisanie_data_zakaza." в ".$raspisanie_time_from_form;
		
		$to = get_option('raspisanie_email');
		$subject = 'Бронирование на сайте';
		
		wp_mail($to, $subject, $raspisanie_message);
		
		//header('Location: .');
		
		
	}
	}
	else 
	{
		$raspisanie_total_result .= "<h2><b>Комнаты не существует!</b></h2>";
	}
	return $raspisanie_total_result;
}

//Панель администрирования
function raspisanie_admin_menu () {

	add_options_page('Настройка плагина расписания', 'Расписание','install_plugins','raspisanie','raspisanie_admin_func');
}
function raspisanie_admin_func() {
	global $wpdb;
	$raspisanie_report_tamplate = $wpdb->prefix."raspisanie_report_tamplate";
	$raspisanie_table_of_rooms = $wpdb->prefix."raspisanie_table_of_rooms";
	$raspiasnie_table_of_holidays = $wpdb->prefix."raspiasnie_table_of_holidays";
	$raspisanie_table_of_rooms_res = $wpdb->get_results("SELECT * FROM $raspisanie_table_of_rooms;");
	
	if ($raspisanie_table_of_rooms_res[0]->room_selected == "0") 
	{
		$raspisanie_table_of_rooms_res_name_of_zakaz_table = $raspisanie_table_of_rooms_res[0]->name_of_zakaz_table;
		$raspisanie_table_of_rooms_res_name_of_time_table = $raspisanie_table_of_rooms_res[0]->name_of_time_table;
		$raspisanie_table_of_rooms_res_name_of_time_tamplate = $raspisanie_table_of_rooms_res[0]->name_of_time_tamplate;
		$raspisanie_time_tamplate = $wpdb->prefix.$raspisanie_table_of_rooms_res_name_of_time_tamplate;
		$raspisanie_table = $wpdb->prefix.$raspisanie_table_of_rooms_res_name_of_zakaz_table;
		$raspisanie_time = $wpdb->prefix.$raspisanie_table_of_rooms_res_name_of_time_table;
	}
	else
	{
		$raspisanie_table_of_rooms_temp = $raspisanie_table_of_rooms_res[0]->room_selected;
		$raspisanie_table_of_rooms_result = $wpdb->get_results("SELECT * FROM $raspisanie_table_of_rooms WHERE name_of_room = '$raspisanie_table_of_rooms_temp'");
		foreach ($raspisanie_table_of_rooms_result as $raspisanie_table_of_rooms_result_items)
		{
		$raspisanie_table = $wpdb->prefix.$raspisanie_table_of_rooms_result_items->name_of_zakaz_table;
		$raspisanie_time = $wpdb->prefix.$raspisanie_table_of_rooms_result_items->name_of_time_table;
		$raspisanie_time_tamplate = $wpdb->prefix.$raspisanie_table_of_rooms_result_items->name_of_time_tamplate;
		}
	}
	
	
//настройка имейла
if (isset($_POST['raspisanie_email_change_button']))
{
	$raspisanie_email = $_POST['raspisanie_email'];
	update_option( 'raspisanie_email',  $raspisanie_email);
	
}


echo '
<h2>Настройка Имейла администратора</h2>
<form action="'.$_SERVER['PHP_SELF'].'?page=raspisanie" method="post">
<input style="width: 300px;" name="raspisanie_email" type="text" value="'.get_option('raspisanie_email').'"/><input name="raspisanie_email_change_button" type="submit" value="Изменить"/>
</form><br />
';

//Таблица комнат		

if (isset($_POST['raspisanie_room_reneme']))
	{
		$raspianie_room_id = $_POST['raspianie_room_id'];
		$raspisanie_room_reneme_value = $_POST['raspisanie_room_reneme_value'];
		
		$wpdb->update
			(
				$raspisanie_table_of_rooms,
				array('name_of_room' => $raspisanie_room_reneme_value),
				array('id' => $raspianie_room_id),
				array('%s'),
				array('%d')
			);
	}
if (isset($_POST['raspisanie_room_delete']))
	{
		$raspisanie_prefix =$wpdb->prefix;
		$raspianie_room_id = $_POST['raspianie_room_id'];
		$raspianie_room_delete = $wpdb->get_results("SELECT * FROM $raspisanie_table_of_rooms WHERE id = $raspianie_room_id ");
		
		$raspisanie_room_prev = $wpdb->query("SELECT * FROM $raspisanie_table_of_rooms WHERE id < $raspianie_room_id ORDER BY id DESC LIMIT 1;");
		$raspisanie_room_next = $wpdb->query("SELECT * FROM $raspisanie_table_of_rooms WHERE id > $raspianie_room_id ORDER BY id DESC LIMIT 1;");
		if ($raspisanie_room_prev)
		{
			$raspisanie_room_del_rsel = $wpdb->get_results("SELECT * FROM $raspisanie_table_of_rooms WHERE id < $raspianie_room_id ORDER BY id DESC LIMIT 1;");
		}
		else 
		{
			if ($raspisanie_room_next)
			{
				$raspisanie_room_del_rsel = $wpdb->get_results("SELECT * FROM $raspisanie_table_of_rooms WHERE id > $raspianie_room_id ORDER BY id DESC LIMIT 1;");
			}
		}
		
		$wpdb->update
			(
				$raspisanie_table_of_rooms,
				array('room_selected' => $raspisanie_room_del_rsel[0]->name_of_room),
				array('id' => $raspisanie_table_of_rooms_res[0]->id),
				array('%s'),
				array('%d')
			);
		
		$sql1 = "DROP TABLE `".$raspisanie_prefix.$raspianie_room_delete[0]->name_of_zakaz_table."`;";
		$sql2 = "DROP TABLE `".$raspisanie_prefix.$raspianie_room_delete[0]->name_of_time_table."`;";
		$sql3 = "DROP TABLE `".$raspisanie_prefix.$raspianie_room_delete[0]->name_of_time_tamplate."`;";
		
		$wpdb->query($sql1);
		$wpdb->query($sql2);
		$wpdb->query($sql3);
		
		$wpdb->query("DELETE FROM $raspisanie_table_of_rooms WHERE id = $raspianie_room_id");
	}

if (isset($_POST['raspianie_room_add']))
	{
	
	$raspisanie_table_of_rooms_res = $wpdb->get_results("SELECT * FROM $raspisanie_table_of_rooms;");
	
		$raspianie_room_add_form = $_POST['raspianie_room_add_form'];
		$raspisanie_find_match = true;
		$raspianie_room_id = $_POST['raspianie_room_id'];
		$raspisanie_room_reneme_value = $_POST['raspisanie_room_reneme_value'];
		
		$wpdb->update
			(
				$raspisanie_table_of_rooms,
				array('room_selected' => $raspianie_room_add_form),
				array('id' => $raspisanie_table_of_rooms_res[0]->id),
				array('%s'),
				array('%d')
			);
		
		
		for ($i=1;  $raspisanie_find_match ;$i++)
		{
			$result = $wpdb->query("SELECT * FROM $raspisanie_table_of_rooms WHERE name_of_zakaz_table = 'raspisanie_table__$i'");
			
			if ($result == false)
			{
				$raspisanie_matches_raspisanie_table = 'raspisanie_table__'.$i;
				$raspisanie_matches_raspisanie_time = 'raspisanie_time__'.$i;
				$raspisanie_matches_raspisanie_time_tamplate = 'raspisanie_time_tamplate__'.$i;
				
				$sql_raspisanie_table = "
							CREATE TABLE `".$wpdb->prefix.$raspisanie_matches_raspisanie_table."` (
							`id` INT(11) NOT NULL AUTO_INCREMENT,
							`name_of_zakazchik` VARCHAR(40) NOT NULL,
							`data_zakaza` CHAR(50) NOT NULL,
							`time_zakaza` CHAR(50) NOT NULL,
							`gorod_zakaza` VARCHAR(40) NOT NULL,
							`chto_zakazano` VARCHAR(40) NOT NULL,
							`e_mail` VARCHAR(40) NOT NULL,
							`telefon` VARCHAR(40) NOT NULL,
							`status_potverzdeniya` VARCHAR(50) NOT NULL DEFAULT 'Заказ не потврежден',
							PRIMARY KEY (`id`)
							)
							COLLATE='utf8_general_ci'
							ENGINE=InnoDB
							AUTO_INCREMENT=2;
						";
						
						$sql_raspisanie_time = "
							CREATE TABLE `".$wpdb->prefix.$raspisanie_matches_raspisanie_time."` (
							`id` INT(4) NOT NULL AUTO_INCREMENT,
							`day` DATE NOT NULL,
							`time` CHAR(50) NOT NULL,
							`aveliable` INT(11) NOT NULL DEFAULT '0',
							PRIMARY KEY (`id`)
							)
							COLLATE='utf8_general_ci'
							ENGINE=InnoDB
							AUTO_INCREMENT=2
							;

						";
						$sql_raspisanie_time_tamplate = "
							CREATE TABLE `".$wpdb->prefix.$raspisanie_matches_raspisanie_time_tamplate."` (
								`id` INT(11) NOT NULL AUTO_INCREMENT,
								`what_day` CHAR(50) NOT NULL,
								`time` CHAR(50) NOT NULL DEFAULT '0',
								`cost` CHAR(50) NOT NULL DEFAULT '0',
								PRIMARY KEY (`id`)
							)
							COLLATE='utf8_general_ci'
							ENGINE=InnoDB
							AUTO_INCREMENT=21
							;
						";
						$wpdb->query($sql_raspisanie_table);
						$wpdb->query($sql_raspisanie_time);
						$wpdb->query($sql_raspisanie_time_tamplate);
						
						$wpdb->insert(
							$raspisanie_table_of_rooms,
							array( 'name_of_room' => $raspianie_room_add_form, 'name_of_zakaz_table' => $raspisanie_matches_raspisanie_table,'name_of_time_table' => $raspisanie_matches_raspisanie_time, 'name_of_time_tamplate' => $raspisanie_matches_raspisanie_time_tamplate ),
							array( '%s', '%s' , '%s' , '%s' )
						);
						$raspisanie_find_match = false;
			}
		}
		
		
		
	}

if (isset($_POST['raspisanie_change_num_days']))
{
	$raspianie_room_id_num_days = $_POST['raspianie_room_id_num_days'];
	$raspisanie_num_days = $_POST['raspisanie_num_days'];
	$raspisanie_table_of_rooms_change_num_days = $wpdb->get_results("SELECT * FROM $raspisanie_table_of_rooms WHERE id = $raspianie_room_id_num_days;");
	
	$wpdb->update
			(
				$raspisanie_table_of_rooms,
				array('number_of_days' => $raspisanie_num_days),
				array('id' => $raspisanie_table_of_rooms_change_num_days[0]->id),
				array('%d'),
				array('%d')
			);
	
}	


	echo '
		
				  <table width="98%" border="0" style="text-align:left">
				  <tr style="background:#CCC">
					<th scope="col">Шорткод / Количество выводимых дней в комнате</th>
					<th scope="col">Имя комнаты</th>
				   <th scope="col">Изменить название комнаты</th>
				  </tr>
				  <tr>
					<td></td>
					
					<td colspan="2">
						<form action="'.$_SERVER['PHP_SELF'].'?page=raspisanie" method="post"><input name="raspianie_room_add_form" type="text" value="" /><input name="raspianie_room_add" type="submit" value="Добавить" />
						</form>
					</td>
					
				  </tr>
				 <tr>
				
	';

	if (isset($_POST['raspianie_room_change']))
	{
	$raspisanie_room_select = $_POST['raspisanie_room_select'];
	$raspiasnie_show_town = $_POST['raspiasnie_show_town'];

	$raspisanie_table_of_rooms_res_string = $wpdb->get_results("SELECT * FROM $raspisanie_table_of_rooms WHERE name_of_room = '$raspisanie_room_select';");
	$raspisanie_prefix =$wpdb->prefix;
	$raspisanie_table_of_rooms_res_string_all = $wpdb->get_results('SELECT * FROM '.$raspisanie_table_of_rooms.';');
	
	$wpdb->update
			(
				$raspisanie_table_of_rooms,
				array('room_selected' => $raspisanie_room_select),
				array('id' => $raspisanie_table_of_rooms_res_string_all[0]->id),
				array('%s'),
				array('%d')
			);
			foreach ($raspisanie_table_of_rooms_res_string as $items_res_string)
			{
				$raspisanie_table = $raspisanie_prefix.$items_res_string->name_of_zakaz_table;
				$raspisanie_time = $raspisanie_prefix.$items_res_string->name_of_time_table;
				
				echo '
					<td>
					<form action="'.$_SERVER['PHP_SELF'].'?page=raspisanie" method="post">
					<input type="text" value="[raspisanie id=&quot;'.$items_res_string->id.'&quot;]" size="30">
					<input type="hidden" name="raspianie_room_id_num_days" value="'.$items_res_string->id.'">
					<input type="text" name="raspisanie_num_days" value="'.$items_res_string->number_of_days.'" >
					<input name="raspisanie_change_num_days" type="submit" value="Изменить" >
					</form>	
					</td>
					<td>
					<form action="'.$_SERVER['PHP_SELF'].'?page=raspisanie" method="post">';
					if (($_REQUEST["raspiasnie_show_town"]=='on') && ($raspisanie_room_select == $raspisanie_table_of_rooms_res_string_all[0]->room_selected)) 
					{	
						
						echo '
						<span><input name="raspiasnie_show_town" type="checkbox" checked="checked" /> Отображать город </span>';
						
						$wpdb->update
							(
								$raspisanie_table_of_rooms,
								array('show_town' => '1'),
								array('id' => $items_res_string->id),
								array('%s'),
								array('%d')
							);
					} 
					else
					{
						if ($raspisanie_room_select == $raspisanie_table_of_rooms_res_string_all[0]->room_selected)
						{
							echo '
								<span><input name="raspiasnie_show_town" type="checkbox" /> Отображать город </span>';
								$wpdb->update
									(
										$raspisanie_table_of_rooms,
										array('show_town' => '0'),
										array('id' => $items_res_string->id),
										array('%s'),
										array('%d')
									);
						}
						else
						{
							if ($raspisanie_table_of_rooms_res_string[0]->show_town == 1)
							{
								echo '
								<span><input name="raspiasnie_show_town" type="checkbox" checked="checked" /> Отображать город </span>';
							
							}
							else
							{
								echo '
								<span><input name="raspiasnie_show_town" type="checkbox" /> Отображать город </span>';
							}
						}
					}
					
					echo '
					<input type="hidden" name="raspianie_room_id" value="'.$items_res_string->id.'">
					<select name="raspisanie_room_select" >
					';
			}
				
					
					
					 foreach ($raspisanie_table_of_rooms_res_string_all as $items_res_string_all)
					{
						if ($raspisanie_table_of_rooms_res_string[0]->name_of_room == $items_res_string_all->name_of_room)
						{
						echo '
								<option selected value="'.$items_res_string_all->name_of_room.'">'.$items_res_string_all->name_of_room.'</option>
								';
						}
						else 
						{
							echo '
								<option value="'.$items_res_string_all->name_of_room.'">'.$items_res_string_all->name_of_room.'</option>
								';
						}
					}
					echo '
		</select>
		<input name="raspianie_room_change" type="submit" value="Выбрать" /><input name="raspisanie_room_delete" type="submit" value="Удалить" /></form></td>
		
		<td><form action="'.$_SERVER['PHP_SELF'].'?page=raspisanie" method="post">
		<input type="hidden" name="raspianie_room_id" value="'.$raspisanie_table_of_rooms_res_string[0]->id.'">
		<input name="raspisanie_room_reneme_value" type="text" value="'.$raspisanie_table_of_rooms_res_string[0]->name_of_room.'" /> <input name="raspisanie_room_reneme" type="submit" value="Изменить" /></form></td>
		</tr>
	</table>
	</form>
';
		
	}
	else
	{
			
			$raspisanie_table_of_rooms_res_string_not_selected = $wpdb->get_results("SELECT * FROM $raspisanie_table_of_rooms;");
			
			if ($raspisanie_table_of_rooms_res_string_not_selected[0]->room_selected != '0')
			{
				
				$raspisanie_table_of_rooms_last_room = $raspisanie_table_of_rooms_res_string_not_selected[0]->room_selected;
				
				$raspisanie_table_of_rooms_res_string = 
				$wpdb->get_results("SELECT * FROM $raspisanie_table_of_rooms WHERE name_of_room = '$raspisanie_table_of_rooms_last_room';");
				
				echo '
					
					<td>
					<form action="'.$_SERVER['PHP_SELF'].'?page=raspisanie" method="post">
					<input type="text" value="[raspisanie id=&quot;'.$raspisanie_table_of_rooms_res_string[0]->id.'&quot;]" size="30">
					<input type="hidden" name="raspianie_room_id_num_days" value="'.$raspisanie_table_of_rooms_res_string[0]->id.'">
					<input type="text" name="raspisanie_num_days" value="'.$raspisanie_table_of_rooms_res_string[0]->number_of_days.'" >
					<input name="raspisanie_change_num_days" type="submit" value="Изменить" >
					</form>	
					</td>
					<td>
					<form action="'.$_SERVER['PHP_SELF'].'?page=raspisanie" method="post">';
					if ($raspisanie_table_of_rooms_res_string[0]->show_town == '1')
					{
					echo '
					<span><input name="raspiasnie_show_town" type="checkbox" checked="checked" /> Отображать город </span>';
					}
					else
					{
					echo '
					<span><input name="raspiasnie_show_town" type="checkbox" /> Отображать город </span>';
					}
					echo '
					<input type="hidden" name="raspianie_room_id" value="'.$raspisanie_table_of_rooms_res_string[0]->id.'">
					<select name="raspisanie_room_select" >
					';
					
					 foreach ($raspisanie_table_of_rooms_res_string_not_selected as $items_res_string_all)
					{

						if ($raspisanie_table_of_rooms_res_string[0]->name_of_room == $items_res_string_all->name_of_room)
						{
						echo '
								<option selected value="'.$items_res_string_all->name_of_room.'">'.$items_res_string_all->name_of_room.'</option>
								';
						}
						else 
						{
							echo '
								<option value="'.$items_res_string_all->name_of_room.'">'.$items_res_string_all->name_of_room.'</option>
								';
						}
						
						
					}
					echo '
					</select>
					<input name="raspianie_room_change" type="submit" value="Выбрать" /><input name="raspisanie_room_delete" type="submit" value="Удалить" /></form></td>
					
					<td><form action="'.$_SERVER['PHP_SELF'].'?page=raspisanie" method="post">';
					
					echo '
					<input type="hidden" name="raspianie_room_id" value="'.$raspisanie_table_of_rooms_res_string[0]->id.'">
					<input name="raspisanie_room_reneme_value" type="text" value="'.$raspisanie_table_of_rooms_res_string[0]->name_of_room.'" />';
					
					echo '
					<input name="raspisanie_room_reneme" type="submit" value="Изменить" /></form></td>
					</tr>
				</table>
				</form>
			';
			}
			else
			{
			
			echo '
					
					<td>
					<form action="'.$_SERVER['PHP_SELF'].'?page=raspisanie" method="post">
					<input type="text" value="[raspisanie id=&quot;'.$raspisanie_table_of_rooms_res_string_not_selected[0]->id.'&quot;]" size="30">
					<input type="hidden" name="raspianie_room_id_num_days" value="'.$raspisanie_table_of_rooms_res_string_not_selected[0]->id.'">
					<input type="text" name="raspisanie_num_days" value="'.$raspisanie_table_of_rooms_res_string_not_selected[0]->number_of_days.'" >
					<input name="raspisanie_change_num_days" type="submit" value="Изменить" >
					</form>	
					</td>
					<td>
					<form action="'.$_SERVER['PHP_SELF'].'?page=raspisanie" method="post">';
					if ($raspisanie_table_of_rooms_res_string_not_selected[0]->show_town == 1)
					{
					echo '
					<span><input name="raspiasnie_show_town" type="checkbox" checked="checked" /> Отображать город </span>';
					}
					else
					{
					echo '
					<span><input name="raspiasnie_show_town" type="checkbox" /> Отображать город </span>';
					}
					echo '
					<input type="hidden" name="raspianie_room_id" value="'.$raspisanie_table_of_rooms_res_string_not_selected[0]->id.'">
					<select name="raspisanie_room_select" >
					';
					
					 foreach ($raspisanie_table_of_rooms_res_string_not_selected as $items_res_string_all)
					{
						echo '
							<option value="'.$items_res_string_all->name_of_room.'">'.$items_res_string_all->name_of_room.'</option>
							';
					}
					echo '
					</select>
					<input name="raspianie_room_change" type="submit" value="Выбрать" /><input name="raspisanie_room_delete" type="submit" value="Удалить" /></form></td>
					
					<td><form action="'.$_SERVER['PHP_SELF'].'?page=raspisanie" method="post">
					<input type="hidden" name="raspianie_room_id" value="'.$raspisanie_table_of_rooms_res_string_not_selected[0]->id.'">
					<input name="raspisanie_room_reneme_value" type="text" value="'.$raspisanie_table_of_rooms_res_string_not_selected[0]->name_of_room.'" /> <input name="raspisanie_room_reneme" type="submit" value="Изменить" /></form></td>
					</tr>
				</table>
				</form>
			';
			}
			
}
	

	//Таблица комнат конец	

	
	
	

	if (isset($_POST['potverdit_zakaz']))
	{
	
	
	
	if ($raspisanie_table_of_rooms_res[0]->room_selected == "0") 
	{
		$raspisanie_table_of_rooms_res_name_of_zakaz_table = $raspisanie_table_of_rooms_res[0]->name_of_zakaz_table;
		$raspisanie_table_of_rooms_res_name_of_time_table = $raspisanie_table_of_rooms_res[0]->name_of_time_table;
		$raspisanie_table = $wpdb->prefix.$raspisanie_table_of_rooms_res_name_of_zakaz_table;
		$raspisanie_time = $wpdb->prefix.$raspisanie_table_of_rooms_res_name_of_time_table;
	}
	else
	{
		$raspisanie_table_of_rooms_temp = $raspisanie_table_of_rooms_res[0]->room_selected;
		$raspisanie_table_of_rooms_result = $wpdb->get_results("SELECT * FROM $raspisanie_table_of_rooms WHERE name_of_room = '$raspisanie_table_of_rooms_temp'");
		foreach ($raspisanie_table_of_rooms_result as $raspisanie_table_of_rooms_result_items)
		{
		$raspisanie_table = $wpdb->prefix.$raspisanie_table_of_rooms_result_items->name_of_zakaz_table;
		$raspisanie_time = $wpdb->prefix.$raspisanie_table_of_rooms_result_items->name_of_time_table;
		}
	}
	$raspisanie_table_of_rooms_res = $wpdb->get_results("SELECT * FROM $raspisanie_table_of_rooms;");
	$raspisanie_potverdit_zakaz = $_POST['potverdit_zakaz'];
	$raspisanie_otmenit_zakaz = $_POST['otmenit_zakaz'];
	$raspisanie_udalit_zakaz = $_POST['udalit_zakaz'];
	$raspisanie_id  = $_POST['raspisanie_id'];
		$wpdb->update
			(
				$raspisanie_table,
				array('status_potverzdeniya' => 'Заказ потвержден'),
				array('id' => $raspisanie_id),
				array('%s'),
				array('%d')
			);
		$raspisanie_data_to_mail = $wpdb->get_results("SELECT * FROM $raspisanie_table WHERE id = $raspisanie_id ");
		foreach ($raspisanie_data_to_mail as $items)
		{	
		$raspisanie_report_tamplate_tamplate_potvizdenie_send = $wpdb->get_results("SELECT * FROM $raspisanie_report_tamplate WHERE name_of_tamplate = 'tamplate_potvizdenie';");
		$potvizdenie_send_array = explode('-',$raspisanie_report_tamplate_tamplate_potvizdenie_send[0]->report_tamplate);
		$potvizdenie_send_array_count = count($potvizdenie_send_array);
		for ($i=0;$i < $potvizdenie_send_array_count;$i++)
		{
			switch ($potvizdenie_send_array[$i])
			{
				case 'name_of_zakazchik': $raspisanie_message .= $items->name_of_zakazchik; break;
				case 'chto_zakazano': $raspisanie_message .= $items->chto_zakazano; break;
				case 'data_zakaza': $raspisanie_message .= $items->data_zakaza; break;
				case 'time_zakaza':  $raspisanie_message .= $items->time_zakaza; break;
				case 'gorod_zakaza':
					if ($raspisanie_table_of_rooms_result[0]->show_town == "1")
					{
					$raspisanie_message .= 'город '.$items->gorod_zakaza.' и ';
					}
				break;
				case 'telefon': $raspisanie_message .= $items->telefon; break;

				default: $raspisanie_message .= $potvizdenie_send_array[$i];
			}
		}

		$raspisanie_email = $items->e_mail;
		}
		$headers = 'From: '.get_option('raspisanie_email')."\r\n".
		'Reply-To: '.get_option('raspisanie_email').'' . "\r\n" .
		'X-Mailer: PHP/' . phpversion();
		
		wp_mail($raspisanie_email, "Потверждение бронирования", $raspisanie_message, $headers);
	
	}
	if (isset($_POST['otmenit_zakaz']))
	{
	$raspisanie_id  = $_POST['raspisanie_id'];
	
	
	if ($raspisanie_table_of_rooms_res[0]->room_selected == "0") 
	{
		$raspisanie_table_of_rooms_res_name_of_zakaz_table = $raspisanie_table_of_rooms_res[0]->name_of_zakaz_table;
		$raspisanie_table_of_rooms_res_name_of_time_table = $raspisanie_table_of_rooms_res[0]->name_of_time_table;
		$raspisanie_table = $wpdb->prefix.$raspisanie_table_of_rooms_res_name_of_zakaz_table;
		$raspisanie_time = $wpdb->prefix.$raspisanie_table_of_rooms_res_name_of_time_table;
	}
	else
	{
		$raspisanie_table_of_rooms_temp = $raspisanie_table_of_rooms_res[0]->room_selected;
		$raspisanie_table_of_rooms_result = $wpdb->get_results("SELECT * FROM $raspisanie_table_of_rooms WHERE name_of_room = '$raspisanie_table_of_rooms_temp'");
		foreach ($raspisanie_table_of_rooms_result as $raspisanie_table_of_rooms_result_items)
		{
		$raspisanie_table = $wpdb->prefix.$raspisanie_table_of_rooms_result_items->name_of_zakaz_table;
		$raspisanie_time = $wpdb->prefix.$raspisanie_table_of_rooms_result_items->name_of_time_table;
		}
	}
	$raspisanie_table_of_rooms_res = $wpdb->get_results("SELECT * FROM $raspisanie_table_of_rooms;");
		$wpdb->update
			(
				$raspisanie_table,
				array('status_potverzdeniya' => 'Заказ отменен'),
				array('id' => $raspisanie_id),
				array('%s'),
				array('%d')
			);
		$raspisanie_data_to_mail = $wpdb->get_results("SELECT * FROM $raspisanie_table WHERE id = $raspisanie_id ");
		foreach ($raspisanie_data_to_mail as $items)
		{
		$raspisanie_report_tamplate_tamplate_potvizdenie_send = $wpdb->get_results("SELECT * FROM $raspisanie_report_tamplate WHERE name_of_tamplate = 'tamplate_otmena';");
		$otmena_send_array = explode('-',$raspisanie_report_tamplate_tamplate_potvizdenie_send[0]->report_tamplate);
		$otmena_send_array_count = count($otmena_send_array);
		for ($i=0;$i < $otmena_send_array_count;$i++)
		{
			switch ($otmena_send_array[$i])
			{
				case 'name_of_zakazchik': $raspisanie_message .= $items->name_of_zakazchik; break;
				case 'chto_zakazano': $raspisanie_message .= $items->chto_zakazano; break;
				case 'data_zakaza': $raspisanie_message .= $items->data_zakaza; break;
				case 'time_zakaza':  $raspisanie_message .= $items->time_zakaza; break;
				case 'gorod_zakaza':
					if ($raspisanie_table_of_rooms_result[0]->show_town == "1")
					{
					$raspisanie_message .= 'город '.$items->gorod_zakaza.' и ';
					}
				break;
				case 'telefon': $raspisanie_message .= $items->telefon; break;

				default: $raspisanie_message .= $otmena_send_array[$i];
			}
		}
		

		$raspisanie_email = $items->e_mail;
		
		$headers = 'From: '.get_option('raspisanie_email')."\r\n".
		'Reply-To: '.get_option('raspisanie_email').'' . "\r\n" .
		'X-Mailer: PHP/' . phpversion();
		
		wp_mail($raspisanie_email, "Бронирование отменено", $raspisanie_message, $headers);
			
		$raspisanie_result_mode = $wpdb->query('SELECT * FROM '.$raspisanie_time.' WHERE day = "'.$items->data_zakaza.'" && time = "'.$items->time_zakaza.'";');
		$raspisanie_res_string = $wpdb->get_results('SELECT * FROM '.$raspisanie_time.' WHERE day = "'.$items->data_zakaza.'" && time = "'.$items->time_zakaza.'";');
		
		
			foreach ($raspisanie_res_string as $items_res_string)
			{
				$wpdb->update
				(
					$raspisanie_time,
					array('aveliable' => 0),
					array('id' => $items_res_string->id),
					array('%d'),
					array('%d')
				);
			}		
		}
	}
	if (isset($_POST['udalit_zakaz']))
	{
	$raspisanie_id  = $_POST['raspisanie_id'];
	
	
	if ($raspisanie_table_of_rooms_res[0]->room_selected == "0") 
	{
		$raspisanie_table_of_rooms_res_name_of_zakaz_table = $raspisanie_table_of_rooms_res[0]->name_of_zakaz_table;
		$raspisanie_table_of_rooms_res_name_of_time_table = $raspisanie_table_of_rooms_res[0]->name_of_time_table;
		$raspisanie_table = $wpdb->prefix.$raspisanie_table_of_rooms_res_name_of_zakaz_table;
		$raspisanie_time = $wpdb->prefix.$raspisanie_table_of_rooms_res_name_of_time_table;
	}
	else
	{
		$raspisanie_table_of_rooms_temp = $raspisanie_table_of_rooms_res[0]->room_selected;
		$raspisanie_table_of_rooms_result = $wpdb->get_results("SELECT * FROM $raspisanie_table_of_rooms WHERE name_of_room = '$raspisanie_table_of_rooms_temp'");
		foreach ($raspisanie_table_of_rooms_result as $raspisanie_table_of_rooms_result_items)
		{
		$raspisanie_table = $wpdb->prefix.$raspisanie_table_of_rooms_result_items->name_of_zakaz_table;
		$raspisanie_time = $wpdb->prefix.$raspisanie_table_of_rooms_result_items->name_of_time_table;
		}
	}
	$raspisanie_table_of_rooms_res = $wpdb->get_results("SELECT * FROM $raspisanie_table_of_rooms;");
	
		$raspisanie_data_to_mail = $wpdb->get_results("SELECT * FROM $raspisanie_table WHERE id = $raspisanie_id ");
		foreach ($raspisanie_data_to_mail as $items)
		{	
			$raspisanie_result_mode = $wpdb->query('SELECT * FROM '.$raspisanie_time.' WHERE day = "'.$items->data_zakaza.'" && time = "'.$items->time_zakaza.'";');
			$raspisanie_res_string = $wpdb->get_results('SELECT * FROM '.$raspisanie_time.' WHERE day = "'.$items->data_zakaza.'" && time = "'.$items->time_zakaza.'";');
			foreach ($raspisanie_res_string as $items_res_string)
			{
				$wpdb->update
				(
					$raspisanie_time,
					array('aveliable' => 0),
					array('id' => $items_res_string->id),
					array('%d'),
					array('%d')
				);
			}
		}
		$wpdb->query("DELETE FROM $raspisanie_table WHERE id = $raspisanie_id");
	}

	
	
//Таблица заказов
$raspisanie_table_of_rooms_res = $wpdb->get_results("SELECT * FROM $raspisanie_table_of_rooms;");
	
	if ($raspisanie_table_of_rooms_res[0]->room_selected == "0") 
	{
		$raspisanie_table_of_rooms_res_name_of_zakaz_table = $raspisanie_table_of_rooms_res[0]->name_of_zakaz_table;
		$raspisanie_table_of_rooms_res_name_of_time_table = $raspisanie_table_of_rooms_res[0]->name_of_time_table;
		$raspisanie_table = $wpdb->prefix.$raspisanie_table_of_rooms_res_name_of_zakaz_table;
		$raspisanie_time = $wpdb->prefix.$raspisanie_table_of_rooms_res_name_of_time_table;
	}
	else
	{
		$raspisanie_table_of_rooms_temp = $raspisanie_table_of_rooms_res[0]->room_selected;
		$raspisanie_table_of_rooms_result = $wpdb->get_results("SELECT * FROM $raspisanie_table_of_rooms WHERE name_of_room = '$raspisanie_table_of_rooms_temp'");
		foreach ($raspisanie_table_of_rooms_result as $raspisanie_table_of_rooms_result_items)
		{
		$raspisanie_table = $wpdb->prefix.$raspisanie_table_of_rooms_result_items->name_of_zakaz_table;
		$raspisanie_time = $wpdb->prefix.$raspisanie_table_of_rooms_result_items->name_of_time_table;
		}
	}
$raspisanie = $wpdb->get_results("SELECT * FROM $raspisanie_table");

	echo ' 
	<h2>Таблица заказов</h2>
		<table width="98%" border="1" style="text-align:center">
		  <tr style="background:#CCC">
			<th>ID</th>
			<th>Заказчик</th>
			<th>Дата</th>
			<th>Время</th>
			<th>Город заказа</th>
			<th>Что заказал</th>
			<th>И-мейл</th>
			<th>Телефон</th>
			<th>Статус подтверждения</th>
		  </tr>';
		  foreach ($raspisanie as $items)
			{
			echo ' 
			<form method="post" action="'.$_SERVER['PHP_SELF'].'?page=raspisanie">
			  <tr>
				<td>'.$items->id.'</td>
				<td>'.$items->name_of_zakazchik.'</td>
				<td>'.$items->data_zakaza.'</td>
				<td>'.$items->time_zakaza.'</td>
				<td>'.$items->gorod_zakaza.'</td>
				<td>'.$items->chto_zakazano.'</td>
				<td>'.$items->e_mail.'</td>
				<td>'.$items->telefon.'</td>';
				switch ($items->status_potverzdeniya)
				{
						case 'Заказ не потврежден' : echo '<td><b>'.$items->status_potverzdeniya.'</b><br>'; break;
						case 'Заказ потвержден' : echo '<td><b style="color:green">'.$items->status_potverzdeniya.'</b><br>'; break;
						case 'Заказ отменен' : echo '<td><b style="color:red">'.$items->status_potverzdeniya.'</b><br>'; break;
				}
				
				echo '
					<input type="hidden" name="raspisanie_id" value="'.$items->id.'" />
					<input type="hidden" name="raspisanie_e_mail" value="'.$items->e_mail.'" />
					<input type="submit" name="potverdit_zakaz" value="Потвердить заказ" />
					<input type="submit" name="otmenit_zakaz" value="Отменить заказ" />
					<input type="submit" name="udalit_zakaz" value="Удалить заказ" />
				</td>
			  </tr> 
			  </form>';
			}
		  echo ' 
		</table><br>';
		
/*-----------------Настройка сообщения закачику-----------------*/
	
	if (isset($_POST['tamplate_potvizdenie']))
	{
		$raspisanie_report_tamplate_tamplate_potvizdenie_get_id = $wpdb->get_results("SELECT * FROM $raspisanie_report_tamplate WHERE name_of_tamplate = 'tamplate_potvizdenie';");
		$raspisanie_textfield = $_POST['textfield'];
		$wpdb->update
				(
					$raspisanie_report_tamplate,
					array('report_tamplate' => $raspisanie_textfield),
					array('id' => $raspisanie_report_tamplate_tamplate_potvizdenie_get_id[0]->id),
					array('%s'),
					array('%d')
				);
	}
	if (isset($_POST['tamplate_otmena']))
	{
		$raspisanie_report_tamplate_tamplate_potvizdenie_get_id = $wpdb->get_results("SELECT * FROM $raspisanie_report_tamplate WHERE name_of_tamplate = 'tamplate_otmena';");
		$raspisanie_textfield = $_POST['textfield'];
		$wpdb->update
				(
					$raspisanie_report_tamplate,
					array('report_tamplate' => $raspisanie_textfield),
					array('id' => $raspisanie_report_tamplate_tamplate_potvizdenie_get_id[0]->id),
					array('%s'),
					array('%d')
				);
	}
	$raspisanie_report_tamplate_tamplate_potvizdenie = $wpdb->get_results("SELECT * FROM $raspisanie_report_tamplate WHERE name_of_tamplate = 'tamplate_potvizdenie';");
	$raspisanie_report_tamplate_tamplate_otmena = $wpdb->get_results("SELECT * FROM $raspisanie_report_tamplate WHERE name_of_tamplate = 'tamplate_otmena';");
echo '
		<h2>Настройка сообщения заказчику</h2>
		
		';
		
		echo '
		<table width="98%"  >
  <tr>
  <th width="300" scope="col"">&nbsp;</th>
    <th width="300" scope="col">&nbsp;</th>
      <th scope="col" bgcolor="#CCCCCC">Шаблон сообщения</th>
  </tr>
  <tr>
  <td rowspan=2>
  <p >Для добавления данных используется : <br />Имя заказчика: -name_of_zakazchik- <br />Что заказано:  -chto_zakazano-<br /> Дата, время: -data_zakaza- , -time_zakaza- <br /> Город: -gorod_zakaza- <br /> Телефон: -telefon- <br />Используйте символ - в начале и конце </p>
  </td>
	<td width=20%><p>
	Шаблон сообщения при потверждении</p>
    </td>
    <td>
    <form method="post" action="'.$_SERVER['PHP_SELF'].'?page=raspisanie">
		<textarea name="textfield" id="" cols="70" rows="4">'.$raspisanie_report_tamplate_tamplate_potvizdenie[0]->report_tamplate.'</textarea>
      <input type="submit" name="tamplate_potvizdenie" value="Сохранить"  />
    </form>
    </td>
   <tr>

   <td><p>
	Шаблон сообщения при отмене</p>
    </td>
    <td>
    <form method="post" action="'.$_SERVER['PHP_SELF'].'?page=raspisanie">
		<textarea name="textfield" id="" cols="70" rows="5">'.$raspisanie_report_tamplate_tamplate_otmena[0]->report_tamplate.'</textarea>
      <input type="submit" name="tamplate_otmena" value="Сохранить"  />
    </form>
    </td>
  </tr>
</table>';

echo '
		<br />';
		
		if (isset($_POST['raspisanie_bron_conf']))
		{
			$raspisanie_bron_conf_text = str_replace(array("\r","\n"), array("","\\n"), $_POST['raspisanie_bron_conf_text']);
			update_option( 'raspisanie_bron_conf', $raspisanie_bron_conf_text );
		}
		if (isset($_POST['raspisanie_bron_mail']))
		{
			$raspisanie_bron_mail_text = str_replace(array("\r","\n"), array("","\\n"), $_POST['raspisanie_bron_mail_text']);
			update_option( 'raspisanie_bron_mail', $raspisanie_bron_mail_text);
		}
		if (isset($_POST['raspisanie_bron_tel']))
		{
			$raspisanie_bron_tel_text = str_replace(array("\r","\n"), array("","\\n"), $_POST['raspisanie_bron_tel_text']);
			update_option( 'raspisanie_bron_tel', $raspisanie_bron_tel_text);
		}
		
echo '
	<h2>Сообщение при успешном бронировании</h2>
	<form method="post" action="'.$_SERVER['PHP_SELF'].'?page=raspisanie">
		<textarea name="raspisanie_bron_conf_text" cols="70" rows="5">'.str_replace("\\n", PHP_EOL ,get_option('raspisanie_bron_conf')).'</textarea>
      <input type="submit" name="raspisanie_bron_conf" value="Сохранить"  />
    </form>
	<h2>Сообщение при неправильном вводе имейла</h2>
	<form method="post" action="'.$_SERVER['PHP_SELF'].'?page=raspisanie">
		<textarea name="raspisanie_bron_mail_text" cols="70" rows="5">'.str_replace("\\n", PHP_EOL ,get_option('raspisanie_bron_mail')).'</textarea>
      <input type="submit" name="raspisanie_bron_mail" value="Сохранить"  />
    </form>
	<h2>Сообщение при неправильном вводе телефона</h2>
	<form method="post" action="'.$_SERVER['PHP_SELF'].'?page=raspisanie">
		<textarea name="raspisanie_bron_tel_text"  cols="70" rows="5">'.str_replace("\\n", PHP_EOL ,get_option('raspisanie_bron_tel')).'</textarea>
      <input type="submit" name="raspisanie_bron_tel" value="Сохранить"  />
    </form>
';


/*-----------------конец настройки сообщения заказчику-----------------*/

//Настройки временеи отображения
if (isset($_POST['raspisanie_room_select']))
{
	$raspisanie_table_of_rooms_temp = $_POST['raspisanie_room_select'];
	$raspisanie_table_of_rooms_result = $wpdb->get_results("SELECT * FROM $raspisanie_table_of_rooms WHERE name_of_room = '$raspisanie_table_of_rooms_temp'");
	$raspisanie_time_tamplate = $wpdb->prefix.$raspisanie_table_of_rooms_result[0]->name_of_time_tamplate;
}

if (isset($_POST['raspianie_room_add']))
{
	$raspianie_room_add_form = $_POST['raspianie_room_add_form'];
	$raspisanie_table_of_rooms_result = $wpdb->get_results("SELECT * FROM $raspisanie_table_of_rooms WHERE name_of_room = '$raspianie_room_add_form'");
	$raspisanie_time_tamplate = $wpdb->prefix.$raspisanie_table_of_rooms_result[0]->name_of_time_tamplate;
}

if (isset($_POST['raspisanie_time_tamplate_change']))
	{
	$raspisanie_time_tamplate_id = $_POST['raspisanie_time_tamplate_id'];
	$raspisanie_what_day_tamplate = $_POST['what_day_tamplate'];
	$raspisanie_time_tamplate_colomn = $_POST['time_tamplate'];
	$raspisanie_cost_tamplate = $_POST['cost_tamplate'];
	
		if (isset($_POST['what_day_tamplate']))
		{
			$wpdb->update
				(
					$raspisanie_time_tamplate,
					array('what_day' => $raspisanie_what_day_tamplate),
					array('id' => $raspisanie_time_tamplate_id),
					array('%s'),
					array('%d')
				);
		}
		if (isset($_POST['time_tamplate']))
		{
			$wpdb->update
				(
					$raspisanie_time_tamplate,
					array('time' => $raspisanie_time_tamplate_colomn),
					array('id' => $raspisanie_time_tamplate_id),
					array('%s'),
					array('%d')
				);
		}
		if (isset($_POST['cost_tamplate']))
		{
			$wpdb->update
				(
					$raspisanie_time_tamplate,
					array('cost' => $raspisanie_cost_tamplate),
					array('id' => $raspisanie_time_tamplate_id),
					array('%s'),
					array('%d')
				);
		}
	}
	if (isset($_POST['raspisanie_time_tamplate_delete']))
	{
		$raspisanie_time_tamplate_id = $_POST['raspisanie_time_tamplate_id'];
		$wpdb->query("DELETE FROM $raspisanie_time_tamplate WHERE id = $raspisanie_time_tamplate_id");
	}
	
	if (isset($_POST['raspisanie_time_tamplate_add_button']))
	{	
		$raspisanie_what_day_tamplate_add = $_POST['what_day_tamplate_add'];
		$raspisanie_time_tamplate_add = $_POST['time_tamplate_add'];
		$raspisanie_cost_tamplate_add = $_POST['cost_tamplate_add'];

		$wpdb->insert(
			$raspisanie_time_tamplate,
			array( 'what_day' => $raspisanie_what_day_tamplate_add, 'time' => $raspisanie_time_tamplate_add,'cost' => $raspisanie_cost_tamplate_add ),
			array( '%s', '%s' , '%s' )
		);
	}

$raspisanie_time_tamplate_data = $wpdb->get_results("SELECT * FROM $raspisanie_time_tamplate ORDER BY what_day,time;");	
		echo '
		<h2>Настройка таблицы времени посещения и стоимости</h2>
		<p>B- будний, BP- пятница, V- выходной, P- празничные дни</p>
		<table  style="float: left;">
		  <tr bgcolor="#CCCCCC">
			
			<th scope="col">Обозначение дня</th>
			<th scope="col">Время</th>
			<th scope="col">Стоимость</th>
		  </tr>
		  <tr>
			<td><form method="post" action="'.$_SERVER['PHP_SELF'].'?page=raspisanie"> 
				<input type="text" name="what_day_tamplate_add" value="" />	
			</td>
			<td>
				<input type="text" name="time_tamplate_add" value="" />
			</td>
			<td>
				<input type="text" name="cost_tamplate_add" value="" />
				<input type="submit" name="raspisanie_time_tamplate_add_button" value="Добавить" /></form>
			</td>
		  </tr>';
		 
		   foreach ($raspisanie_time_tamplate_data as $time_tamplate_items)
			{
		  echo '
		  <tr>
		  
			<td>
			  <form method="post" action="'.$_SERVER['PHP_SELF'].'?page=raspisanie"> 
				<input type="hidden" name="raspisanie_time_tamplate_id" value="'.$time_tamplate_items->id.'" />
				<input style="width:80px" type="text" name="what_day_tamplate" value="'.$time_tamplate_items->what_day.'" />
				<input type="submit" name="raspisanie_time_tamplate_change" value="Изменить" />
			  </form>
			</td>
			<td>
			  <form method="post" action="'.$_SERVER['PHP_SELF'].'?page=raspisanie">
			  <input type="hidden" name="raspisanie_time_tamplate_id" value="'.$time_tamplate_items->id.'" />
				<input style="width:80px" type="text" name="time_tamplate" value="'.$time_tamplate_items->time.'" />
				<input type="submit" name="raspisanie_time_tamplate_change" value="Изменить" />
			  </form>
			</td>
			<td>
			  <form method="post" action="'.$_SERVER['PHP_SELF'].'?page=raspisanie">
			  <input type="hidden" name="raspisanie_time_tamplate_id" value="'.$time_tamplate_items->id.'" />
				<input style="width:81px" type="text" name="cost_tamplate" value="'.$time_tamplate_items->cost.'" />
				<input type="submit" name="raspisanie_time_tamplate_change" value="Изменить" />
				<input type="submit" name="raspisanie_time_tamplate_delete" value="Удалить" />
			  </form>
			</td>
		  </tr>';
		  }
		  echo '
		</table>';
	
	if (isset($_POST['holiday_add_button']))
	{
		$raspisanie_holiday_add = $_POST['holiday_add'];

		$wpdb->insert(
			$raspiasnie_table_of_holidays,
			array( 'date_of_holiday' => $raspisanie_holiday_add ),
			array( '%s' )
		);
	}
	
	if (isset($_POST['raspisanie_holiday_change']))
	{
		$raspisanie_holiday_id = $_POST['raspisanie_holiday_id'];
		$raspisanie_holiday = $_POST['raspisanie_holiday'];
		
		$wpdb->update
				(
					$raspiasnie_table_of_holidays,
					array('date_of_holiday' => $raspisanie_holiday),
					array('id' => $raspisanie_holiday_id),
					array('%s'),
					array('%d')
				);
	}	
		
	if (isset($_POST['raspisanie_holiday_delete']))
	{
		$raspisanie_holiday_id = $_POST['raspisanie_holiday_id'];
		$wpdb->query("DELETE FROM $raspiasnie_table_of_holidays WHERE id = $raspisanie_holiday_id");
	}	
		
	$raspisanie_holiday_data = $wpdb->get_results("SELECT * FROM $raspiasnie_table_of_holidays  ORDER BY date_of_holiday;");	
		echo '
		<table style="float: left;">
			<tr bgcolor="#CCCCCC">
			<th scope="col">Празничные дни</th>
			</tr>
			<tr>
				<td>
				 <form method="post" action="'.$_SERVER['PHP_SELF'].'?page=raspisanie"> 
				<input type="text" name="holiday_add" value="">
				<input type="submit" name="holiday_add_button" value="Добавить">
				</form>
				</td>
			</tr>';
			
			foreach ($raspisanie_holiday_data as $raspisanie_holiday_items)
			{
				echo '
			  <tr>
				<td>
				  <form method="post" action="'.$_SERVER['PHP_SELF'].'?page=raspisanie"> 
					<input type="hidden" name="raspisanie_holiday_id" value="'.$raspisanie_holiday_items->id.'" />
					<input style="width:81px" type="text" name="raspisanie_holiday" value="'.$raspisanie_holiday_items->date_of_holiday.'" />
					<input type="submit" name="raspisanie_holiday_change" value="Изменить" />
					<input type="submit" name="raspisanie_holiday_delete" value="Удалить" />
				  </form>
				</td>
			  </tr>';
			}
			
		echo '
		</table>
		';
		echo '<br>';

}
 
function raspisanie_install()
{
	global $wpdb;
	$raspisanie_table = $wpdb->prefix.raspisanie_table.'__1';
	$raspisanie_time = $wpdb->prefix.raspisanie_time.'__1';
	$raspisanie_time_tamplate = $wpdb->prefix.raspisanie_time_tamplate.'__1';
	$raspisanie_table_of_rooms = $wpdb->prefix.raspisanie_table_of_rooms;
	$raspisanie_report_tamplate = $wpdb->prefix.raspisanie_report_tamplate;
	$raspiasnie_table_of_holidays = $wpdb->prefix.raspiasnie_table_of_holidays;
	
	$sql_raspiasnie_table_of_holidays =
		"
		CREATE TABLE `".$raspiasnie_table_of_holidays."` (
		`id` INT(11) NOT NULL AUTO_INCREMENT,
		`date_of_holiday` CHAR(50) NOT NULL DEFAULT '0',
		PRIMARY KEY (`id`)
		)
		ENGINE=InnoDB
		;";
	
	$sql_raspisanie_report_tamplate ="
		CREATE TABLE `".$raspisanie_report_tamplate."` (
		`id` INT(11) NOT NULL AUTO_INCREMENT,
		`name_of_tamplate` CHAR(50) NOT NULL DEFAULT '0',
		`report_tamplate` VARCHAR(500) NOT NULL,
		PRIMARY KEY (`id`)
		)
		ENGINE=InnoDB
		;";
	$sql_raspisanie_report_tamplate_add_data = "
		INSERT INTO `".$raspisanie_report_tamplate."` (`id`, `name_of_tamplate`, `report_tamplate`) VALUES
		(1, 'tamplate_potvizdenie', 'Уважаемый -name_of_zakazchik- , наша команда потверждает бронирование -chto_zakazano- Вами на -data_zakaza- в -time_zakaza- . Также Вы указали -gorod_zakaza-телефон -telefon-. Если какие-то данные были указаны не верно, пожайлуста свяжитесь с администрацией.'),
		(2, 'tamplate_otmena', 'Уважаемый -name_of_zakazchik- , наша команда потверждает отмену бронирования -chto_zakazano- Вами на -data_zakaza- в -time_zakaza- . Также Вы указали -gorod_zakaza-телефон -telefon-. Если какие-то данные были указаны не верно, пожайлуста свяжитесь с администрацией.
')
	";
	$sql_raspisanie_table_of_rooms = "
		CREATE TABLE `".$raspisanie_table_of_rooms."` (
		`id` INT(11) NOT NULL AUTO_INCREMENT,
		`name_of_room` CHAR(50) NOT NULL DEFAULT '0',
		`name_of_zakaz_table` CHAR(50) NOT NULL DEFAULT '0',
		`name_of_time_table` CHAR(50) NOT NULL DEFAULT '0',
		`name_of_time_tamplate` CHAR(50) NOT NULL DEFAULT '0',
		`show_town` INT NULL DEFAULT '0',
		`room_selected` CHAR(50) NOT NULL DEFAULT '0',
		`number_of_days` INT(11) NOT NULL DEFAULT '30',
		PRIMARY KEY (`id`)
		)
		ENGINE=InnoDB
		AUTO_INCREMENT=2
		;
	";
	$sql_raspisanie_table_of_rooms_add_data = "
		INSERT INTO `".$raspisanie_table_of_rooms."` (`id`, `name_of_room`, `name_of_zakaz_table`, `name_of_time_table`, `name_of_time_tamplate`) VALUES
		(1, 'Комната 1', 'raspisanie_table__1', 'raspisanie_time__1', 'raspisanie_time_tamplate__1')
	";
	$sql_raspisanie_table = "
		CREATE TABLE `".$raspisanie_table."` (
		`id` INT(11) NOT NULL AUTO_INCREMENT,
		`name_of_zakazchik` VARCHAR(40) NOT NULL,
		`data_zakaza` CHAR(50) NOT NULL,
		`time_zakaza` CHAR(50) NOT NULL,
		`gorod_zakaza` VARCHAR(40) NOT NULL,
		`chto_zakazano` VARCHAR(40) NOT NULL,
		`e_mail` VARCHAR(40) NOT NULL,
		`telefon` VARCHAR(40) NOT NULL,
		`status_potverzdeniya` VARCHAR(50) NOT NULL DEFAULT 'Заказ не потврежден',
		PRIMARY KEY (`id`)
		)
		COLLATE='utf8_general_ci'
		ENGINE=InnoDB
		AUTO_INCREMENT=2;
	";
	$sql_raspisanie_time = "
		CREATE TABLE `".$raspisanie_time."` (
		`id` INT(4) NOT NULL AUTO_INCREMENT,
		`day` DATE NOT NULL,
		`time` CHAR(50) NOT NULL,
		`aveliable` INT(11) NOT NULL DEFAULT '0',
		PRIMARY KEY (`id`)
		)
		COLLATE='utf8_general_ci'
		ENGINE=InnoDB
		AUTO_INCREMENT=2
		;

	";
	$sql_raspisanie_time_tamplate = "
		CREATE TABLE `".$raspisanie_time_tamplate."` (
		`id` INT(11) NOT NULL AUTO_INCREMENT,
		`what_day` CHAR(50) NOT NULL,
		`time` CHAR(50) NOT NULL DEFAULT '0',
		`cost`  CHAR(50) NOT NULL DEFAULT '0',
		PRIMARY KEY (`id`)
		)
		ENGINE=InnoDB
		AUTO_INCREMENT=2
		;
		";
	$sql_raspisanie_time_tamplate_add_data = "
		INSERT INTO `".$raspisanie_time_tamplate."` (`id`, `what_day`, `time`, `cost`) VALUES
		(1, 'B', '17:00', '1500'),
		(2, 'B', '18:15', '1500'),
		(3, 'B', '19:30', '1500'),
		(4, 'B', '20:45', '1500'),
		(5, 'B', '22:00', '1500'),
		(6, 'BP', '17:00', '2000'),
		(7, 'BP', '18:15', '2000'),
		(8, 'BP', '19:30', '2000'),
		(9, 'BP', '20:45', '2000'),
		(10, 'BP', '22:00', '2000'),
		(11, 'V', '12:00', '2000'),
		(12, 'V', '13:15', '2000'),
		(13, 'V', '14:30', '2000'),
		(14, 'V', '15:45', '2000'),
		(15, 'V', '17:00', '2000'),
		(16, 'V', '18:15', '2000'),
		(17, 'V', '19:30', '2000'),
		(18, 'V', '20:45', '2000'),
		(19, 'V', '22:00', '2000');
	";
	
	$wpdb->query($sql_raspiasnie_table_of_holidays);
	$wpdb->query($sql_raspisanie_report_tamplate);
	$wpdb->query($sql_raspisanie_report_tamplate_add_data);
	$wpdb->query($sql_raspisanie_table_of_rooms);
	$wpdb->query($sql_raspisanie_table_of_rooms_add_data);
	$wpdb->query($sql_raspisanie_table);
	$wpdb->query($sql_raspisanie_time);
	$wpdb->query($sql_raspisanie_time_tamplate);
	$wpdb->query($sql_raspisanie_time_tamplate_add_data);
	
	add_option( 'raspisanie_email', '' );
	add_option( 'raspisanie_bron_conf', 'Ваш заказ  отправлен.\\nНаши специалисты свяжутся с Вам в течении 2-х часов.\\nБлагодарим за интерес к нашей фирме!' );
	add_option( 'raspisanie_bron_mail', 'Пожалуйста, введите адрес электронной почты ,\\n чтобы наши специалисты могли связаться с Вами.' );
	add_option( 'raspisanie_bron_tel', 'Пожалуйста, введите телефон с +7 в начале и 10 цифрами номера,\\n чтобы наши специалисты могли связаться с Вами.' );

		
}
function raspisanie_unistall()
{
	global $wpdb;
	$raspisanie_table_of_rooms = $wpdb->prefix.raspisanie_table_of_rooms;
	$raspisanie_report_tamplate = $wpdb->prefix.raspisanie_report_tamplate;
	$raspiasnie_table_of_holidays = $wpdb->prefix.raspiasnie_table_of_holidays;

	$raspisanie_table_of_rooms_res_string = $wpdb->get_results('SELECT * FROM '.$raspisanie_table_of_rooms.';');
		
        foreach ($raspisanie_table_of_rooms_res_string as $items_res_string)
        {
            $sql_drop1 = "DROP TABLE `".$wpdb->prefix.$items_res_string->name_of_zakaz_table."`;";
            $wpdb->query($sql_drop1);
            $sql_drop2 = "DROP TABLE `".$wpdb->prefix.$items_res_string->name_of_time_table."`;";
            $wpdb->query($sql_drop2);
            $sql_drop3 = "DROP TABLE `".$wpdb->prefix.$items_res_string->name_of_time_tamplate."`;";
            $wpdb->query($sql_drop3);
        }	
	
	$sql1 = "DROP TABLE `".$raspisanie_table_of_rooms."`;";
	$sql2 = "DROP TABLE `".$raspisanie_report_tamplate."`;";
	$sql3 = "DROP TABLE `".$raspiasnie_table_of_holidays."`;";
	
	$wpdb->query($sql1);
	$wpdb->query($sql2);
	$wpdb->query($sql3);
	
	delete_option('raspisanie_email');
	delete_option('raspisanie_bron_conf');
	delete_option('raspisanie_bron_mail');
	delete_option('raspisanie_bron_tel');
}
register_activation_hook(__FILE__, 'raspisanie_install');
register_deactivation_hook(__FILE__, 'raspisanie_unistall');

add_action('admin_menu' , 'raspisanie_admin_menu');
add_shortcode('raspisanie', 'raspisanie_func');
	
?>