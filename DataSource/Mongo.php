<?php

class PPI_DataSource_Mongo {
    protected $conn;

	function __construct() {

	}

	function getDriver(array $config) {
        if (!class_exists('Mongo')) {
			throw new PPI_Exception('Mongo extension is missing');
        }
        if (empty($config['host'])) {
            $config['host'] = 'localhost';
        }
        if (empty($config['options'])) {
            $config['options'] = array();
        }

        if (empty($this->conn)) {

           $uri =  'mongodb://'
                .((isset($config['user'])) ? $config['user'] . ((isset($config['pass'])) ? ':' . $config['pass'] : '') .'@' : '')
                .((isset($config['host'])) ? $config['host'] : 'localhost')
                .((isset($config['port'])) ? ':' . $config['port'] : '');

            $this->conn = new Mongo($uri, $config['options']);
        }
        

        return $this->conn->selectDB($config['dbname']);
    }

}
