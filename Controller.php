<?php

/**
	 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
	 * @copyright (c) Digiflex Development Team 2008
	 * @version 1.0
	 * @author Paul Dragoonis <dragoonis@php.net>
	 * @since Version 1.0
	 */
class PPI_Controller extends PPI_View {

	protected $_input = null;

	function __construct ($p_preloadModels = array(), $p_ControllerType = PPI_CONTROLLER) {
		$this->_input = PPI_Input::getInstance();
		$this->oInput = PPI_Input::getInstance();
	}

    /**
     * In progress. Functions made but not active
     */
	function checkAuth() {
		var_dump(PPI_Acl::getInstance()->hasAccess(false, false, false, true));
	}

    /**
     * Perform redirect to internal framework url. Optionally redirect to external host
     * @param string $p_sURL Optional param for where to redirect to
     * @param boolean $p_bPrependBase Default is true. If true will prepend the framework's base Url. 
     * 									If false will redirect to absolute external url.
     */
	protected function _redirect($p_sURL="", $p_bPrependBase = true) {
		$this->redirect($p_sURL, $p_bPrependBase);
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


    protected function _setFlashMessage($p_sMessage, $p_bSuccess = true) {
        $this->setFlashMessage($p_sMessage, $p_bSuccess);
    }

    protected function _getFlashMessage() {
        $this->getFlashMessage();
    }

    protected function _clearFlashMessage() {
    	$this->clearFlashMessage();
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

}