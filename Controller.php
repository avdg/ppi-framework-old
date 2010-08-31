<?php
	/**
	 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
	 * @copyright (c) Digiflex Development Team 2008
	 * @author Paul Dragoonis <dragoonis@php.net>
	 */
class PPI_Controller {

	protected $_input = null;

	function __construct () {
		$this->_input = PPI_Helper::getInput();
		$this->_view  = new PPI_View();
		$this->oInput = $this->_input; // Legacy Code
	}

	/**
	 * Perform redirect to internal framework url. Optionally redirect to external host
	 * @param string $p_sURL Optional param for where to redirect to
	 * @param boolean $p_bPrependBase Default is true. If true will prepend the framework's base Url. 
 	 *									If false will redirect to absolute external url.
	 */
	protected function redirect($p_sURL="", $p_bPrependBase = true) {
		$sUrl = ($p_bPrependBase === true) ? $this->getConfig()->system->base_url . $p_sURL : $p_sURL;
		if($this->getCurrUrl() == $sUrl) {
			return false;
		}
		if(!headers_sent()) {
			header("Location: $sUrl");
			exit;
		} else {
			throw new PPI_Exception('Unable to redirect to '.$sUrl.'. Headers already sent');
		}
	}
	
	protected function load($p_tplFile, $p_tplParams = array()) {
		$this->_view->load($p_tplFile, $p_tplParams);
	}
	
	protected function loadSmarty($p_tplFile, $p_tplParams = array()) {
		$this->_view->loadsmarty($p_tplFile, $p_tplParams);
	}
	
    /**
     * PPI_View::addStylesheet()
     * Append to the list of stylesheets to be included
     * @param mixed $p_mStylesheet This can be an existing array of stylesheets or a string.
     * @return void
     */
    protected function addStylesheet($p_mStylesheet) {
        $this->_view->addStylesheet($p_mStylesheet);
    }

    /**
     * PPI_View::addJavascript()
     * Append to the list of javascript files to be included
     * @param mixed $p_mJavascript
     * @return void
     */
    protected function addJavascript($p_mJavascript) {
        $this->_view->addJavascript($p_mJavascript);
    }

	/**
	 * Override the default template file, with optional include for the .php or .tpl extension
	 * @param string $p_sNewTemplateFile New Template Filename
	 * @todo have this lookup the template engines default extension and remove the smarty param
     * @return void
	 */
	protected function setTemplateFile($p_sNewTemplateFile, $p_bUseSmarty = false) {
		$this->_view->setTemplateFile($p_sNewTemplateFile, $p_bUseSmarty);
	}
			

	/**
	 * Setter for setting the flash message to appear on next page load.
	 * @return void
	 */
	protected function setFlashMessage($p_sMessage, $p_bSuccess = true) {
		PPI_Input::setFlashMessage($p_sMessage, $p_bSuccess);
	}

	/**
	 * PPI_Controller::getFlashMessage()
	 * Getter for the flash message.
	 * @return void
	 */
	protected function getFlashMessage() {
		PPI_Input::getFlashMessage($p_sMessage, $p_bSuccess);
	}

	/**
	 * PPI_Controller::clearFlashMessage()
	 * Clear the flash message from the session
	 * @return void
	 */
	protected function clearFlashMessage() {
		PPI_Input::clearFlashMessage();
	}

    /**
     * Get the full current URI
     * @todo Maybe just strip off baseUrl from the URL and that's our URI
     */
	protected function getCurrUrl() {
		return PPI_Helper::getCurrUrl();
	}
	
	/**
	 * Get the full URL
	 *
	 * @return string
	 */
	protected function getFullUrl() {
		return PPI_Helper::getFullUrl();
	}

	/**
	 * PPI_Controller::getBaseUrl()
	 * Get the base url set in the config
	 * @return string
	 */
	protected function getBaseUrl() {
		return $this->getConfig()->system->base_url;
	}

	/**
	 * PPI_Controller::getConfig()
	 * Returns the PPI_Config object
	 * @return object
	 */
	protected function getConfig() {
		return PPI_Helper::getConfig();
	}

	/**
	 * PPI_Controller::getSession()
	 * Returns the session object
	 * @return object PPI_Model_Session
	 */
	protected function getSession() {
		return PPI_Helper::getSession();
	}

	/**
	 * PPI_Controller::getSession()
	 * Returns the session object
	 * @return object PPI_Model_Session
	 */
	protected function getRegistry() {
		return PPI_Helper::getRegistry();
	}

	/**
	 * Checks if the current user is logged in
	 * @return boolean
	 */
	protected function isLoggedIn() {
		$aAuthData = $this->getAuthData();
		return !empty($aAuthData);
	}

	/**
	 * Gets the current logged in users authentication data
     * @param boolean $p_bUseArray Default is true. If false then will return an object instead
	 * @return array|object
	 */
	protected function getAuthData($p_bUseArray = true) {
		$authData = $this->getSession()->getAuthData();
		return $p_bUseArray ? $authData : (object) $authData;
	}
	
	protected function get($p_sVar, $p_mDefault = null) {
		return $this->_input->get($p_sVar, $p_mDefault);
	}
	
	protected function post($p_sVar = null, $p_mDefault = null) {
		return $this->_input->post($p_sVar, $p_mDefault);
	}	
	
	protected function hasPost($p_sKey) {
		return $this->_input->hasPost($p_sKey);
	}
	
	protected function isPost() {
		return $this->_input->isPost();
	}
	
	protected function stripPost($p_sPrefix) {
		return $this->_input->stripPost($p_sPrefix)
	}

}