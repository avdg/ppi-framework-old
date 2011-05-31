<?php
/**
 *
 * @author    Paul Dragoonis <dragoonis@php.net>
 * @license   http://opensource.org/licenses/mit-license.php MIT
 * @package   Core
 * @link      www.ppiframework.com
 *
 */
class PPI_Request {

	/**
	 * Remote vars cache for the getRemove() function
	 *
	 * @var array
	 */
	protected $_remoteVars = array(
		'ip'                => '',
		'userAgent'         => '',
		'browser'           => '',
		'browserVersion'    => '',
		'browserAndVersion' => ''
	);

	/**
	 * Vars cache for the is() function
	 *
	 * @var array
	 */
	protected $_isVars = array(
		'ajax'   => null,
		'mobile' => null
	);

	/**
	 * Mapping fields for get_browser()
	 *
	 * @var array
	 */
	protected $_userAgentMap = array(
		'browser'           => 'browser',
		'browserVersion'    => 'version',
		'browserAndVersion' => 'parent'
	);

	/**
	 * The browser data from
	 *
	 * @var array|null
	 */
	protected $_userAgentInfo = null;

	/**
	 * The request method
	 *
	 * @var null|string
	 */
	protected $_requestMethod = null;

	/**
	 * The protocol being used
	 *
	 * @var null|string
	 */
	protected $_protocol = null;

	/**
	 * The full url including the protocol
	 *
	 * @var null|string
	 */
	protected $_url = null;

	/**
	 * The URI after the base url
	 *
	 * @var null|string
	 */
	protected $_uri = null;

	/**
	 * The quick keyval lookup array for URI parameters
	 *
	 * @var array
	 */
	protected $_uriParams = array();

	function __construct() {
		$this->_uri = PPI_Helper::getRegistry()->get('PPI::Request_URI');
	}

	/**
	 * Obtain a url segments value pair by specifying the key.
	 * eg: /key/val/key2/val2 - by specifying key, you get val, by specifying key2, you get val2.
	 *
	 * @param string $var
	 * @param mixed $default
	 * @return mixed
	 */
	function get($var, $default = null) {
		if(empty($this->_uriParams)) {
			$this->processUriParams();
		}
		if(isset($_GET[$var])) {
			return urldecode(is_numeric($var) ? (int) $var : $var);
		}
		return isset($this->_uriParams[$var]) ? $this->_uriParams[$var] : $default;
	}

	/**
	 * Process the URI Parameters into a clean hashmap for isset() calling later.
	 *
	 * @return void
	 */
	function processUriParams() {
		$params    = array();
		$uriParams = explode('/', trim($this->_uri, '/'));
		$count     = count($uriParams);
		if($count > 0) {
			for($i = 0; $i < $count; $i++) {
				$val = isset($uriParams[($i + 1)]) ? $uriParams[($i + 1)] : null;
				$params[$uriParams[$i]] = urldecode(is_numeric($val) ? (int) $val : $val);
			}
			$this->_uriParams = $params;
		}
	}

	/**
	 * Retrieve information passed via the $_POST array.
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
	 * Retrieve all $_POST elements with have a specific prefix
	 *
	 * @param string $sPrefix The prefix to get values with
	 * @return array|boolean
	 */

	function stripPost($p_sPrefix = '') {
		$aValues = array();
		if($p_sPrefix !== '' && $this->is('post')) {
			$aPost = $this->post();
			$aPrefixKeys = preg_grep("/{$p_sPrefix}/", array_keys($aPost));
			foreach($aPrefixKeys as $prefixKey) {
				$aValues[$prefixKey] = $aPost[$prefixKey];
			}
		}
		return $aValues;
	}

	/**
	 * Check whether a value has been submitted via post
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
	 * @return void
	 */
	function addPost($p_sKey, $p_mValue) {
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

		$var = strtolower($var);
		switch($var) {

			case 'post':
			case 'get':
			case 'put':
			case 'delete':
			case 'head':
				return strtolower($this->getRequestMethod()) === $var;

			case 'https':
			case 'ssl':
				return $this->getProtocol() === 'https';

			case 'mobile':
				if($this->_isVars['mobile'] === null) {
					$this->_isVars['mobile'] = $this->isRequestMobile();
				}
				return $this->_isVars['mobile'];

			case 'ajax':
				if($this->_isVars['ajax'] === null) {
					$this->_isVars['ajax'] = isset($_SERVER['HTTP_X_REQUESTED_WITH'])
					                         && strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] === 'xmlhttprequest');
				}
				return $this->_isVars['ajax'];
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

			case 'referer':
				return isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';

			case 'userAgent':
				return isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';

			case 'browser':
			case 'browserVersion':
			case 'browserAndVersion':
				if($this->_userAgentInfo === null) {
					$userAgentParts = explode(' ', $this->getRemote('userAgent'));
					foreach($this->_userAgentMap as $mapKey => $userAgentKey) {
						$this->_userAgentInfo[$mapKey] = $userAgentInfo[$userAgentKey];
					}
					ppi_dump($this->_userAgentInfo); exit;
				}
					die('here');
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
