<?php

namespace WpAutoUpdater;

class AdminAssets
{
	function __construct(){
//	    register scripts and styles
		add_action('admin_enqueue_scripts', array($this, 'enqueue'));
	}
	function enqueue(){
		wp_register_script(
			'themes-actions',
			plugins_url('assets/js/themes.js', WP_UPDATE_CHECKER_FILE),
			array(),
			WP_UPDATE_CHECKER_VER
		);
		$options            = get_option( 'ignore_updates', array() );
		wp_localize_script(
			'themes-actions',
			'themeIgnoreUpdates',
			array(
				'ignore_updates'=> $options,
				'i18n' => array(
					"disable_auto_update" => __('Disable auto-update'),
					"enable_auto_update"  => __('Enable auto-update'),
				)
			)
		);
		wp_enqueue_script('themes-actions');
	}
}
