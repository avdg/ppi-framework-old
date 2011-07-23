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
		$this->_data = array('foo' => 'bar', 'bar' => 'foo');
	}

	public function tearDown() {
		$this->_data = array();
	}

	public function testIsCollected() {

		$url = new PPI_Request_Url();
		$this->assertFalse($url->isCollected());

		$url = new PPI_Request_Url(array('drink' => 'beer'));
		$this->assertFalse($url->isCollected());

		$url = new PPI_Request_Url('/foo/bar/');
		$this->assertFalse($url->isCollected());
	}

	public function testCollectGetQuery()
	{
		$url = new PPI_Request_Url($this->_data);
		$this->assertEquals('foo', $url['bar']);
		$this->assertEquals('bar', $url['foo']);
		$this->assertEquals(null,  $url['random']);
		$this->assertFalse($url->isCollected());
	}

	public function testCustomGetQuery() {
		$url = new PPI_Request_Url(array('drink' => 'beer'));
		$this->assertEquals('beer', $url['drink']);
		$this->assertEquals(null,   $url['foo']);
		$this->assertEquals(null,   $url['random']);
		$this->assertFalse($url->isCollected());
	}

	public function testUrlString()
	{
		$url = new PPI_Request_Url('/drink/beer');
		$this->assertEquals('beer', $url['drink']);
		$this->assertEquals(null,   $url['beer']);

		$this->assertFalse($url->isCollected());
	}
}
