<?php
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

	function __construct() {}

	/**
	 * Append to the list of javascript files to be included
	 *
	 * @param mixed $js
	 * @return void
	 */
	function addJS($js) {
		$this->addJavascript($js);
	}

	/**
	 * Get the list of JS files
	 *
	 * @return array
	 */
	function getJSFiles() {
		return $this->_jsFiles;
	}

    /**
     * Append to the list of stylesheets to be included
     *
     * @param mixed $p_mStylesheet This can be an existing array of stylesheets or a string.
     * @return void
     */
	function addStylesheet($p_mStylesheet) {
		if(is_string($p_mStylesheet)) {
			$this->_cssFiles[] = $p_mStylesheet;
			return;
		}
		if(is_array($p_mStylesheet)) {
			foreach($p_mStylesheet as $stylesheet) {
				$this->addStylesheet($stylesheet);
			}
			return;
		}
	}

	/**
	 * Append to the list of stylesheets to be included
	 *
	 * @param mixed $css This can be an existing array of stylesheets or a string.
	 * @return void
	 */
	function addCSS($css) {
		$this->addStylesheet($css);
	}

	/**
	 * Clear the list of added css files
	 *
	 * @return void
	 */
	function clearCSS() {
		$this->_cssFiles = array();
	}

	/**
	 * Get the list of CSS files
	 *
	 * @return array
	 */
	function getCSSFiles() {
		return $this->_cssFiles;
	}

	/**
	 * Clear the list of added JS files
	 *
	 * @return void
	 */
	function clearJS() {
		$this->_jsFiles = array();
	}

    /**
     * Append to the list of javascript files to be included
     *
     * @param mixed $p_mJavascript
     * @return void
     */
	function addJavascript($p_mJavascript) {
		if(is_string($p_mJavascript)) {
			$this->_jsFiles[] = $p_mJavascript;
			return;
		}
		if(is_array($p_mJavascript)) {
			foreach($p_mJavascript as $javascriptFile) {
				$this->addJavascript($javascriptFile);
			}
			return;
		}
	}

	/**
	 * Set a flash message
	 *
	 * @param string $message
	 * @param bool $success
	 * @return void
	 */
	function setFlash($message, $success = true) {
        PPI_Helper::getSession()->set('ppi_flash_message', array(
            'mode'    => $success ? 'success' : 'failure',
            'message' => $message
        ));
	}

	/**
	 * Get the flash messages
	 *
	 * @return mixed
	 */
	function getFlash() {
		return PPI_Helper::getSession()->get('ppi_flash_message');
	}

	/**
	 * Get the flash message and then clear it
	 *
	 * @return void
	 */
	function getFlashAndClear() {
		$flash = $this->getFlash();
		$this->clearFlash();
	}

	/**
	 * Clear the flash message
	 *
	 * @return array
	 */
	function clearFlash() {
		return PPI_Helper::getSession()->remove('ppi_flash_message');
	}

	/**
	 * Get the charset
	 *
	 * @return string
	 */
	function getCharset() {
		return $this->_charset;
	}

}