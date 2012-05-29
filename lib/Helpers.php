<?php

namespace Conduit;

class Helpers {
	/**
	 * Parse the HTTP Accept header
	 * @param string $header
	 * @return array
	 */
	function parseAcceptHeader($header) {
		list($mime, $quality) = array_map('trim', explode(';', $header));
		list(, $q) = array_map('trim', explode('=', $quality));

		return array(
			'mime' => strtolower($mime),
			'q' => $q ? (float) $q : 1,
		);
	}

	/**
	 * Pick the preferred format from the HTTP Accept header
	 * e.g. 'Accept: text/html;q=0.8,application/json;q=0.5,application/xml
	 * @param array $formats
	 * @params string $header $_SERVER['HTTP_ACCEPT']
	 * @return string|null
	 */
	function preferredFormat($formats = array(), $header = null) {
		if (!$header) $header = $_SERVER['HTTP_ACCEPT'];

		$items = array_map(array(self, 'parseAcceptHeader'), array_filter(array_map('trim', explode(',', $header))));
		if (!$items) return null;

		$accept = array();
		foreach ($items as $item) {
			$accept[$item['mime']] = $item['q'];
		}
		arsort($accept); // sort the accepted formats in descending order of preference (q value)

		// pick the acceptable format with the highest value
		foreach (array_keys($accept) as $mime) {
			if (in_array($mime, $formats)) return $mime;
		}

		return null;
	}
}

