<?php

namespace WpAutoUpdater;

class FoldersCreator
{
	public static $main_folder ='update-checker';
	public static $folders = array(
		'core',
		'plugins',
		'themes',
	);
	public $tmp_path;
	public static $DIRS = DIRECTORY_SEPARATOR;
	public static $CORE = 0;
	public static $PLUGINS = 1;
	public static $THEMES = 2;
	public static function createFolders(){
		$folders_created = array();
		$tmp_path = self::get_tmp_path().self::$DIRS;
		$is_folder = FolderManager::create_if_not_exist($tmp_path);
		if(!$is_folder){
			return false;
		}
		foreach(self::$folders as $folder){
			$is_folder = FolderManager::create_if_not_exist($tmp_path.self::$DIRS.$folder);
			if(!$is_folder){
				$folders_created[] =  false;
				continue;
			}
			$folders_created[] = true;
		}
		if(count($folders_created) == count(self::$folders)){
			return true;
		}
		return false;
	}

	/**
	 * @return mixed
	 */
	public static function get_core_folder()
	{
		return self::get_tmp_path().self::$DIRS.self::$folders[self::$CORE];
	}

	/**
	 * @return mixed
	 */
	public static function get_plugins_folder()
	{
		return self::get_tmp_path().self::$DIRS.self::$folders[self::$PLUGINS];
	}

	/**
	 * @return mixed
	 */
	public static function get_theme_folder()
	{
		return self::get_tmp_path().self::$DIRS.self::$folders[self::$THEMES];
	}

	/**
	 * @return mixed
	 */
	public static function get_tmp_path()
	{
		$uploads      = wp_upload_dir();
		$uploads_path = $uploads["basedir"].self::$DIRS;
		return $uploads_path.self::$main_folder;
	}

}
