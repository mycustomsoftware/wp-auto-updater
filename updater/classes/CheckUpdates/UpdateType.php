<?php

namespace WpAutoUpdater\CheckUpdates;

use WpAutoUpdater\CronSchedulesInterval;
use WpAutoUpdater\Importer;

class UpdateType
{
	public static $type;
	function __construct(){
		$type = static::$type;
		add_action("wp_update_checker_handle_error_{$type}_download",array($this,'error_download_handler'));
		add_action("update_checker_{$type}_update",array($this,'check_update'));
		add_filter("handle_checker_{$type}_update",array($this,'update'),10,2);
//		add_action('init',array($this,'init'),9999999);
		if ( ! wp_next_scheduled( "update_checker_{$type}_update" ) ) {
			wp_schedule_event( time(), CronSchedulesInterval::$SLUG, "update_checker_{$type}_update" );
		}
	}
	public function update($return, $Importer){
		return $return;
	}
	public function can_continue($Importer,$is_core = false){
		$can_continue = true;
		if($is_core === false){
			$is_update_available = CoreUpdates::is_core_update_available();
			if($is_update_available){
				$can_continue = false;
			}
		}
		if(($Importer instanceof Importer) === false){
			$can_continue = false;
		}
		if(static::$type !== $Importer->type){
			$can_continue = false;
		}
		return $can_continue;
	}
	function error_download_handler($is_handled, Importer $Importer){
		return $is_handled;
	}
	function init(){
		$type = static::$type;
		do_action("update_checker_{$type}_update",array($this,'check_update'));
	}
	function check_update(){
		if (!function_exists('get_plugin_updates')) {
			require_once(ABSPATH . 'wp-admin/includes/update.php');
		}
		return true;
	}
}
