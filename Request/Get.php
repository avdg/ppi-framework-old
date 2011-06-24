<?php

class PPI_Request_Get extends PPI_Request_Abstract {

	protected $_uri = null;

	protected $_processedUriParams = false;

	protected $_dataOverride = false;

	/**
	 * Constructor
	 *
	 * Stores the given $_GET data or tries to fetch
	 * $_GET if the given array is empty or not given
	 *
	 * @param array $options Some Options
	 */
	public function __construct(array $data = array()) {
		if(!empty($data)) {
			$this->_dataOverride = true;
			$this->_array = $data;
		}
	}

	/**
	 * Pass in the current URI for this class to interact against.
	 *
	 * @param string $uri
	 * @return void
	 */
	public function setUri($uri) {
		$this->_uri = $uri;
	}

	/**
	 * Process the URI Parameters into a clean hashmap for isset() calling later.
	 *
	 * @return array
	 */
	protected function processUriParams() {
		$params    = array();
		$uriParams = explode('/', trim($this->_uri, '/'));
		$count     = count($uriParams);
		if($count > 0) {
			for($i = 0; $i < $count; $i++) {
				$val = isset($uriParams[($i + 1)]) ? $uriParams[($i + 1)] : null;
				$params[$uriParams[$i]] = urldecode(is_numeric($val) ? (int) $val : $val);
			}
		}
		return $params;
	}

	/**
	 * Get the param
	 *
	 * @param  $offset
	 * @return mixed
	 */
	public function offsetGet($offset) {
		if(!$this->_dataOverride && !$this->_processedUriParams) {
			$this->_array              = $this->processUriParams();
			$this->_processedUriParams = true;
		}
		return parent::offsetGet($offset);
	}

	/**
	 * Override on offsetExists, this gets data from $_GET first otherwise defaulting to URI params.
	 * It also runs $this->processUriParams()
	 *
	 * @param  $offset
	 * @return bool
	 */
	public function offsetExists($offset) {
		if($this->_processedUriParams === false) {
			$this->_array              = $this->processUriParams();
			$this->_processedUriParams = true;
		}
		return isset($_GET[$offset]) ? $_GET[$offset] : parent::offsetExists($offset);
	}
}