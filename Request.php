<?php
class PPI_Request {

	protected $_remoteVars = array(
		'ip'                => '',
		'userAgent'         => '',
		'browser'           => '',
		'browserVersion'    => '',
		'browserAndVersion' => ''
	);

	protected $_isVars = array(
		'ajax'   => null,
		'mobile' => null
	);

	protected $_requestMethod = null;
	protected $_protocol = null;
	protected $_url = null;
	protected $_uri = null;
	protected $_uriParams = array();

	function __construct() {
		$this->_uri = PPI_Helper::getRegistry()->get('PPI::Request_URI');
		$this->_uriParams = explode('/', $this->_uri);
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
		$count = count($this->_uriParams);
		for($i = 0 , $j = 1; $i < $count; $i+=1, $j++) {
			if(!empty($this->_uriParams[$i]) && isset($this->_uriParams[$j])) {
				if(is_integer($this->_uriParams[$j]) || $this->_uriParams[$j] == '0') {
					$tmp[$this->_uriParams[$i]] = (int) $this->_uriParams[$j];
				} else {
					$tmp[$this->_uriParams[$i]] = $this->_uriParams[$j];
				}
			} else {
				$tmp[$this->_uriParams[$i]] = '';
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
	 * Wipe the $_POST superglobal
	 *
	 * @return void
	 */
	function emptyPost() {
		$_POST = array();
	}

	/**
	 * Series of request related boolean checks
	 *
	 * @param string $var
	 * @return bool
	 */
	function is($var) {

		switch($var) {

			case 'ajax':
				if($this->_isVars['ajax'] === null) {
					$this->_isVars['ajax'] = isset($_SERVER['HTTP_X_REQUESTED_WITH'])
					                         && strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] === 'xmlhttprequest');
				}
				return $this->_isVars['ajax'];
				break;

			case 'post':
				return strtolower($this->getRequestMethod()) === 'post';
				break;

			case 'get':
				return strtolower($this->getRequestMethod()) === 'get';
				break;

			case 'put':
				return strtolower($this->getRequestMethod()) === 'put';
				break;

			case 'delete':
				return strtolower($this->getRequestMethod()) === 'delete';
				break;

			case 'head':
				return strtolower($this->getRequestMethod()) === 'head';
				break;

			case 'mobile':
				if($this->_isVars['mobile'] === null) {
					$this->_isVars['mobile'] = $this->isRequestMobile();
				}
				return $this->_isVars['mobile'];
				break;

			case 'https':
			case 'ssl':
				return $this->getProtocol() === 'https';
				break;

		}

	}

	/**
	 * Get a value from the remote requesting user/browser
	 *
	 * @param string $var
	 * @return string
	 */
	function getRemote($var) {

		switch($var) {

			case 'ip':
				return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
				break;

			case 'referer':
				return isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
				break;

			case 'userAgent':
				return isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
				break;

			case 'browser':
				$ret = '';
				break;

			case 'browserVersion':
				$ret = '';
				break;

			case 'browserAndVersion':
				$ret = '';
				break;

		}

	}

	/**
	 * Get the current request uri
	 *
	 * @todo substr the baseurl
	 * @return string
	 */
	function getUri() {
		if($this->_uri === null) {
			$this->_uri = rtrim($_SERVER['REQUEST_URI'], '/') . '/';
		}
		return $this->_uri;

	}

	function getProtocol() {
		if($this->_protocol === null) {
			$this->_protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https' : 'http';
		}
		return $this->_protocol;
	}

	/**
	 * Get the current url
	 *
	 * @return string
	 */
	function getUrl() {
		if($this->_url === null) {
			$this->_url = $this->getProtocol() . '://' . str_replace('www.', '', $_SERVER['HTTP_HOST']) . $_SERVER['REQUEST_URI'];
		}
		return $this->_url;
	}

	/**
	 * Is the current request a mobile request
	 *
	 * @todo see if there is an array based func to do the foreach and strpos
	 * @return boolean
	 */
	protected function isRequestMobile() {
		$mobileUserAgents = array(
			'iPhone', 'MIDP', 'AvantGo', 'BlackBerry', 'J2ME', 'Opera Mini', 'DoCoMo', 'NetFront',
			'Nokia', 'PalmOS', 'PalmSource', 'portalmmm', 'Plucker', 'ReqwirelessWeb', 'iPod',
			'SonyEricsson', 'Symbian', 'UP\.Browser', 'Windows CE', 'Xiino', 'Android'
		);
		$currentUserAgent = $this->getRemote('userAgent');
		foreach($mobileUserAgents as $userAgent) {
			if(strpos($currentUserAgent, $userAgent) !== false) {
				return true;
			}
		}
		return false;
	}

	protected function getRequestMethod() {
		if($this->_requestMethod === null) {
			$this->_requestMethod = $_SERVER['REQUEST_METHOD'];
		}
		return $this->_requestMethod;
	}

}