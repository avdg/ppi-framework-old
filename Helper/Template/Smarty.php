<?php

/**
 *
 * @version   1.0
 * @author    Paul Dragoonis <dragoonis@php.net>
 * @license   http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright Digiflex Development
 * @package   PPI
 */

require_once SYSTEMPATH . 'Vendor/Smarty/class.Smarty.php';
class PPI_Helper_Template_Smarty implements PPI_Interface_Template {


	private $_renderer = null;
	private $_viewPath = null;
	private $_smartyPath = null;
	function __construct() {
		$oConfig                         = PPI_Helper::getConfig();
		$this->_renderer                 = new Smarty();

		$this->_viewPath                 = APPFOLDER . 'View/';
		$this->_cachePath                = APPFOLDER . 'Cache/Smarty/';
		$this->_smartyPath               = SYSTEMPATH . 'Vendor/Smarty/';

		$this->_renderer->_tpl_vars   	 = array();
		$this->_renderer->template_dir   = $this->_viewPath;
		$this->_renderer->compile_dir 	 = $this->_cachePath.'templates_c';
		$this->_renderer->cache_dir 	 = $this->_cachePath.'cache';
		$this->_renderer->config_dir 	 = $this->_smartyPath.'configs';
		$this->_renderer->force_compile  = isset($oConfig->system->smarty_compile) ? (bool) $oConfig->system->smarty_compile : false;
		$this->_renderer->caching 		 = isset($oConfig->system->enable_caching) ? (bool) $oConfig->system->enable_caching : false;
	}

	
	/**
	 * Render the actual view file.
	 *
	 * @param string $p_sTplFile The template to load up. For example the master template.
	 * @throws PPI_Exception
	 */
	function render($p_sTplFile) {
		// Optional extension for smarty templates
		$p_sTplFile = PPI_Helper::checkExtension($p_sTplFile, SMARTY_EXT);
		$sTheme     = PPI_Helper::getConfig()->layout->view_theme;
		$sPath      = 	$this->_viewPath. "$sTheme/$p_sTplFile";
        if(!file_exists($sPath)) {
            throw new PPI_Exception('Unable to load: ' . $sPath . ' file does not exist');
        }
		$this->_renderer->display($sPath);
	}

	/**
	 * Assign a variable to the view
	 *
	 * @param string $key The variable name
	 * @param string $val The variable value
	 */
	function assign($key, $val) {
		$this->_renderer->assign($key, $val);
	}

	/**
	 * Get the view file extension. Config overridable defaulting to .tpl
	 *
	 * @return string
	 */
	function getTemplateExtension() {
		$oConfig = PPI_Helper::getConfig();
		return !empty($oConfig->layout->rendererExt) ? $oConfig->layout->rendererExt : '.tpl';
	}

	/**
	 * Get the default master template
	 *
	 * @return string
	 */
	function getDefaultMasterTemplate() {
		return 'template.tpl';
	}

}