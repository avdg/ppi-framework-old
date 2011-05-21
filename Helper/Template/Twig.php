<?php

/**
 *
 * @version   1.0
 * @author    Paul Dragoonis <dragoonis@php.net>
 * @license   http://opensource.org/licenses/mit-license.php MIT
 * @copyright Digiflex Development
 * @package   View
 */

require_once SYSTEMPATH . 'Vendor/Twig/Autoloader.php';
class PPI_Helper_Template_Twig implements PPI_Interface_Template {

	private $_renderer = null;
	private $_viewVars = array();

	function __construct() {
		Twig_Autoloader::register();
		$sTheme     = PPI_Helper::getConfig()->layout->view_theme;
		$this->_renderer = new Twig_Environment(new Twig_Loader_Filesystem(VIEWPATH . "$sTheme/", array(
			'cache' => APPFOLDER . 'Cache/Twig/'
		)));
	}

	function render($p_sTplFile) {
		// Optional extension for twig templates
		$p_sTplFile = PPI_Helper::checkExtension($p_sTplFile, $this->getTemplateExtension());
		$sTheme     = PPI_Helper::getConfig()->layout->view_theme;
		$sPath      = VIEWPATH . "$sTheme/$p_sTplFile";
		if(!file_exists($sPath)) {
			throw new PPI_Exception('Unable to load: ' . $sPath . ' file does not exist');
		}
		$template = $this->_renderer->loadTemplate($p_sTplFile);
		$template->display($this->_viewVars);
	}

	function assign($key, $val) {
		$this->_viewVars[$key] = $val;
	}

	function getTemplateExtension() {
		$oConfig = PPI_Helper::getConfig();
		return !empty($oConfig->layout->rendererExt) ? $oConfig->layout->rendererExt : '.html';
	}

	function getDefaultMasterTemplate() {
		return 'template.html';
	}

}