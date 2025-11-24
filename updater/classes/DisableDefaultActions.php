<?php

namespace WpAutoUpdater;

class DisableDefaultActions
{
	public function __construct()
	{
		add_filter('plugins_auto_update_enabled',"__return_false");
		add_filter('themes_auto_update_enabled',"__return_false");
	}
}