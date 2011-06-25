<?php

class PPI_Request_PostTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
		$_POST = array('foo' => 'bar', 'bar' => 'foo');
	}

	public function tearDown() {
		$_POST = array();
	}

	public function testIsCollected() {
		$post = new PPI_Request_Post;
		$this->assertTrue($post->isCollected());

		$post = new PPI_Request_Post(array('drink' => 'beer'));
		$this->assertFalse($post->isCollected());

		$post = new PPI_Request_Post(array());
		$this->assertTrue($post->isCollected());
	}

	public function testCollectPost() {
		$post = new PPI_Request_Post;
		$this->assertEquals('foo', $post['bar']);
		$this->assertEquals('bar', $post['foo']);
		$this->assertEquals(null,  $post['random']);
		$this->assertTrue($post->isCollected());
	}

	public function testCustomPost() {
		$post = new PPI_Request_Post(array('drink' => 'beer'));
		$this->assertEquals('beer', $post['drink']);
		$this->assertEquals(null,   $post['foo']);
		$this->assertEquals(null,   $post['random']);
		$this->assertFalse($post->isCollected());
	}
}