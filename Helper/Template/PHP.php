<?php
/**
 * @author    Paul Dragoonis <dragoonis@php.net>
 * @license   http://opensource.org/licenses/mit-license.php MIT
 * @package   View
 * @package   www.ppiframework.com
 */
class PPI_Helper_Template_PHP implements PPI_Interface_Template {

    /**
     * The variables that are to be rendered in the View file
     *
     * @var array
     */
	protected $_viewVars = array();

    /**
     * The constructor
     *
     * @param array $options
     */
	function __construct(array $options = array()) {
		if(isset($options['config'])) {
			$this->_config = $options['config'];
		} else {
			$this->_config = PPI_Helper::getConfig();
		}
	}

	/**
	 * Actually load in the view and render it.
	 *
	 * @param string $p_sTemplateFile The filename to load, such as the master template
     * @return void
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
     * @return void
	 */
	function assign($key, $val) {
		$this->_viewVars[$key] = $val;
	}

	/**
	 * Get the default extension for our view files, config override or defaulting to .php
	 *
	 * @return string
	 */
	function getTemplateExtension() {
		return !empty($this->_config->layout->rendererExt) ? $this->_config->layout->rendererExt : '.php';
	}

	/**
	 * Get the default master template filename
	 *
	 * @return string
	 */
	function getDefaultMasterTemplate() {
		return 'template.php';
	}

	/**
	 * Get the default file extension for templates on this renderer
	 *
	 * @return string
	 */
	function getDefaultExtension() {
		return '.php';
	}

}