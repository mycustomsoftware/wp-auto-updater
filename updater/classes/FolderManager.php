<?php

namespace WpAutoUpdater;

class FolderManager
{
	public static function create_if_not_exist(string $folder,$Loger = false)
	{
		$is_created = is_dir($folder);
		if(!$is_created){
			$is_created = wp_mkdir_p($folder);
		}
		if($Loger){
			if(!$is_created){
				$Loger->error_log(__METHOD__.':: cannot create folder '.$folder,'create-folder');
			}
		}
		return $is_created;
	}
}
