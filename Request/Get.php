<?php

class PPI_Request_Get extends PPI_Request_Abstract {

	/**
	 * The current URI. This is used if we are automatically collecting params, we will need a URI
	 *
	 * @var null|string
	 */
	protected $_uri = null;

	/**
	 * Have we automatically processed the params?
	 *
	 * @var bool
	 */
	protected $_processedUriParams = false;

	/**
	 * Constructor
	 *
	 * If data is supplied then that data is used, otherwise we automatically
	 *
	 * @param array $data Supplied Data
	 */
	public function __construct(array $data = array()) {

		if(!empty($data)) {
			$this->_isCollected = false;
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
	 * Override on offsetGet, this checks if we're supposed to automatically collect data
	 * and if it has not done so already then we do that.
	 *
	 * @param string $offset
	 * @return mixed
	 */
	public function offsetGet($offset) {
		if($this->_isCollected === true && $this->_processedUriParams === false) {
			$this->_array = $this->processUriParams();
		}
		return parent::offsetGet($offset);
	}

	/**
	 * Override on offsetExists, this checks if we're supposed to automatically collect data
	 * and if it has not done so already then we do that.
	 *
	 * @param string $offset
	 * @return bool
	 */
	public function offsetExists($offset) {
		if($this->_isCollected === true && $this->_processedUriParams === false) {
			$this->_array = $this->processUriParams();
		}
		return parent::offsetExists($offset);
	}
}