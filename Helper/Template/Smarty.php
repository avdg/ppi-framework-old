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
		$this->_smartyPath               = SYSTEMPATH . 'Vendor/Smarty/';

		$this->_renderer->_tpl_vars   	 = array();
		$this->_renderer->template_dir   = $this->_viewPath;
		$this->_renderer->compile_dir 	 = $this->_smartyPath.'templates_c';
		$this->_renderer->cache_dir 	 = $this->_smartyPath.'cache';
		$this->_renderer->config_dir 	 = $this->_smartyPath.'configs';
		$this->_renderer->force_compile  = isset($oConfig->system->smarty_compile) ? (bool) $oConfig->system->smarty_compile : false;
		$this->_renderer->caching 		 = isset($oConfig->system->enable_caching) ? (bool) $oConfig->system->enable_caching : false;
	}

	function render($p_sTplFile) {
		// Optional extension for smarty templates
		$p_sTplFile = PPI_Helper::checkExtension($p_sTplFile, SMARTY_EXT);
		$sTheme     = PPI_Helper::getConfig()->layout->view_theme;
		$sPath      = 	$this->_viewPath. "$sTheme/$p_sTplFile";
        if(!file_exists($sPath)) {
            throw new PPI_Exception('Unable to load: ' . $sPath . ' file does not exist');
        }
		return $this->_renderer->display($sPath);
	}

	function assign($key, $val) {
		return $this->_renderer->assign($key, $val);
	}

	function import($p_sFilename = "") {
		$oConfig = PPI_Helper::getConfig();
		$sPrefix = $oConfig->layout->default_view_language;
		if (!(strstr ($p_sFilename, SMARTY_EXT))) {
			$p_sFilename .= SMARTY_EXT;
		}
		parent::assign('pathprefix', $sPrefix.'/');
		return parent::fetch ($sPrefix.'/'.$p_sFilename);
	}

	function getTemplateExtension() {
		$oConfig = PPI_Helper::getConfig();
		return !empty($oConfig->layout->rendererExt) ? $oConfig->layout->rendererExt : '.tpl';
	}

	function getDefaultMasterTemplate() {
		return 'template.tpl';
	}

}