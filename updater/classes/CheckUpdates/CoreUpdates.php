<?php

namespace WpAutoUpdater\CheckUpdates;

use Core_Upgrader;
use WpAutoUpdater\FoldersCreator;
use WpAutoUpdater\History\HistoryItem;
use WpAutoUpdater\Importer;
use WP_Ajax_Upgrader_Skin;

class CoreUpdates extends UpdateType
{
	public static $type = 'core';
	function check_update(){
		$check_update = parent::check_update();
		if(!$check_update){
			return;
		}
		global $wp_version;
		$updates = get_core_updates();
		if(empty($updates)){
			return;
		}
		foreach ($updates as $update):
			if ( $update->response !== 'upgrade' ) {
				continue;
			}
			if( !version_compare( $wp_version, $update->version ,'<' ) ){
				continue;
			}
			if( !isset($update->packages) ){
				continue;
			}
			if( !isset($update->packages->no_content) ){
				continue;
			}
			$slug        = $update->version . 'core' . $wp_version;
			$history     = get_option("update_checker_{$slug}_history", array());
			$core_folder = FoldersCreator::get_core_folder();
			new Importer(
				$slug,
				self::$type,
				$update->packages->no_content,
				$core_folder,
				$history,
				'',
				array('update' => $update)
			);
		endforeach;
	}

	/**
	 * @param Importer $Importer
	 * @return false|string|\WP_Error
	 */
	public function update_core($Importer) {
		if(!class_exists('Core_Upgrader')){
			require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		}
		require_once(ABSPATH . 'wp-includes/update.php');
		require_once(ABSPATH . 'wp-admin/includes/file.php');
		$skin     = new WP_Ajax_Upgrader_Skin();
		$upgrader = new Core_Upgrader( $skin );
		return $upgrader->upgrade( $Importer->args['update']);
	}

	public static function is_core_update_available(){
		$available = false;
		global $wp_version;
		$updates = get_core_updates();
		if(!empty($updates)){
			foreach ($updates as $update){
				if ( $update->response !== 'upgrade' ) {
					continue;
				}
				if( !version_compare( $wp_version, $update->version ,'<' ) ){
					continue;
				}
				if( !isset($update->packages) ){
					continue;
				}
				if( !isset($update->packages->no_content) ){
					continue;
				}
				$available = true;
			}
		}
		return $available;
	}
	public function update($return, $Importer){
		if(!$this->can_continue($Importer,true)){
			return $return;
		}
		if (version_compare(get_bloginfo('version'), '4.5', '>=')) {
			delete_option('core_updater.lock');
			delete_option('auto_updater.lock');
		} else {
			delete_option('core_updater');
		}
		$result_upgrade = $this->update_core($Importer);
		$updated = false;
		$result  = $result_upgrade;
		if(!empty($result_upgrade) && is_array($result_upgrade)){
			$result = array_shift($result_upgrade);
		}
		if ( FALSE === $result || is_wp_error( $result ) ) {

			ob_start();
			var_dump($result);
			$data = ob_get_contents();
			ob_clean();
			$Importer->history->add_history_item(new HistoryItem(array(
				'status'  => 'error-updated',
				"message" => $data
			)));
		}
		if ( $result && !is_wp_error( $result ) ) {
			$updated = true;
		}
		if($updated == true){
			$Importer->history->clear_history();
		}
		return true;
	}
}
