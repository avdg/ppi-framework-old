<?php

/**
 *
 * @version   1.0
 * @author    Paul Dragoonis <dragoonis@php.net>
 * @license   http://opensource.org/licenses/mit-license.php MIT
 * @package   Core
 * @link      www.ppiframework.com
 *
 */
class PPI_Response {

	/**
	 * The charset
	 *
	 * @var string
	 */
	protected $_charset = 'utf-8';
	/**
	 * The JS files for rendering
	 *
	 * @var array
	 */
	protected $_jsFiles = array();
	/**
	 * The CSS files for rendering
	 *
	 * @var array
	 */
	protected $_cssFiles = array();

	public function __construct() {

	}

	/**
	 * Append to the list of javascript files to be included
	 *
	 * @param mixed $js
	 * @return void
	 */
	public function addJS($js) {
		$this->addJavascript($js);
	}

	/**
	 * Get the list of JS files
	 *
	 * @return array
	 */
	public function getJSFiles() {
		return $this->_jsFiles;
	}

	/**
	 * Append to the list of stylesheets to be included
	 *
	 * @param mixed $p_mStylesheet This can be an existing array of stylesheets or a string.
	 * @return void
	 */
	public function addStylesheet($p_mStylesheet) {

		switch (gettype($p_mStylesheet)) {
			case 'string':
				$this->_cssFiles[] = $p_mStylesheet;
				return;
			case 'array':
				foreach ($p_mStylesheet as $stylesheet) {
					$this->addStylesheet($stylesheet);
				}
		}
	}

	/**
	 * Append to the list of stylesheets to be included
	 *
	 * @param mixed $css This can be an existing array of stylesheets or a string.
	 * @return void
	 */
	public function addCSS($css) {
		$this->addStylesheet($css);
	}

	/**
	 * Clear the list of added css files
	 *
	 * @return void
	 */
	public function clearCSS() {
		$this->_cssFiles = array();
	}

	/**
	 * Get the list of CSS files
	 *
	 * @return array
	 */
	public function getCSSFiles() {
		return $this->_cssFiles;
	}

	/**
	 * Clear the list of added JS files
	 *
	 * @return void
	 */
	public function clearJS() {
		$this->_jsFiles = array();
	}

	/**
	 * Append to the list of javascript files to be included
	 *
	 * @param mixed $p_mJavascript
	 * @return void
	 */
	public function addJavascript($p_mJavascript) {

		switch (gettype($p_mJavascript)) {
			case 'string':
				$this->_jsFiles[] = $p_mJavascript;
				return;
			case 'array':
				foreach ($p_mJavascript as $javascriptFile) {
					$this->addJavascript($javascriptFile);
				}
		}
	}

	/**
	 * Set a flash message
	 *
	 * @param string $message
	 * @param bool $success
	 * @return void
	 */
	public function setFlash($message, $success = true) {

		PPI_Helper::getSession()->set('ppi_flash_message', array(
			'mode' => $success ? 'success' : 'failure',
			'message' => $message
		));
	}

	/**
	 * Get the flash messages
	 *
	 * @return mixed
	 */
	public function getFlash() {
		return PPI_Helper::getSession()->get('ppi_flash_message');
	}

	/**
	 * Get the flash message and then clear it
	 *
	 * @return void
	 */
	public function getFlashAndClear() {

		$flash = $this->getFlash();
		$this->clearFlash();
	}

	/**
	 * Clear the flash message
	 *
	 * @return array
	 */
	public function clearFlash() {
		return PPI_Helper::getSession()->remove('ppi_flash_message');
	}

	/**
	 * Get the charset
	 *
	 * @return string
	 */
	public function getCharset() {
		return $this->_charset;
	}
}