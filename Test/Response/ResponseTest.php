<?php
/**
* Unit test for PPI Response
*
* @package   Core
* @author    Paul Dragoonis <dragoonis@php.net>
* @license   http://opensource.org/licenses/mit-license.php MIT
* @link      http://www.ppiframework.com
*/
class PPI_ResponseTest extends PHPUnit_Framework_TestCase {

	protected $_response = null;

	public function setUp() {
		$this->_response = new PPI_Response(array(
			'cssFiles' => array('foo', 'bar'),
			'jsFiles'  => array('foo', 'bar'),
			'charset'  => 'utf-8',
			'flash'    => array('mode' => 'failure', 'message' => 'There has been a failure')
		));
	}

	public function tearDown() {
		unset($this->_response);
	}

	public function testCSS() {

		// Set/Get
		$this->_response->addCSS('baz');
		$cssFiles = $this->_response->getCSSFiles();
		$this->assertEquals('foo', $cssFiles[0]);
		$this->assertEquals('bar', $cssFiles[1]);
		$this->assertEquals('baz', $cssFiles[2]);

		// Clear
		$this->_response->clearCSS();
		$cssFiles = $this->_response->getCSSFiles();
		$this->assertTrue(empty($cssFiles));

	}

	public function testJS() {

		// Set/Get
		$this->_response->addJS('baz');
		$jsFiles = $this->_response->getJSFiles();
		$this->assertEquals('foo', $jsFiles[0]);
		$this->assertEquals('bar', $jsFiles[1]);
		$this->assertEquals('baz', $jsFiles[2]);

		// Clear
		$this->_response->clearJS();
		$jsFiles = $this->_response->getJSFiles();
		$this->assertTrue(empty($jsFiles));

	}

	public function testCharset() {

		// Test Get/Set
		$this->assertEquals('utf-8', $this->_response->getCharset());

		// Test Override
		$this->_response->setCharset('foo');
		$this->assertEquals('foo', $this->_response->getCharset());

	}

	public function testFlash() {

		// Override
		$flash = $this->_response->getFlash();
		$this->assertTrue(isset($flash['mode'], $flash['message']));
		$this->assertEquals('failure', $flash['mode']);
		$this->assertEquals('There has been a failure', $flash['message']);

		// Set
		$this->_response->setFlash('New Message', true);
		$flash = $this->_response->getFlash();
		$this->assertTrue(isset($flash['mode'], $flash['message']));
		$this->assertEquals('success', $flash['mode']);
		$this->assertEquals('New Message', $flash['message']);

		// Clear
		$this->_response->clearFlash();
		$flash = $this->_response->getFlash();
		$this->assertTrue(empty($flash));

	}

}

