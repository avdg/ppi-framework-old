<?php

/**
 * @author     Paul Dragoonis <dragoonis@php.net>
 * @license    http://opensource.org/licenses/mit-license.php MIT
 * @package    Core
 * @link       http://www.ppiframework.com/docs/session.html
 */
class PPI_Session {

	/**
	 * The config object, optionally passed.
	 *
	 * @var null|object
	 */
	protected $_config = null;
	/**
	 * The session defaults
	 *
	 * @var array
	 */
	protected $_defaults = array(
		'userAuthKey'               => 'userAuthInfo',
		'sessionNamespace'          => '__MYAPP',
		'frameworkSessionNamespace' => '__PPI'
	);

	/**
	 * This detemines if this class auto collects data from $_SESSION or it has been given its own mock data directly.
	 *
	 * @var bool
	 */
	protected $_isCollected = true;


	/**
	 * The actual data we're manipulating this is only used for mock data.
	 *
	 * @var array
	 */
	protected $_data = array();

	/**
	 * Constructor to optionally pass in session default options
	 *
	 */
	public function __construct(array $p_aOptions = array()) {

		$this->_defaults = ($p_aOptions + $this->_defaults);
		$this->_defaults['sessionNamespace'] = $this->_defaults['frameworkSessionNamespace'] . '_' . $this->_defaults['sessionNamespace'];

		if (!isset($this->_array[$this->_defaults['sessionNamespace']])) {
			$this->_array[$this->_defaults['sessionNamespace']] = array();
		}

		if(isset($p_aOptions['data'])) {
			$this->_isCollected = false;
			$this->_array[$this->_defaults['sessionNamespace']] = $p_aOptions['data'];
		} else {
			$this->_array[$this->_defaults['sessionNamespace']] = $_SESSION;
			session_name($this->_defaults['sessionNamespace']);
			session_start();
		}

	}

	/**
	 * Check if a key exists
	 *
	 * @param string $p_sKey The key
	 * @return boolean
	 */
	public function exists($p_sKey) {
		return array_key_exists($p_sKey, $this->_array[$this->_defaults['sessionNamespace']]);
	}

	/**
	 * Remove all set keys from the session
	 *
	 * @return void
	 */
	public function removeAll() {

		foreach ( (array) $this->_array[$this->_defaults['sessionNamespace']] as $key => $val) {
			unset($this->_array[$this->_defaults['sessionNamespace']][$key]);
		}
	}

	/**
	 * Remove a specific key, or just data within that key.
	 * @example
	 * $session->remove('userInfo');
	 * $session->remove('userInfo', 'email');
	 *
	 * @param string $p_sKey The initial key set
	 * @param string $p_sName A key within the initial key set.
	 * @return void
	 */
	public function remove($p_sKey, $p_sName = null) {

		if (null === $p_sName) {
			unset($this->_array[$this->_defaults['sessionNamespace']][$p_sKey]);
		} else {
			unset($this->_array[$this->_defaults['sessionNamespace']][$p_sKey][$p_sName]);
		}
	}

	/**
	 * Get information from the session by key
	 *
	 * @param string $p_sNamespace
	 * @param mixed $p_mDefault Optional. Default is null
	 * @return mixed
	 */
	public function get($p_sKey, $p_mDefault = null) {

		if (isset($this->_array[$this->_defaults['sessionNamespace']][$p_sKey])) {
			return $this->_array[$this->_defaults['sessionNamespace']][$p_sKey];
		}
		return $p_mDefault;
	}

	/**
	 * Set data into the session by key
	 *
	 * @param string $p_sNamespace
	 * @param mixed $p_mData
	 * @return void
	 */
	public function set($p_sKey, $p_mData = true) {
		$this->_array[$this->_defaults['sessionNamespace']][$p_sKey] = $p_mData;
	}

	/**
	 * Set the authentication information for the current user
	 *
	 * @param mixed $aData The data to be set
	 * @return void
	 */
	public function setAuthData($mData) {
		$this->set($this->_defaults['userAuthKey'], $mData);
	}

	/**
	 * Clear the set authentication information
	 *
	 * @return void
	 */
	public function clearAuthData() {
		$this->set($this->_defaults['userAuthKey'], null);
	}

	/**
	 * Get the auth data, if it doesn't exist we return a blank array
	 *
	 * @param boolean $p_bUseArray Default is true. If true returns array, else object.
	 * @return array
	 */
	public function getAuthData($p_bUseArray = true) {

		$aAuthData = $this->get($this->_defaults['userAuthKey'], false);
		$aAuthData = !empty($aAuthData) ? $aAuthData : array();
		return true === $p_bUseArray ? $aAuthData : (object)$aAuthData;
	}

}
