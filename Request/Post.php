<?php

class PPI_Request_Post extends PPI_Request_Abstract
{
	/**
	 * Constructor
	 *
	 * Stores the given $_POST data or tries to fetch
	 * $_POST if the given array is empty or not given
	 *
	 * @param array $post
	 */
	public function __construct(array $post = null)
	{
		if ($post === null)) {
			$this->_array = $_POST;
		} else {
			$this->_array       = $post;
			$this->_isCollected = false;
		}
	}
}