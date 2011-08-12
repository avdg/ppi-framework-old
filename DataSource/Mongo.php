<?php

class PPI_DataSource_Mongo {
    protected $conn = array();

	function __construct() {

	}

	function getDriver(array $config) {
        if (!class_exists('Mongo')) {
            throw new PPI_Exception('Mongo extension is missing');
        }

        $uri =  'mongodb://'
            .((isset($config['user'])) ?
                $config['user'] . ((isset($config['pass'])) ? ':' . $config['pass'] : '') .'@'
                : '')
            .((isset($config['host'])) ? $config['host'] : 'localhost')
            .((isset($config['port'])) ? ':' . $config['port'] : '');

        if (empty($this->conn[$uri])) {

            if (empty($config['options'])) {
                $config['options'] = array();
            }

            $this->conn[$uri] = new Mongo($uri, $config['options']);
        }
        
        if (!empty($config['database'])) {
            return $this->conn[$uri]->selectDB($config['database']);
        } 
        return $this->conn[$uri];
    }

}
