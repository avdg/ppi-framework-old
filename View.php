<?php
/**
 * @author    Paul Dragoonis <dragoonis@php.net>
 * @license   http://opensource.org/licenses/mit-license.php MIT
 * @link      www.ppiframework.com
 * @package   View
 */
class PPI_View {

    /**
     * The variables to be rendered into the view file
     *
     * @var array
     */
	protected $_viewParams = array();

	/**
	 * The current set view theme
	 *
	 * @var null|string
	 */
	protected $_viewTheme = null;

	/**
	 * The master template file
	 *
	 * @var null|string
	 */
	protected $_masterTemplateFile = null;

	/**
	 * Are we loading a plugin view (TBC)
     *
	 * @var boolean $_plugin
	 */
	private $_plugin = false;

	/**
	 * Default renderer, PHP helper
     *
	 * @var string $_defaultRenderer
	 */
	private $_defaultRenderer = 'php';

	/**
	 * Template Renderer Override from config or useRenderer()
     *
	 * @var string $_rendererOverride
	 */
	private $_rendererOverride = null;

	/**
	 * The constructor
	 *
	 * @todo - When this is instantiated, pass it an options array,
	 * @todo - Get the skeleton app to pass $config->layout->toArray()
	 * @param array $options The options
	 */
	function __construct(array $options = array()) {
		if(isset($options['view_theme'])) {
			$this->_viewTheme = $options['view_theme'];
		}

		$this->_config = PPI_Helper::getConfig();
	}

	/**
	 * Load function called from controllers
     *
	 * @todo Make this alias to $this->render()
	 * @todo look into making this dynamic name rather than 'smarty', 'twig', 'php'
	 * @param string $p_tplFile The template filename
	 * @param array $p_tplParams Optional user defined params
     * @return void
	 */
	function load($p_tplFile, $p_tplParams = array()) {

		if(isset($this->_config->layout->renderer) && $this->_config->layout->renderer != '') {
			$sRenderer = $this->_config->layout->renderer;
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

		$this->setupRenderer($oTpl, $p_tplFile, array_merge($p_tplParams, $this->_viewParams));
	}

	/**
	 * Add a var to the view params
	 *
	 * @param string $p_sKey
	 * @param mixed $p_mVal
     * @return void
	 */
	function set($p_sKey, $p_mVal) {
		$this->_viewParams[$p_sKey] = $p_mVal;
	}

	/**
	 * Alias for $this->load() but forcing the renderer to smarty
	 *
	 * @param string $p_tplFile The template filename
	 * @param array $p_tplParams Optional user defined params
     * @return void
	 */
	function loadSmarty($p_tplFile, array $p_tplParams = array()) {
		$this->setupRenderer(new PPI_Helper_Template_Smarty(), $p_tplFile, $p_tplParams);

	}

	/**
	 * Override the current set theme
	 *
	 * @param string $p_sThemeName
	 * @return void
	 */
	function theme($p_sThemeName) {
		$this->_viewTheme = $p_sThemeName;
	}

	/**
	 * Get the currently set view theme
	 *
	 * @return string
	 */
	protected function getViewTheme() {
		if($this->_viewTheme === null) {
			$this->_viewTheme = $this->_config->layout->view_theme;
		}
		return $this->_viewTheme;
	}

    /**
     * Specify the renderer to use at runtime
     *
     * @param string $p_sRendererName The renderer name
     * @return void
     */
	function useRenderer($p_sRendererName) {
		$this->_rendererOverride = $p_sRendererName;
	}

	/**
	 * Initialisation for the renderer, assignment of default values, boot up of the master template
	 *
	 * @param PPI_Interface_Template $oTpl Templating renderer. Instance of PPI_Interface_Template
	 * @param string $p_tplFile The template file to render
	 * @param array $p_tplParams Optional user defined parameres
     * @return void
	 */
	function setupRenderer(PPI_Interface_Template $oTpl, $p_tplFile, $p_tplParams = array()) {

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
		$sViewDir = $sPath . $this->getViewTheme() . '/';

		// Get the default view vars that come when you load a view page.
		$defaultViewVars = $this->getDefaultRenderValues(array(
			'viewDir'    => $sViewDir,
			'actionFile' => $p_tplFile
		));
		foreach($defaultViewVars as $varName => $viewVar) {
			$oTpl->assign($varName, $viewVar);
		}

		// Flash Messages
		if(!isset($this->_config->layout->useMessageFlash) ||
			($this->_config->layout->useMessageFlash && $this->_config->layout->useMessageFlash == true)) {
			$oTpl->assign('ppiFlashMessage', PPI_Input::getFlashMessage());
			PPI_Input::clearFlashMessage();
		}

		// Master template
		$sMasterTemplate = $this->_masterTemplateFile !== null ? $this->_masterTemplateFile : $oTpl->getDefaultMasterTemplate();
		$sMasterTemplate = PPI_Helper::checkExtension($sMasterTemplate, $oTpl->getTemplateExtension());

		// Lets render baby !!
		$oTpl->render($sMasterTemplate);
	}

	/**
	 * Obtain the list of default view variables
     *
	 * @todo review making var names not HNC prefixed.
	 * @param array $options
	 * @return array
	 */
	function getDefaultRenderValues(array $options) {

		$authData  = PPI_Helper::getSession()->getAuthData();
		$request = array(
			'controller' => '',
			'method'     => ''
		);

		$registry = PPI_Helper::getRegistry();

		// Sometimes a render is forced before the PPI_Dispatch object has finished instantiating
		// For example if a 404 is thrown inside the routing/dispatch process then this scenario occurs.
		if($registry->exists('PPI_Dispatch')) {
			$oDispatch = PPI_Helper::getDispatcher();
			$request   = array(
				'controller' => $oDispatch->getControllerName(),
				'method'     => $oDispatch->getMethodName()
			);
		}
		/*
		if($registry->exists('PPI_Request')) {
			$oRequest = $registry->get('PPI_Request');
		}
		*/
		return array(
			'isLoggedIn'      => !empty($authData),
			'config'          => $this->_config,
			'request'         => $request,
            'authData'        => $authData,
			'baseUrl'         => $this->_config->system->base_url,
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
			'oConfig'         => $this->_config, // Do not use, just BC stuff
		);
	}

	/**
	 * To get a view variable that is set to get rendered. (TBC)
     *
	 * @param string $key The Key
     * @return mixed
	 */
	function get($key) {
		if(!array_key_exists($key, $this->_viewParams)) {
			throw new PPI_Exception('Unable to find View Key: '.$key);
		}
		return $this->_viewParams[$key];
	}

    /**
     * Append to the list of stylesheets to be included
     *
     * @param mixed $p_mStylesheet This can be an existing array of stylesheets or a string.
     * @return void
     */
    function addStylesheet($p_mStylesheet) {
        PPI_View_Helper::addStylesheet($p_mStylesheet);
    }

    /**
     * Append to the list of javascript files to be included
     *
     * @param mixed $p_mJavascript
     * @return void
     */
    function addJavascript($p_mJavascript) {
        PPI_View_Helper::addJavascript($p_mJavascript);
    }

	/**
	 * Override the default template file, with optional include for the .php or .tpl extension
	 *
	 * @todo have this lookup the template engines default extension and remove the smarty param
	 * @param string $p_sNewTemplateFile New Template Filename
	 * @return void
	 */
	function setTemplateFile($p_sNewTemplateFile) {
		$this->_masterTemplateFile = $p_sNewTemplateFile;
	}

	/**
	 * Create an override for the renderer
     *
	 * @todo add Twig to this list.
	 * @param string $p_sRendererName The renderer name
     * @throws PPI_Exception
     * @return void
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
     *
	 * @todo finish this, have it accept 'template' at first.
	 * @param string $p_sTemplate The template name to render
	 * @param array $p_aParams Optional parameters
	 * @param array $p_aOptions Optional options
     * @return void
	 */
	protected function render($p_sTemplate, $p_aParams = array(), $p_aOptions = array()) {

		if(!isset($p_aOptions['use_frame']) || $p_aOptions['use_frame'] == true) {
			// Load up the template

		} else {
			// Render just this template.

		}
	}
}
