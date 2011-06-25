<?php

class PPI_Request_GetTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
		$_GET = array('foo' => 'bar', 'bar' => 'foo');
	}

	public function tearDown() {
		$_GET = array();
	}

	public function testIsCollected() {
		$get = new PPI_Request_Get();
		$this->assertTrue($get->isCollected());

		$get = new PPI_Request_Get(array('drink' => 'beer'));
		$this->assertFalse($get->isCollected());

		$get = new PPI_Request_Get(array());
		$this->assertTrue($get->isCollected());
	}
/*
	public function testCollectGet()
	{
		$get = new PPI_Request_Get;
		$this->assertEquals('foo', $get['bar']);
		$this->assertEquals('bar', $get['foo']);
		$this->assertEquals(null,  $get['random']);
		$this->assertTrue($get->isCollected());
	}
*/
	public function testCustomGet() {
		$get = new PPI_Request_Get(array('drink' => 'beer'));
		$this->assertEquals('beer', $get['drink']);
		$this->assertEquals(null,   $get['foo']);
		$this->assertEquals(null,   $get['random']);
		$this->assertFalse($get->isCollected());
	}
}
