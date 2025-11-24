<?php

/**
 * Plugin Name: WP Updater
 * Plugin URI:
 * Description: This plugin automatically update all active plugins, active theme and WordPress core. This plugin does not update inactive plugins and themes.
 * Version: 1.0.0
 * Author:      My Custom Software
 * Author URI: https://github.com/mycustomsoftware
 *  License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Requires PHP: 7.4
 **/

use WpAutoUpdater\AdminAssets;
use WpAutoUpdater\CheckUpdates\CoreUpdates;
use WpAutoUpdater\CheckUpdates\PluginsUpdates;
use WpAutoUpdater\CheckUpdates\ThemeUpdates;
use WpAutoUpdater\CronSchedulesInterval;
use WpAutoUpdater\DisableDefaultActions;
use WpAutoUpdater\IgnoreUpdatesAction;
use WpAutoUpdater\PageSettings;
use WpAutoUpdater\SelfUpdateProvider;

if (!defined('ABSPATH')) exit;
require_once __DIR__ . '/updater/vendor/autoload.php';
$dirs = DIRECTORY_SEPARATOR;
if(!defined('WP_UPDATE_CHECKER_PL_PATH')){
	define('WP_UPDATE_CHECKER_PL_PATH', __DIR__);
}
if(!defined('WP_UPDATE_CHECKER_PL_FILE')){
	define('WP_UPDATE_CHECKER_PL_FILE', __FILE__);
}
if(!defined('WP_UPDATE_CHECKER_PATH')){
	define('WP_UPDATE_CHECKER_PATH', __DIR__.$dirs.'updater'.$dirs);
}
if(!defined('WP_UPDATE_CHECKER_FILE')){
	define('WP_UPDATE_CHECKER_FILE', __DIR__.$dirs.'updater'.$dirs.'index.php');
}
if(!defined('WP_UPDATE_CHECKER_VER')){
	define('WP_UPDATE_CHECKER_VER', '1.0.0');
}
class WpAutoUpdaterMain
{
	public static $updates_classes = array(
		CoreUpdates::class,
		PluginsUpdates::class,
		ThemeUpdates::class,
	);
	function __construct()
	{
		new SelfUpdateProvider();
		new DisableDefaultActions();
		new CronSchedulesInterval();
		new PageSettings();
		new IgnoreUpdatesAction();
		new AdminAssets();
		$updates_classes = apply_filters('get_updates_classes_list',self::$updates_classes);
		foreach ($updates_classes as $updates_classe){
			new $updates_classe();
		}
	}
}

new WpAutoUpdaterMain();
