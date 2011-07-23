<?php

/**
 * Handles server variables
 *
 * @see http://www.faqs.org/rfcs/rfc3875.html
 */
class PPI_Request_Server extends PPI_Request_Abstract {
	/**
	 * Constructor
	 *
	 * Takes in an optional $server variable otherwise defaulting to $_SERVER
	 *
	 * @param array $server
	 */
	public function __construct(array $server = array()) {
		if(!empty($server)) {
			$this->_isCollected = false;
			$this->_array = $server;
		} else {
			$this->_array = $_SERVER;
		}
	}

	/**
	 * Get the IP
	 *
	 * @return string
	 */
	public function getIp() {
		if (isset($this->_array['REMOTE_ADDR'])) {
			return $this->_array['REMOTE_ADDR'];
		}

		return '';
	}

	/**
	 * Get the referer
	 *
	 * @return string
	 */
	public function getReferer() {
		if (isset($this->_array['HTTP_REFERER'])) {
			return $this->_array['HTTP_REFERER'];
		}

		return '';
	}

	/**
	 * Get the request method
	 *
	 * @return string
	 */
	public function getRequestMethod() {
		if (isset($this->_array['REQUEST_METHOD'])) {
			return $this->_array['REQUEST_METHOD'];
		}

		return '';
	}

	/**
	 * Get the user agent
	 *
	 * @return string
	 */
	public function getUserAgent() {
		if (isset($this->_array['HTTP_USER_AGENT'])) {
			return $this->_array['HTTP_USER_AGENT'];
		}

		return '';
	}

	/**
	 * Is this is mobile request?
	 *
	 * @return bool
	 */
	public function isRequestMobile() {
		$mobileUserAgents = array(
			'iPhone', 'MIDP', 'AvantGo', 'BlackBerry', 'J2ME', 'Opera Mini', 'DoCoMo', 'NetFront',
			'Nokia', 'PalmOS', 'PalmSource', 'portalmmm', 'Plucker', 'ReqwirelessWeb', 'iPod', 'iPad',
			'SonyEricsson', 'Symbian', 'UP\.Browser', 'Windows CE', 'Xiino', 'Android'
		);
		$currentUserAgent = $this->getUserAgent();

		foreach ($mobileUserAgents as $userAgent) {
			if (strpos($currentUserAgent, $userAgent) !== false) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Is the connection secured?
	 *
	 * @return bool
	 */
	public function isSecure() {
		if (isset($this->_array['HTTPS'])) {
			return $this->_array['HTTPS'] == 'on';
		}

		return false;
	}
}