<?php

class PPI_Request_ServerTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
		$_SERVER = array('foo' => 'bar', 'bar' => 'foo');
	}

	public function tearDown() {
		$_SERVER = array();
	}

	public function testIsCollected() {
		$server = new PPI_Request_Server;
		$this->assertTrue($server->isCollected());

		$server = new PPI_Request_Server(array('drink' => 'beer'));
		$this->assertFalse($server->isCollected());

		$server = new PPI_Request_Server(array());
		$this->assertTrue($server->isCollected());
	}

	public function testCollectServer() {
		$server = new PPI_Request_Server;
		$this->assertEquals('foo', $server['bar']);
		$this->assertEquals('bar', $server['foo']);
		$this->assertEquals(null,  $server['random']);
		$this->assertTrue($server->isCollected());
	}

	public function testCustomServer() {
		$server = new PPI_Request_Server(array('drink' => 'beer'));
		$this->assertEquals('beer', $server['drink']);
		$this->assertEquals(null,   $server['foo']);
		$this->assertEquals(null,   $server['random']);
		$this->assertFalse($server->isCollected());
	}
}