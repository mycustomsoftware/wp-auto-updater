<?php

namespace WpAutoUpdater\History;

class HistoryItem
{
	public $time;
	public $status;
	public $message;
	public array $data;

	public function __construct(array $data = array())
	{
		$this->time    = $data['time']    ?? current_time('timestamp');
		$this->data    = $data['data']    ?? array();
		$this->message = $data['message'] ?? '';
		$this->status  = $data['status']  ?? 'empty';
	}

	/**
	 * @return mixed
	 */
	public function get_time()
	{
		return $this->time;
	}

	/**
	 * @param mixed $time
	 */
	public function set_time($time): void
	{
		$this->time = $time;
	}

	/**
	 * @return mixed
	 */
	public function get_message()
	{
		return $this->message;
	}

	/**
	 * @param mixed $message
	 */
	public function set_message($message): void
	{
		$this->message = $message;
	}

	/**
	 * @return mixed
	 */
	public function get_status()
	{
		return $this->status;
	}

	/**
	 * @param mixed $status
	 */
	public function set_status($status): void
	{
		$this->status = $status;
	}

	public function get_data(): array
	{
		return $this->data;
	}

	public function set_data(array $data): void
	{
		$this->data = $data;
	}

	public function to_array(): array
	{
		$vars = get_class_vars(__CLASS__);
		$return_data = array();
		foreach ($vars as $key => $value) {
			$return_data[$key] = $this->$key;
		}
		return $return_data;
	}
}
