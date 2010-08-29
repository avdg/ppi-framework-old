<?php

/**
 *
 * @version   1.0
 * @author    Paul Dragoonis <dragoonis@php.net>
 * @license   http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright Digiflex Development
 * @package   PPI
 * @subpackage core
 */

class PPI_Session {

    private $_authNamespaceName		= 'userAuthData';

    /**
     * Default value for the session name for the app.
     * This can be overritten by the config
     * @var string
     */
	private $_sessionName			= 'myproject';

	/**
	 * The default namespace name for the framework.
	 * This can be overitten by the config
	 * @var unknown_type
	 */
    private $_frameworkSessionName	= '__PPI';

    /**
     * The PPI_Session instance
     * @var object
     */
    private static $_instance = null;

    /**
     * Setup the session namespace
     *
     */
    function __construct() {
    	global $oConfig;
    	if(isset($oConfig->system->sessionNamespace)) {
        	$this->_sessionName = $this->_frameworkSessionName . '_' . $oConfig->system->sessionNamespace;
    	}
        if(!array_key_exists($this->_sessionName, $_SESSION)) {
        	$_SESSION[$this->_sessionName] = array();
        }
    }

    /**
	 * Set the authentication information for the current user
	 *
	 * @param array $aData The data to be set
	 * @return void
	 */
	function setAuthData(array $aData) {
		global $oConfig;
		if(isset($oConfig->system->user_session_key)) {
			$this->_authNamespaceName = $oConfig->system->user_session_key;
		}
		$this->set($this->_authNamespaceName, $aData);
	}

	/**
	 * Clear the set auth data
	 * @return void
	 */
	function clearAuthData() {
		$this->set($this->_authNamespaceName, null);
	}

	/**
	 * Get the auth data, if it doesn't exist we return a blank array
	 * @return array
	 */
	function getAuthData($p_bUseArray = true) {
		$aAuthData = $this->get($this->_authNamespaceName, false);
		$aAuthData = ($aAuthData !== false && !empty($aAuthData)) ? $aAuthData : array();
		return $p_bUseArray === true ? $aAuthData : (object) $aAuthData;
	}

	function exists($p_sNamespace) {
		return array_key_exists($p_sNamespace, $_SESSION[$this->_sessionName]);
	}
	
	function removeAll() {
		foreach((array)$_SESSION[$this->_sessionName] as $key => $val) {
			unset($_SESSION[$this->_sessionName][$key]);
		}
	}	

	function remove($p_sNamespace, $p_sName = null) {
		if($this->exists($p_sNamespace)) {
			if($p_sName === null) {
				unset($_SESSION[$this->_sessionName][$p_sNamespace]);
			} else {
				unset($_SESSION[$this->_sessionName][$p_sNamespace][$p_sName]);
			}
		}
	}

	/**
	 * Get information from the session by key
	 *
	 * @param string $p_sNamespace
	 * @param mixed $p_mDefault Optional. Default is null
	 * @return mixed
	 */
	function get($p_sNamespace, $p_mDefault = null) {
		return ($this->exists($p_sNamespace)) ? $_SESSION[$this->_sessionName][$p_sNamespace] : $p_mDefault;
	}


	/**
	 * Set data into the session
	 *
	 * @param string $p_sNamespace
	 * @param mixed $p_mData
	 */
	function set($p_sNamespace, $p_mData = true) {
		$_SESSION[$this->_sessionName][$p_sNamespace] = $p_mData;
	}

}