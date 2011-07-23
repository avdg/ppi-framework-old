<?php
/**
 * Unit test for the PPI Request Get
 *
 * @package   Core
 * @author    Paul Dragoonis <dragoonis@php.net>
 * @license   http://opensource.org/licenses/mit-license.php MIT
 * @link      http://www.ppiframework.com
*/
class PPI_Request_UrlTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
		$_Url = array('foo' => 'bar', 'bar' => 'foo');
	}

	public function tearDown() {
		$_Url = array();
	}

	public function testIsCollected() {

		$get = new PPI_Request_Url();
		$this->assertTrue($get->isCollected());

		$get = new PPI_Request_Url(array('drink' => 'beer'));
		$this->assertFalse($get->isCollected());

		$get = new PPI_Request_Url(array());
		$this->assertFalse($get->isCollected());
	}

	public function testCollectGetQuery()
	{
		$get = new PPI_Request_Url;
		$this->assertEquals('foo', $get['bar']);
		$this->assertEquals('bar', $get['foo']);
		$this->assertEquals(null,  $get['random']);
		$this->assertTrue($get->isCollected());
	}

	public function testCustomGetQuery() {
		$get = new PPI_Request_Url(array('drink' => 'beer'));
		$this->assertEquals('beer', $get['drink']);
		$this->assertEquals(null,   $get['foo']);
		$this->assertEquals(null,   $get['random']);
		$this->assertFalse($get->isCollected());
	}
}
