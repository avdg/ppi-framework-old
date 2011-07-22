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

	public function setUp() {}

	public function tearDown() {}

	public function testRemove() {
		PPI_Registry::set('foo', 'foo');
		PPI_Registry::remove('foo');
		$this->assertFalse(PPI_Registry::exists('foo'));
	}

	public function testSet() {
		PPI_Registry::set('foo', 'foo');
		$this->assertEquals('foo', PPI_Registry::get('foo'));
		PPI_Registry::remove('foo');
	}

	public function testIsset() {
		PPI_Registry::set('foo2', 'foo');
		$this->assertFalse(PPI_Registry::exists('foo'));
		$this->assertTrue(PPI_Registry::exists('foo2'));
		PPI_Registry::remove('foo2');
	}
}
