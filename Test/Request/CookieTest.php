<?php
/**
 * Unit test for the PPI Request Cookie
 *
 * @package   Core
 * @author    Paul Dragoonis <dragoonis@php.net>
 * @license   http://opensource.org/licenses/mit-license.php MIT
 * @link      http://www.ppiframework.com
*/
class PPI_Request_CookieTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
	}

	public function tearDown() {
	}

	public function testIsCollected() {

		$cookie = new PPI_Request_Cookie();
		$this->assertTrue($cookie->isCollected());

		$cookie = new PPI_Request_Cookie(array('drink' => 'beer'));
		$this->assertFalse($cookie->isCollected());

		$cookie = new PPI_Request_Cookie(array());
		$this->assertFalse($cookie->isCollected());
	}

	public function testCustomGet() {
		$cookie = new PPI_Request_Cookie(array('drink' => 'beer'));
		$this->assertEquals('beer', $cookie['drink']);
		$this->assertEquals(null,   $cookie['foo']);
		$this->assertEquals(null,   $cookie['random']);
		$this->assertFalse($cookie->isCollected());
	}
}
