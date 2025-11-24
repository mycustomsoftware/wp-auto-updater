<?php

namespace WpAutoUpdater;

class CronSchedulesInterval
{
//	public static $SLUG = 'one_minute';
	public static $SLUG = '20_sec';
	function __construct(){
		add_filter( 'cron_schedules', array($this, 'add_schedule_interval') );
	}
	function add_schedule_interval( $schedules ) {
		if(!isset($schedules[self::$SLUG])){
			$schedules[self::$SLUG] = array(
				'interval' => 20,
				'display'  => __( '1 Minute')
			);
		}
		return $schedules;
	}
}
