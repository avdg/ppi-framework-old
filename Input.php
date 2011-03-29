<?php
/**
 *
 * @version   1.0
 * @author    Paul Dragoonis <dragoonis@php.net>
 * @license   http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright Digiflex Development
 * @package   Core
 * @link      www.ppiframework.com/docs/input.html
 */
class PPI_Input {

	protected $_aArguments;

	function __construct() {
		$sUrl = $_SERVER['REQUEST_URI'];
		$this->_aArguments = explode ('/', $sUrl);
	}

	/**
	 * Obtain a url segments value pair by specifying the key.
	 * eg: /key/val/key2/val2 - by specifying key, you get val, by specifying key2, you get val2.
	 * @param string $p_sIndex The specified key
	 * @param string $p_sDefaultValue The default value to return in the situation that the key or subsequent value was not found.
	 * @return mixed (Can be user defined)
	 */
	function get($p_sIndex, $p_sDefaultValue = "") {
		$tmp = array();
		$count = count($this->_aArguments);
		for($i = 0 , $j = 1; $i < $count; $i+=1, $j++) {
			if(!empty($this->_aArguments[$i]) && isset($this->_aArguments[$j])) {
				if(is_integer($this->_aArguments[$j]) || $this->_aArguments[$j] == '0') {
					$tmp[$this->_aArguments[$i]] = (int) $this->_aArguments[$j];
				} else {
					$tmp[$this->_aArguments[$i]] = $this->_aArguments[$j];
				}
			} else {
				$tmp[$this->_aArguments[$i]] = '';
			}
		}
		if(!empty($tmp)) {
			foreach($tmp as $item => $val) {
				if($item == $p_sIndex) {
					if(is_integer ($val) OR $val == '0') {
						return (int) $val;
					} elseif(!empty($val)) {
						return urldecode ($val);
					}
				}
			}
		}

		if(empty($p_sDefaultValue)) $p_sDefaultValue = "";
		if(isset($_GET[$p_sIndex])) {
			if(is_integer ($_GET[$p_sIndex]) || $_GET[$p_sIndex] == '0') {
				return (int) $_GET[$p_sIndex];
			}
			return (!empty($_GET[$p_sIndex])) ? urldecode($_GET[$p_sIndex]) : urldecode($p_sDefaultValue);
		}
		return urldecode($p_sDefaultValue);
	}

	/**
	 * Retreive information passed via the $_POST array.
	 * Can specify a key and return that, else return the whole $_POST array
	 *
	 * @param string [$p_sIndex] Specific $_POST key
	 * @param mixed [$p_sDefaultValue] null if not specified, mixed otherwise
	 * @return string|array Depending if you passed in a value for $p_sIndex
	 */
	function post($p_sIndex = null, $p_sDefaultValue = null, $p_aOptions = null) {
		if($p_sIndex === null) {
			return PPI_Helper::getInstance()->arrayTrim($_POST);
		} else {
			return PPI_Helper::getInstance()->arrayTrim((isset($_POST[$p_sIndex])) ? $_POST[$p_sIndex] : $p_sDefaultValue);
		}
	}

	/**
	 * Retreive all $_POST elements with have a specific prefix
	 *
	 * @param string $sPrefix The prefix to get values with
	 * @return array|boolean
	 */
	function stripPost($p_sPrefix = '') {
		if($p_sPrefix == '') {
			return array();
		}
		if(isset($_POST)) {
			$aValues = array();
			foreach($this->post() as $key => $val) {
				if(strpos($key, $p_sPrefix) !== false) {
					$key = str_replace($p_sPrefix, '', $key);
					$aValues[$key] = $val;
				}
			}
			if(!empty($aValues)) {
				return $aValues;
			}
		}
		return array();
	}

	/**
	 * Check if something is a valid email
	 * @todo make this reference filter_var()
	 * @param string $p_sEmail
	 * @return boolean
	 */
	function is_validemail($p_sEmail = '') {
		return preg_match("/^[_A-Za-z0-9.-]+[^.]@[^.][A-Za-z0-9.-]{2,}[.][a-z]{2,4}$/", $p_sEmail);
	}

	/**
	 * Determine wether or not a form has been submitted.
	 *
	 * @return boolean
	 */
	function isPost() {
		return !empty($_POST);
	}

	/**
	 * Check wether a value has been submitted via post
	 * @param string The $_POST key
	 * @return boolean
	 */
	function hasPost($p_sKey) {
		return array_key_exists($p_sKey, $_POST);
	}

	/**
	 * Remove a value from the $_POST superglobal.
	 *
	 * @param string $p_sKey The key to remove
	 * @return boolean True if the value existed, false if not.
	 */
	function removePost($p_sKey) {
		if(isset($_POST[$p_sKey])) {
			unset($_POST[$p_sKey]);
			return true;
		}
		return false;
	}

	/**
	 * Add a value to the $_POST superglobal
	 *
	 * @param string $p_sKey The key
	 * @param mixed $p_mValue The value to set the key with
	 * @param boolean $p_bOverride Default is false. If you want to override a value that already exists then pass true.
	 * @throws PPI_Exception If the key already existed and you did not permit an override
	 * @return void
	 */
	function addPost($p_sKey, $p_mValue, $p_bOverride = false) {
		if($p_bOverride === false && isset($_POST[$p_sKey])) {
			throw new PPI_Exception("Unable to set POST key: $p_sKey. Key already exists and override was not permitted");
		}
		$_POST[$p_sKey] = $p_mValue;
	}

    /**
     * Set the flash message(s) in the session
     * @param string $p_sMessage
     * @param bool $p_bSuccess
     * @return void
     */
    static function setFlashMessage($p_sMessage, $p_bSuccess = true) {
        PPI_Helper::getSession()->set('ppi_flash_message', array(
            'mode'    => $p_bSuccess ? 'success' : 'failure',
            'message' => $p_sMessage
        ));
    }

    /**
     * Get the flash message(s)
     *
     * @return array|null If not set, it's null.
     */
    static function getFlashMessage() {
        return PPI_Helper::getSession()->get('ppi_flash_message');
    }

    /**
     * PPI_Input::clearFlashMessage()
     * Wipe the flash message from the session
     * @return void
     */
    static function clearFlashMessage() {
        PPI_Helper::getSession()->remove('ppi_flash_message');
    }

    /**
     * Wipe the $_POST superglobal
     * @return void
     */
	function emptyPost() {
		$_POST = array();
	}

}
