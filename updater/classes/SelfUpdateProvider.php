<?php

namespace WpAutoUpdater;

use WP_GitHub_Updater;

class SelfUpdateProvider
{
	public function __construct()
	{
//		git@github.com:mycustomsoftware/wp-auto-updater.git
		$config = array(
			'slug' => plugin_basename( __FILE__ ),
			'proper_folder_name' => 'wp-auto-updater',
			'api_url' => 'https://api.github.com/repos/mycustomsoftware/wp-auto-updater',
			'raw_url' => 'https://raw.github.com/mycustomsoftware/wp-auto-updater/master',
			'github_url' => 'https://github.com/mycustomsoftware/wp-auto-updater',
			'zip_url' => 'https://github.com/mycustomsoftware/wp-auto-updater/archive/master.zip',
			'sslverify' => true,
			'requires' => '6.8.3',
			'tested' => '6.8.3',
			'readme' => 'README.md',
			'access_token' => '',
		);
		new GitHubUpdater( $config );
	}
}