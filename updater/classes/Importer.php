<?php

namespace WpAutoUpdater;

use WpAutoUpdater\History\HistoryItem;
use WpAutoUpdater\History\HistoryItems;

class Importer
{
	/**
	 * @var
	 */
	public $enabled_log = false;
	public $slug;
	/**
	 * @var
	 */
	public $type;
	/**
	 * @var
	 */
	public $url;
	/**
	 * @var
	 */
	public $path;
	/**
	 * @var
	 */
	public $tmppath;
	/**
	 * @var
	 */
	public $main_file;
	public $args = array();
	public $status;
	/**
	 * @var HistoryItems
	 */
	public HistoryItems $history;
	/**
	 * @var string
	 */
	public static $_dir_s = DIRECTORY_SEPARATOR;

	/**
	 * @param $slug
	 * @param $type
	 * @param $url
	 * @param $tmppath
	 * @param $history
	 * @param array $args
	 */
	public function __construct($slug, $type, $url, $tmppath, $history, $main_file = '',$args = array())
	{
		$this->slug      = $slug;
		$this->type      = $type;
		$this->url       = $url;
		$this->tmppath   = $tmppath;
		$this->args      = $args;
		$this->main_file = $main_file;
		$this->history = new HistoryItems($history, $slug);
		$last_status = $this->history->get_last()->status;
		switch (true) {
			case $last_status == 'empty':
			case $last_status == 'error':
			case $last_status == 'error-updated':
				$this->update();
				break;
		}
	}

	/**
	 * @return void
	 */
	public function update(): void
	{
		$historyItemStatus = $this->history->get_last()->status;
		if ($historyItemStatus !== 'updated') {
			$is_handled = apply_filters("handle_checker_{$this->type}_update",false,$this);
			if($is_handled === true){
				return;
			}
			if($historyItemStatus != 'error-updated' && $is_handled === false){
				$this->history->add_history_item(new HistoryItem(array(
					'status' => 'error-updated',
					"message" => "url: ".$this->url
				)));
				return;
			}
		}
	}

	/**
	 * @param mixed $status
	 */
	public function set_status($status): void
	{
		$this->status = $status;
	}


}
