<?php

class PPI_Request_Server extends PPI_Request_Abstract
{
	/**
	 * Constructor
	 *
	 * Stores the given $_SERVER data or tries to fetch
	 * $_SERVER if the given array is empty or not given
	 *
	 * @param array $get
	 */
	public function __construct(array $server = null)
	{
		if ($server === null) {
			$this->_array = $_SERVER;
		} else {
			$this->_array       = $server;
			$this->_isCollected = false;
		}
	}
}