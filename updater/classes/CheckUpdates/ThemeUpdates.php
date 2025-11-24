<?php

namespace WpAutoUpdater\CheckUpdates;

use Theme_Upgrader;
use WpAutoUpdater\FoldersCreator;
use WpAutoUpdater\History\HistoryItem;
use WpAutoUpdater\Importer;
use WP_Ajax_Upgrader_Skin;

class ThemeUpdates extends UpdateType
{
	public static $type = 'themes';

	function check_update()
	{
		$check_update = parent::check_update();
		if (!$check_update) {
			return;
		}
		if (!($themes = get_theme_updates())) {
			return;
		}
		$ignore_updates = get_option('ignore_updates', array());
		foreach ($themes as $theme):
			$theme = (object) $theme;
			if(!isset($theme->update)){
				continue;
			}
			$update = $theme->update;
			if(!isset($update['theme'])){
				continue;
			}
			$theme_update = $update['theme'];
			$new_version  = $update['new_version'];
			$package      = $update['package'];
			if(empty($package)){
				continue;
			}
			if ( in_array($theme_update, $ignore_updates) ) {
				continue;
			}
			$slug         = $new_version . '-' . $theme_update;
			$history      = get_option("update_checker_{$slug}_history", array());
			$theme_folder = FoldersCreator::get_theme_folder();
			new Importer(
				$slug,
				self::$type,
				$package,
				$theme_folder,
				$history,
				'',
				array('update'=>$update)
			);
		endforeach;
	}
	public function update($return, $Importer){
		if(!$this->can_continue($Importer)){
			return $return;
		}
		if(!class_exists('Theme_Upgrader')){
			require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		}
		if(! defined('FS_METHOD')){
			define('FS_METHOD', 'direct'); //May be defined already and might not be 'direct' so this could cause problems. But we were getting reports of a warning that this is already defined, so this check added.
		}
		require_once(ABSPATH . 'wp-includes/update.php');
		require_once(ABSPATH . 'wp-admin/includes/file.php');
		$skin     = new WP_Ajax_Upgrader_Skin();
		$upgrader = new Theme_Upgrader( $skin );
		$result_bulk_upgrade = $upgrader->bulk_upgrade( array($Importer->args['update']['theme']) );
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
