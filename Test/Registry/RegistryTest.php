<?php
/**
* Unit test for the PPI Registry
*
* @package   Core
* @author    Paul Dragoonis <dragoonis@php.net>
* @license   http://opensource.org/licenses/mit-license.php MIT
* @link      http://www.ppiframework.com
*/
class PPI_RegistryTest extends PHPUnit_Framework_TestCase {

	protected $_reg = null;

	public function setUp() {
		$this->_reg = PPI_Registry::getInstance();
	}

	public function tearDown() {
		unset($this->_reg);
	}

	public function testRemove() {
		$this->_reg->set('foo', 'foo');
		$this->_reg->remove('foo');
		$this->assertFalse($this->_reg->exists('foo'));
	}

	public function testSet() {
		$this->_reg->set('foo', 'foo');
		$this->assertEquals('foo', $this->_reg->get('foo'));
		$this->_reg->remove('foo');
	}

	public function testIsset() {
		$this->_reg->set('foo2', 'foo');
		$this->assertFalse($this->_reg->exists('foo'));
		$this->assertTrue($this->_reg->exists('foo2'));
		$this->_reg->remove('foo2');
	}

	public function testDoubleInstance() {
		$this->setExpectedException('PPI_Exception');
		$this->_reg->setInstance(new PPI_Registry());
	}
}
