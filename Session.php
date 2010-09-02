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

    private $_authKeyName = 'userAuthInfo';

    /**
     * Default value for the session name for the app.
     * This can be overritten by the config
     * @var string $_sessionName
     */
	private $_sessionName = 'myproject';

	/**
	 * The default namespace name for the framework.
	 * This can be overitten by the config: system.sessionNamespace
	 * @var string $_frameworkSessionName
	 */
    private $_frameworkSessionName = '__PPI';

    /**
     * The PPI_Session instance
     * @var object $_instance
     */
    private static $_instance = null;

    /**
     * Setup the session namespace
     *
     */
    function __construct() {
    	$oConfig = PPI_Helper::getConfig();
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
	 * @param mixed $aData The data to be set
	 * @return void
	 */
	function setAuthData($mData) {
		$oConfig = PPI_Helper::getConfig();
		if(isset($oConfig->system->user_session_key)) {
			$this->_authKeyName = $oConfig->system->user_session_key;
		}
		$this->set($this->_authKeyName, $mData);
	}

	/**
	 * Clear the set authentication information
	 * @return void
	 */
	function clearAuthData() {
		$this->set($this->_authKeyName, null);
	}

	/**
	 * Get the auth data, if it doesn't exist we return a blank array
	 * @param boolean $p_bUseArray Default is true. If true returns array, else object.
	 * @return array
	 */
	function getAuthData($p_bUseArray = true) {
		$aAuthData = $this->get($this->_authKeyName, false);
		$aAuthData = ($aAuthData !== false && !empty($aAuthData)) ? $aAuthData : array();
		return $p_bUseArray === true ? $aAuthData : (object) $aAuthData;
	}

	/**
	 * Check if a key exists
	 * @param string $p_sKey The key
	 * @return boolean
	 */
	function exists($p_sKey) {
		return array_key_exists($p_sKey, $_SESSION[$this->_sessionName]);
	}
	
	/**
	 * Remove all set keys from the session
	 * @return void
	 */
	function removeAll() {
		foreach( (array) $_SESSION[$this->_sessionName] as $key => $val) {
			unset($_SESSION[$this->_sessionName][$key]);
		}
	}	

	/**
	 * <code>
	 * $session->remove('userInfo');
	 * $session->remove('userInfo', 'email');
	 * </code>
	 * Remove a specific key, or just data within that key.
	 * @param string $p_sKey The initial key set
	 * @param string $p_sName A key within the initial key set. 
	 * @return void
	 */
	function remove($p_sKey, $p_sName = null) {
		if($this->exists($p_sKey)) {
			if($p_sName === null) {
				unset($_SESSION[$this->_sessionName][$p_sKey]);
			} else {
				unset($_SESSION[$this->_sessionName][$p_sKey][$p_sName]);
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
	function get($p_sKey, $p_mDefault = null) {
		return ($this->exists($p_sKey)) ? $_SESSION[$this->_sessionName][$p_sKey] : $p_mDefault;
	}


	/**
	 * Set data into the session by key
	 *
	 * @param string $p_sNamespace
	 * @param mixed $p_mData
	 * @return void
	 */
	function set($p_sKey, $p_mData = true) {
		$_SESSION[$this->_sessionName][$p_sKey] = $p_mData;
	}

}
