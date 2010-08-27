<?php
class PPI_Cache_Memcached implements PPI_Interface_Cache {
	
	private $_handler;
	
	function __construct() {
		$this->_handler = new Memcached();
	}
	
	function get($p_sKey) {
		return $this->_handler->get($p_sKey);
	}
	
	function set($p_sKey, $p_mData, $p_iTTL) {
		return $this->_handler->set($p_sKey, $p_mData,0, $p_iTTL);
	}
	
	function exists($p_sKey) {}
	
	function remove($p_sKey) {		
		return $this->_handler->delete($key)
	}
	
	function addServer($host, $port = 11211, $weight = 10) {
		$this->_handler->addServer($host, $port, true, $weight);
	}
	
}