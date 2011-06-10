<?php

class PPI_Request_Post extends PPI_Request_Abstract {

	/**
	 * Obtain the information from $this->_array.
	 * Override from $options['data']
	 *
	 * @param array $options
	 */
	public function __construct(array $options = array()) {
		$this->_array = isset($options['data']) ? $options['data'] : $_POST;
	}
}