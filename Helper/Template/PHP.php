<?php

/**
 *
 * @version   1.0
 * @author    Paul Dragoonis <dragoonis@php.net>
 * @license   http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright Digiflex Development
 * @package   View
 */

class PPI_Helper_Template_PHP implements PPI_Interface_Template {

	private $_viewVars = array();

	function __construct() {}

	/**
	 * Actually load in the view and render it.
	 *
	 * @param string $p_sTemplateFile The filename to load, such as the master template
	 */
	function render($p_sTemplateFile) {
		// Optional extension for php templates
		$p_sTplFile = PPI_Helper::checkExtension($p_sTemplateFile, EXT);
		$sTheme     = PPI_Helper::getConfig()->layout->view_theme;
		$sPath      = VIEWPATH . "$sTheme/$p_sTemplateFile";
        if(!file_exists($sPath)) {
            throw new PPI_Exception('Unable to load template: ' . $sPath . ' file does not exist');
        }

		foreach($this->_viewVars as $key => $var) {
			$$key = $var;
		}

		include_once($sPath);
	}

	/**
	 * Assign a value for this current view
	 *
	 * @param string $key The variable name
	 * @param string $val The variable value
	 */
	function assign($key, $val) {
		$this->_viewVars[$key] = $val;
	}

	/**
	 * Get the default extension for our view files, config overridable defaulting to .php
	 *
	 * @return string
	 */
	function getTemplateExtension() {
		$oConfig = PPI_Helper::getConfig();
		return !empty($oConfig->layout->rendererExt) ? $oConfig->layout->rendererExt : '.php';
	}

	/**
	 * Get the default master template filename
	 *
	 * @return string
	 */
	function getDefaultMasterTemplate() {
		return 'template.php';
	}

}