<?php

require_once __DIR__ . '/../lib/Helpers.php';

class HelpersTest extends PHPUnit_Framework_TestCase {
	function testParseAcceptHeader() {
		$this->assertEquals(array('mime' => 'text/html', 'q' => 0.8), Conduit\Helpers::parseAcceptHeader('text/html;q=0.8'));
		$this->assertEquals(array('mime' => 'text/html', 'q' => 0.8), Conduit\Helpers::parseAcceptHeader('text/html; q=0.8'));
		$this->assertEquals(array('mime' => 'text/html', 'q' => 1.0), Conduit\Helpers::parseAcceptHeader('text/html;q=1'));
		$this->assertEquals(array('mime' => 'text/html', 'q' => 1.0), Conduit\Helpers::parseAcceptHeader('text/html;q='));
		$this->assertEquals(array('mime' => 'text/html', 'q' => 1.0), Conduit\Helpers::parseAcceptHeader('text/html'));
	}

	function testPreferredFormat() {
		$this->assertEquals('text/html', Conduit\Helpers::preferredFormat(array('application/json', 'text/html'), 'text/html'));
		$this->assertEquals('text/html', Conduit\Helpers::preferredFormat(array('text/html'), 'text/html;q=0.8,application/json;q=0.5,application/xml'));
		$this->assertEquals('application/xml', Conduit\Helpers::preferredFormat(array('text/html', 'application/xml'), 'text/html;q=0.8,application/json;q=0.5,application/xml'));
		$this->assertEquals('application/json', Conduit\Helpers::preferredFormat(array('application/json', 'text/html', 'application/xml'), 'text/html;q=0.8,application/json;q=1,application/xml=0.3'));
	}
}