<?php
class PPI_Model_Example {

	function __construct(array $options = array()) {

		if(isset($options['connection'])) {
			$this->_connection = $options['connection'];
		} else {
			// Our PDO Connection
			$datasource = new PPI_DataSource();
			$this->_connection = $datasource->get('main');
		}
	}

	function fetchAll() {
		return $this->_connection->query("SELECT * FROM topcats")->fetchAll();
	}

}