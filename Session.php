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
     * The initialise function to create the instance
     * @return void
     */
    protected static function init() {
        self::setInstance(new PPI_Session());
    }

    /**
     * The function used to initially set the instance
     *
     * @param PPI_Model_Session $instance
     * @throws PPI_Exception
     * @return void
     */
    static function setInstance(PPI_Session $instance) {
        if (self::$_instance !== null) {
            throw new PPI_Exception('PPI_Model_Session is already initialised');
        }
        self::$_instance = $instance;
    }

    /**
     * Obtain the instance if it exists, if not create it
     *
     * @return PPI_Model_Session
     */
    static function getInstance() {
        if (self::$_instance === null) {
            self::init();
        }
        return self::$_instance;
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

	/**
	 * Erase all session information
	 * @todo Investigate why we can't just unset $_SESSION[$this->_sessionName]
	 * @return void
	 */
	function namespaceUnsetAll() {
		$this->unsetAll();
	}



	/**
	 * Check if a namespace exists in $_SESSION
	 * @param string $p_sNamespace
	 * @return boolean
	 */
	function namespaceExists($p_sNamespace) {
		return $this->exists($p_sNamespace);
	}

	function exists($p_sNamespace) {
		return array_key_exists($p_sNamespace, $_SESSION[$this->_sessionName]);
	}

	/**
	 * Unset the namespace from $this->_namespaces and from $_SESSIOn
	 * @param string $p_sNamespace
	 * @return void
	 */
	function namespaceUnset($p_sNamespace, $p_sName = null) {
		$this->remove($p_sNamespace, $p_sName);
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
	 * Get a namespace from the session
	 * @param string $p_sNamespace
	 * @return mixed The Namespace Data
	 */
	function namespaceGet($p_sNamespace, $p_mDefault = null) {
		return $this->get($p_sNamespace, $p_mDefault);
	}

	/**
	 * Get information from the session by key
	 *
	 * @param string $p_sNamespace
	 * @param mixed $p_mDefault Optional. Default is null
	 * @return mixed
	 */
	function get($p_sNamespace, $p_mDefault = null) {
		return ($this->namespaceExists($p_sNamespace)) ? $_SESSION[$this->_sessionName][$p_sNamespace] : $p_mDefault;
	}
	
	function namespaceSet($p_sNamespace, $p_mData = true) {
		$this->set($p_sNamespace, $p_mData);
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