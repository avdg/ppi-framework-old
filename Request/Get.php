<?php

class PPI_Request_Get extends PPI_Request_Abstract
{
	/**
	 * Constructor
	 *
	 * Stores the given $_GET data or tries to fetch
	 * $_GET if the given array is empty or not given
	 *
	 * @param array $get
	 */
	public function __construct(array $get = null)
	{
		if ($get === null)) {
			$this->_array = $_GET;
		} else {
			$this->_array       = $get;
			$this->_isCollected = false;
		}
	}
}