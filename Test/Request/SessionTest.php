<?php
/**
 * Unit test for the PPI Request GetQuery
 *
 * @package   Core
 * @author    Paul Dragoonis <dragoonis@php.net>
 * @license   http://opensource.org/licenses/mit-license.php MIT
 * @link      http://www.ppiframework.com
*/
class PPI_Request_SessionTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
	}

	public function tearDown() {
	}

	public function testIsCollected() {

		$session = new PPI_Request_Session();
		$this->assertTrue($session->isCollected());

		$session = new PPI_Request_Session(array('drink' => 'beer'));
		$this->assertFalse($session->isCollected());

		$session = new PPI_Request_Session(array());
		$this->assertFalse($session->isCollected());
	}

	public function testCustomSession() {
		$session = new PPI_Request_Session(array('drink' => 'beer'));
		$this->assertEquals('beer', $session['drink']);
		$this->assertEquals(null,   $session['foo']);
		$this->assertEquals(null,   $session['random']);
		$this->assertFalse($session->isCollected());
	}
}
