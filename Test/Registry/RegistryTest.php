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

  public function startUp() {
     $this->_reg = PPI_Registry::getInstance();
  }

  public function testSet()
  {
  	$reg = PPI_Registry::getInstance();
	$reg->set('foo', 'foo');

	$this->assertEquals('foo', $reg->get('foo'));
	$reg->remove('foo');
	unset($reg);
  }

  public function testIsset() {
   	$reg = PPI_Registry::getInstance();
	$reg->set('foo2', 'foo');
	$this->assertFalse($reg->exists('foo'));

	$reg->remove('foo2');
	unset($reg);
  }

  public function testDoubleInstance() {
  	 $reg = PPI_Registry::getInstance();
	 $this->setExpectedException('PPI_Exception');

	 $reg->setInstance( new PPI_Registry());
	 unset($reg);
  }
}
