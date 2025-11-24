<?php

namespace WpAutoUpdater\History;

class HistoryItems
{
	public $array_history_items = array();
	public $type = '';

	/**
	 * @param array $array_history_items
	 */
	public function __construct(array $array_history_items, $type = '')
	{
		$this->type = $type;
		if(!empty($array_history_items)){
			foreach ($array_history_items as $array_history_item) {
				$this->array_history_items[] = new HistoryItem($array_history_item);
			}
		}
	}

	public function clear_history(): void{
		delete_option("update_checker_{$this->type}_history");
	}
	public function add_history_item( $item ): void
	{
		if($item instanceof HistoryItem){
			$this->array_history_items[] = $item;
		}else{
			$this->array_history_items[] = new HistoryItem($item);
		}
		$history_array = array();
		foreach ($this->array_history_items as $array_history_item) {
			$history_array[] = $array_history_item->to_array();
		}
		update_option("update_checker_{$this->type}_history",$history_array);
	}

	public function get_array_history_items(): array
	{
		return $this->array_history_items;
	}

	public function set_array_history_items(array $array_history_items): void
	{
		$this->array_history_items = $array_history_items;
	}

	public function get_last(): HistoryItem
	{
		return $this->array_history_items[count($this->array_history_items) - 1] ?? new HistoryItem();
	}
	public function get_first(): HistoryItem
	{
		return $this->array_history_items[0] ?? new HistoryItem();
	}

}
