<?php

/**
 *
 * @version   1.0
 * @author    Paul Dragoonis <dragoonis@php.net>
 * @license   http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright Digiflex Development
 * @package   Cache
 */

class PPI_Cache_Memcached implements PPI_Cache_Interface {

	private $_handler;
	private $_serverAdded = false;

	function __construct() {
		if(extension_loaded('Memcached')) {
			$this->_handler = new Memcached();
		} elseif(extension_loaded('Memcache')) {
			$this->_handler = new Memcache();
		} else {
			throw new PPI_Exception('Unable to use Memcache. Extension not loaded.');
		}
	}

	/**
	 * Get a value from cache
	 * @param string $p_sKey The Key
	 * @return mixed
	 */
	function get($p_sKey) {
		if($this->_serverAdded === false) {
			$this->addServer('localhost');
		}
		return $this->_handler->get($p_sKey);
	}

	/**
	 * Set a value in the cache
	 * @param string $p_sKey The Key
	 * @param mixed $p_mData The Data
	 * @param integer $p_iTTL The Time To Live
	 */
	function set($p_sKey, $p_mData, $p_iTTL = 0) {
		if($this->_serverAdded === false) {
			$this->addServer('localhost');
		}
		return $this->_handler->set($p_sKey, $p_mData,0, (is_numeric($p_mTTL) ? $p_mTTL : strtotime($p_mTTL)));
	}

	/**
	 * Incremenet a cache value
	 *
	 * @param stirng $p_sKey The Key
	 * @param numeric $p_mIncrement The incremental value
	 * @return numeric
	 */
	function increment($p_sKey, $p_mIncrement) {
		return $this->_handler->increment($p_sKey, $p_mIncrement);
	}

	/**
	 * Decrement a cache value
	 *
	 * @param string $p_sKey The Key
	 * @param numeric $p_mDecrement The Decremental Value
	 * @return numeric
	 */
	function decremenet($p_sKey, $p_mDecrement) {
		return $this->_handler->decrement($p_sKey, $p_mDecrement);
	}

	/**
	 * Clear the cache
	 * @return boolean
	 */
	function clear() { $this->_handler->flush(); }

	/**
	 * Check if a key exists in the cache
	 * @param string $p_mKey The Key
	 * @return boolean
	 */
	function exists($p_sKey) {}

	/**
	 * Remove a key from the cache
	 * @param string $p_sKey The Key
	 * @return boolean
	 */
	function remove($p_sKey) {
		return $this->_handler->delete($key);
	}

	/**
	 * Add a memcached server to connect to
	 * @param string $host The Hostname
	 * @param integer $port The Port
	 * @param integer $weight The Weight
	 */
	function addServer($host, $port = 11211, $weight = 10) {
		$this->_serverAdded = true;
		$this->_handler->addServer($host, $port, true, $weight);
	}

	/**
	 * Check if the memcached extension is loaded.
	 *
	 * @return boolean
	 */
	function enabled() { return extension_loaded('memcached'); }

}
