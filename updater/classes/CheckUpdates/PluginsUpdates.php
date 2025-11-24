<?php

namespace WpAutoUpdater\CheckUpdates;

use Plugin_Upgrader;
use SimplePie\Exception;
use WpAutoUpdater\FoldersCreator;
use WpAutoUpdater\History\HistoryItem;
use WpAutoUpdater\Importer;
use WP_Ajax_Upgrader_Skin;

class PluginsUpdates extends UpdateType
{
	public static $type = 'plugins';
	function check_update()
	{
		$check_update = parent::check_update();
		if (!$check_update) {
			return;
		}
		$ignore_updates = get_option('ignore_updates', array());
		$all_plugins = self::get_all_plugins();
		if (empty($all_plugins)) {
			return;
		}
		$current = get_site_transient('update_plugins');
		$plugins_to_update = array();
		foreach ((array)$all_plugins as $plugin_file => $plugin_data) {
			$plugins[$plugin_file] = (array)$plugin_data;
			if (in_array($plugin_file, $ignore_updates)) {
				continue;
			}
			if (!isset($current->response[$plugin_file])) {
				continue;
			}
			$response       = $current->response[$plugin_file];
			if (!isset($response->package) || empty($response->package)) {
				continue;
			}
			$slug           = $response->new_version . '-' . $response->slug;
			$history        = get_option("update_checker_{$slug}_history", array());
			$package        = $response->package;
			$plugins_to_update[$slug] = array(
				'plugins_folder' => FoldersCreator::get_plugins_folder(),
				'history'        => $history,
				'package'        => $package,
				'plugin_file'    => $plugin_file,
			);
		}
		if(!empty($plugins_to_update)){
			foreach ($plugins_to_update as $slug => $plugin_data) {
				new Importer(
					$slug,
					self::$type,
					$plugin_data['package'],
					$plugin_data['plugins_folder'],
					$plugin_data['history'],
					$plugin_data['plugin_file'],
					array('update'=>$plugin_data)
				);
			}
		}
	}
	public static function get_all_plugins(){
		$all_plugins   = array();
		if( !function_exists('get_plugin_data') ){
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}
		if(!function_exists('wp_get_active_and_valid_plugins')){
			return $all_plugins;
		}
		$valid_plugins = wp_get_active_and_valid_plugins();
		if (empty($valid_plugins)) {
			return $all_plugins;
		}
		foreach ($valid_plugins as $key => $valid_plugin) {
			$slug = str_replace(WP_PLUGIN_DIR . '/', '', $valid_plugin);
			$all_plugins[$slug] = get_plugin_data($valid_plugin);
		}
		return $all_plugins;
	}
	function update($return, $Importer){
		if(!$this->can_continue($Importer)){
			return $return;
		}
		if(!class_exists('Plugin_Upgrader')){
			require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		}
		require_once(ABSPATH . 'wp-includes/update.php');
		require_once(ABSPATH . 'wp-admin/includes/file.php');
		$skin     = new WP_Ajax_Upgrader_Skin();
		$upgrader = new Plugin_Upgrader( $skin );
		$result_bulk_upgrade = $upgrader->bulk_upgrade( array($Importer->main_file) );
		$updated = false;
		$result  = false;
		if(!empty($result_bulk_upgrade) && is_array($result_bulk_upgrade)){
			$result = array_shift($result_bulk_upgrade);
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
