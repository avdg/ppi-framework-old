<?php
/**
* Unit test for PPI Session
*
* @package   Core
* @author    Paul Dragoonis <dragoonis@php.net>
* @license   http://opensource.org/licenses/mit-license.php MIT
* @link      http://www.ppiframework.com
*/
class PPI_SessionTest extends PHPUnit_Framework_TestCase {

	protected $_session = null;

	public function setUp() {
		$this->_session = new PPI_Session();
	}

	public function tearDown() {
		unset($this->_session);
	}

	public function testSession() {

		// Set
		$this->_session->set('foo', 'bar');
		$this->assertEquals('bar', $this->_session->get('foo'));

		// Exists
		$this->assertTrue($this->_session->exists('foo'));
		$this->assertFalse($this->_session->exists('foo2'));

		// Remove
		$this->_session->remove('foo');
		$this->assertFalse($this->_session->exists('foo'));

		// Remove All
		$this->_session->set('foo', 'bar');
		$this->_session->set('foo2', 'bar2');
		$this->_session->removeAll();
		$this->assertFalse($this->_session->exists('foo') && $this->_session->exists('foo2'));

	}

}