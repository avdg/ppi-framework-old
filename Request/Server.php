<?php

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
}