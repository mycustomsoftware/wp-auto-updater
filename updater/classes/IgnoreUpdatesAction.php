<?php

namespace WpAutoUpdater;

class IgnoreUpdatesAction
{
	function __construct()
	{
		add_action('plugin_action_links', array($this, 'add_links'), 10, 3);
	}

	public function add_links($actions, $plugin_file)
	{
		$options            = get_option( 'ignore_updates', array() );
		$is_disabled        = !in_array( $plugin_file, $options );
		$ignore_args = array(
				'page'     => 'wp-manage-updates-action',
				'type'     => 'plugin',
				'file'     => $plugin_file,
				'activate' => true
		);
		if (!$is_disabled) {
			unset($ignore_args['activate']);
			$ignore_args['deactivate'] = true;
		}
		$DisableLabel = __("Disable auto-update");
		$EnableLabel  = __("Enable auto-update");
		$ignore_updates_url = add_query_arg(
			apply_filters(
				'ignore_updates_action_query_args', $ignore_args),
			is_network_admin() ? network_admin_url('options-general.php') : admin_url('options-general.php')
		);
		$actions['ignore_updates'] = apply_filters(
			'ignore_updates_action_link',
			sprintf('<a href="%s">%s</a>',
				esc_url($ignore_updates_url),
				($is_disabled == true ? $DisableLabel : $EnableLabel)
			)
		);
		return $actions;
	}
}
