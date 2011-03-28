<?php
/**
 * @version   1.0
 * @author    Paul Dragoonis <dragoonis@php.net>
 * @license   http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright Digiflex Development
 * @link      www.ppiframework.com
 * @package   PPI
 */
class PPI_View {

	protected $_viewParams = array();

	/**
	 * Are we loading a plugin view (TBC)
	 * @var boolean $_plugin
	 */
	private $_plugin = false;

	/**
	 * Default renderer, PHP helper
	 * @var string $_defaultRenderer
	 */
	private $_defaultRenderer = 'php';

	/**
	 * Master Template Override from config or setTemplateFile()
	 * @var string $_templateOverride
	 */
	private $_templateOverride = null;

	/**
	 * Template Renderer Override from config or useRenderer()
	 * @var string $_rendererOverride
	 */
	private $_rendererOverride = null;

	function __construct() {}

	/**
	 * Load function called from controllers
	 * @todo Make this alias to $this->render()
	 * @todo look into making this dynamic name rather than 'smarty', 'twig', 'php'
	 * @param string $p_tplFile The template filename
	 * @param array $p_tplParams Optional user defined params
	 */
	function load($p_tplFile, $p_tplParams = array()) {

		// checking for config overrides or useRenderer() overrides
		$oConfig = PPI_Helper::getConfig();
		if($this->_rendererOverride !== null) {
			$sRenderer = $this->_rendererOverride;
		} elseif(isset($oConfig->layout->renderer) && $oConfig->layout->renderer != '') {
			$sRenderer = $oConfig->layout->renderer;
		} else {
			$sRenderer = $this->_defaultRenderer;
		}

		switch($sRenderer) {
			case 'smarty':
				$oTpl = new PPI_Helper_Template_Smarty();
				break;

			case 'twig':
				$oTpl = new PPI_Helper_Template_Twig();
				break;

			case 'php':
			default:
				$oTpl = new PPI_Helper_Template_PHP();
				break;
		}

		$this->setupRenderer($oTpl, $p_tplFile, $p_tplParams, $oConfig);
	}

	/**
	 * Add a var to the view params
	 *
	 * @param string $p_sKey
	 * @param mixed $p_mVal
	 */
	function set($p_sKey, $p_mVal) {
		$this->_viewParams[$p_sKey] = $p_mVal;
	}

	/**
	 * Alias for $this->load() but forcing the renderer to smarty
	 *
	 * @param string $p_tplFile The template filename
	 * @param array $p_tplParams Optional user defined params
	 */
	function loadSmarty($p_tplFile, $p_tplParams = array()) {
		$this->setupRenderer(new PPI_Helper_Template_Smarty(), $p_tplFile, $p_tplParams, PPI_Helper::getConfig());
	}

	function useRenderer($p_sRendererName) {
		$this->_rendererOverride = $p_sRendererName;
	}

	/**
	 * Initialisation for the renderer, assignment of default values, boot up of the master template
	 *
	 * @param object $oTpl Templating renderer. Instance of PPI_Interface_Template
	 * @param string $p_tplFile The template file to render
	 * @param array $p_tplParams Optional user defined parameres
	 */
	function setupRenderer(PPI_Interface_Template $oTpl, $p_tplFile, $p_tplParams = array(), $p_oConfig) {

		$oSession = PPI_Helper::getSession();

		// Default View Values
		if(!empty($p_tplParams)) {
			foreach($p_tplParams as $key => $val) {
				$oTpl->assign($key, $val);
			}
		}

		$p_tplFile = PPI_Helper::checkExtension($p_tplFile, $oTpl->getTemplateExtension());
		// Plugin View Detection
		$sPath = (defined('PLUGINVIEWPATH') ? PLUGINVIEWPATH : APPFOLDER . 'View/');

		// View Directory Preparation By Theme
		$sViewDir = $sPath . $p_oConfig->layout->view_theme . '/';

		// Get the default view vars that come when you load a view page.
		$defaultViewVars = $this->getDefaultRenderValues(array(
			'viewDir'    => $sViewDir,
			'actionFile' => $p_tplFile
		), $p_oConfig);

		foreach($defaultViewVars as $varName => $viewVar) {
			$oTpl->assign($varName, $viewVar);
		}

		// Flash Messages
		if(!isset($p_oConfig->layout->useMessageFlash) ||
			($p_oConfig->layout->useMessageFlash && $p_oConfig->layout->useMessageFlash == true)) {
			$oTpl->assign('ppiFlashMessage', PPI_Input::getFlashMessage());
			PPI_Input::clearFlashMessage();
		}



		// Master template override from config or setTemplateFile()
		if($this->_templateOverride !== null) {
			$sMasterTemplate = $this->_templateOverride;
		} elseif(isset($p_oConfig->layout->masterFile) && $p_oConfig->layout->masterFile != '') {
			$sMasterTemplate = $p_oConfig->layout->masterFile;
		} else {
			$sMasterTemplate = $oTpl->getDefaultMasterTemplate();
		}

		$oTpl->render($sMasterTemplate);
	}

	/**
	 * Obtain the list of default view variables
	 * @todo review making var names not HNC prefixed.
	 * @param array $options
	 * @return array
	 */
	function getDefaultRenderValues(array $options, $p_oConfig) {

		$authData  = PPI_Helper::getSession()->getAuthData();
		$oDispatch = PPI_Helper::getDispatcher();
		$request   = array(
			'controller' => $oDispatch->getControllerName(),
			'method'     => $oDispatch->getMethodName()
		);
		return array(
			'isLoggedIn'      => !empty($authData),
			'config'          => $p_oConfig,
			'request'         => $request,
			'input'           => PPI_Helper::getInput(),
                        'authData'        => $authData,
			'baseUrl'         => $p_oConfig->system->base_url,
			'fullUrl'         => PPI_Helper::getFullUrl(),
			'currUrl'         => PPI_Helper::getCurrUrl(),
			'viewDir'         => $options['viewDir'],
			'actionFile'      => $options['actionFile'],
			'responseCode'    => PPI_Helper::getRegistry()->get('PPI_View::httpResponseCode', 200),
			'stylesheetFiles' => PPI_View_Helper::getStylesheets(),
			'javascriptFiles' => PPI_View_Helper::getJavascripts(),
                        'authInfo'        => $authData, // Do not use, just BC stuff
			'aAuthInfo'       => $authData, // Do not use, just BC stuff.
			'bIsLoggedIn'     => !empty($authData), // Do not use, just BC stuff
			'oConfig'         => $p_oConfig, // Do not use, just BC stuff
		);
	}

	/**
	 * To get a view variable that is set to get rendered. (TBC)
	 * @param string $key The Key
	 */
	function get($key) {
		if(!array_key_exists($key, $this->_viewParams)) {
			throw new PPI_Exception('Unable to find View Key: '.$key);
		}
		return $this->_viewParams[$key];
	}

    /**
     * PPI_View::addStylesheet()
     * Append to the list of stylesheets to be included
     * @param mixed $p_mStylesheet This can be an existing array of stylesheets or a string.
     * @return void
     */
    function addStylesheet($p_mStylesheet) {
        PPI_View_Helper::addStylesheet($p_mStylesheet);
    }

    /**
     * PPI_View::addJavascript()
     * Append to the list of javascript files to be included
     * @param mixed $p_mJavascript
     * @return void
     */
    function addJavascript($p_mJavascript) {
        PPI_View_Helper::addJavascript($p_mJavascript);
    }

	/**
	 * Override the default template file, with optional include for the .php or .tpl extension
	 * @param string $p_sNewTemplateFile New Template Filename
	 * @todo have this lookup the template engines default extension and remove the smarty param
     * @return void
	 */
	function setTemplateFile($p_sNewTemplateFile, $p_bUseSmarty = false) {
		$ext = ($p_bUseSmarty === true) ? '.tpl' : '.php';
		if(strripos($p_sNewTemplateFile, $ext) === false) {
			$p_sNewTemplateFile .= $ext;
		}
		$this->_templateOverride = $p_sNewTemplateFile;
	}

	/**
	 * Create an override for the renderer
	 * @todo add Twig to this list.
	 * @param string $p_sRendererName
	 */
	function setRenderer($p_sRendererName) {
		switch($p_sRendererName) {
			case 'php':
				throw new PPI_Exception('Not yet implemented.');
				break;

			case 'smarty':
				throw new PPI_Exception('Not yet implemented.');
				break;

			case 'twig':
			default:
				throw new PPI_Exception('Not yet implemented.');
				break;

		}

	}

	/**
	 * The internal render function, this is called by $this->load('template');
	 * @todo finish this, have it accept 'template' at first.
	 * @param string $p_sTemplate The template name to render
	 * @param array $p_aParams Optional parameters
	 * @param array $p_aOptions Optional options
	 */
	protected function render($p_sTemplate, $p_aParams = array(), $p_aOptions = array()) {

		if(!isset($p_aOptions['use_frame']) || $p_aOptions['use_frame'] == true) {
			// Load up the template

		} else {
			// Render just this template.

		}
	}
}
