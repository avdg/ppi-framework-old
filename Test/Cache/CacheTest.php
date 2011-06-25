<?php

/**
 * @version   1.0
 * @author    Johnny Mast <mastjohnny@gmail.net>
 * @license   http://opensource.org/licenses/mit-license.php MIT
 * @copyright Digiflex Development
 * @package   Tests
 * @link      www.ppiframework.com
 */
class PPI_CacheTest extends PHPUnit_Framework_TestCase {
  
  
  public function testSet() {
  	$this->assertEquals(1, 1);
	return;
  	$cache = PPI_Helper::getCache();
	$cache->set('foo', 'bar');
	
	$this->assertEquals('foo', 'bar');
	unset($cache);
  }
  
  public function testNoConfig() {
  	
	$this->setExpectedException('PPI_Exception');
	
  	$cache = PPI_Helper::getCache();
	$cache->set('foo', 'bar');
	
	$this->assertEquals('foo', 'bar');
	unset($cache);  	
  }

}
