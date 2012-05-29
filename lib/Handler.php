<?php

namespace Conduit;

require_once __DIR__ . '/Helpers.php';
require_once __DIR__ . '/HTTP.php';

class Handler {
	public $id;
	public $format;

	protected $formats = array();

	function __construct($id) {
		if (!$id) exit('No ID provided'); // 400
		$this->id = $id;
	}

	function handle() {
		ob_start();

		$this->format = Helpers::preferredFormat(array_keys($this->formats));
		if (!$this->format) exit('No acceptable output format'); // 406

		$handler = array($this, $this->formats[$this->format]);
		if (!is_callable($handler)) exit('No acceptable output format'); // 406

		call_user_func($handler);
		header('Content-Type: ' . $this->format);

		ob_end_flush();
	}

	function redirect($url, $code = 301) {
		header('Location: ' . $url, $code);
		header('Content-Type: text/plain');
		print $url;
	}
}