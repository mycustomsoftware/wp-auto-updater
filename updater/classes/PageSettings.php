<?php

namespace WpAutoUpdater;
class PageSettings
{
    private $options;
    public function __construct(){
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
    }
    public function add_plugin_page(){
        add_options_page(
            'Manage updates',
            'Manage updates',
            'manage_options',
            'wp-manage-updates-action',
            array( $this, 'create_admin_page' )
        );
    }
    public function create_admin_page(){
	    $file          = $_GET['file'] ?? '';
	    $type          = $_GET['type'] ?? '';
	    $is_activate   = isset($_GET['activate']);
	    $is_deactivate = isset($_GET['deactivate']);
	    if( empty($type) ) {
		    return;
	    }
	    if( empty($file) ) {
		    return;
	    }
	    $options = get_option( 'ignore_updates', array() );
		if(!in_array($file,$options) && $is_activate){
		    $options[] = $file;
		}
		if(in_array($file,$options) && $is_deactivate){
			$option_key = array_search($file,$options);
			if($option_key !== false){
				unset($options[$option_key]);
			}
		}
	    update_option('ignore_updates', $options);
		if($type == 'plugin'){
			wp_redirect( admin_url( 'plugins.php?plugin_status=all' ) );
		}
		if($type == 'theme'){
			wp_redirect( admin_url( 'themes.php?theme='.$file ) );
		}
    }
    public function sanitize( $input )
    {
        return $input;
    }
}

