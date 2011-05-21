<?php
/**
 *
 * @version   1.0
 * @author	Paul Dragoonis <dragoonis@php.net>
 * @license   http://opensource.org/licenses/mit-license.php MIT
 * @copyright Digiflex Development
 * @package   Cache
 */

class PPI_Cache_Memcached implements PPI_Cache_Interface {

	/**
	 * The memcache(d) driver
	 *
	 * @var Memcache|Memcached
	 */
	protected $_handler;

	/**
	 * If a server has been added. Default is false
	 *
	 * @var bool
	 */
	protected $_serverAdded = false;

	/**
	 * The PPI_Cache_Memcached constructor
	 *
	 * @todo use $this->enabled()
	 * @todo Investigate if we can use memcache(d) or just one (with API usage complying to the interface)
	 * @throws PPI_Exception
	 *
	 */
	function __construct() {
		if(extension_loaded('Memcached')) {
			$this->_handler = new Memcached();
		} elseif(extension_loaded('Memcache')) {
			$this->_handler = new Memcache();
		} else {
			throw new PPI_Exception('Unable to use Memcache. Extension not loaded.');
		}
	}

	function init() {}

	/**
	 * Get a value from cache
	 *
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
	 *
	 * @param string $p_sKey The Key
	 * @param mixed $p_mData The Data
	 * @param integer $p_mTTL The Time To Live
	 * @return boolean
	 */
	function set($p_sKey, $p_mData, $p_mTTL = 0) {
		if($this->_serverAdded === false) {
			$this->addServer('localhost');
		}
		return $this->_handler->set($p_sKey, $p_mData, (is_numeric($p_mTTL) ? $p_mTTL : strtotime($p_mTTL)));
	}

	/**
	 * Increment a cache value
	 *
	 * @param string $p_sKey The Key
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
	function decrement($p_sKey, $p_mDecrement) {
		return $this->_handler->decrement($p_sKey, $p_mDecrement);
	}

	/**
	 * Clear the cache
	 *
	 * @return boolean
	 */
	function clear() { $this->_handler->flush(); }

	/**
	 * Check if a key exists in the cache
	 *
	 * @param string $p_mKey The Key
	 * @return boolean
	 */
	function exists($p_sKey) {}

	/**
	 * Remove a key from the cache
	 *
	 * @param string $p_sKey The Key
	 * @return boolean
	 */
	function remove($p_sKey) {
		return $this->_handler->delete($p_sKey);
	}

	/**
	 * Add a memcached server to connect to
	 *
	 * @param string $host The Hostname
	 * @param integer $port The Port
	 * @param integer $weight The Weight
	 */
	function addServer($host, $port = 11211, $weight = 10) {

		$this->_serverAdded = true;
		$this->_handler->addServer($host, $port, $weight);
	}

	/**
	 * Check if the memcached extension is loaded.
	 *
	 * @return boolean
	 */
	function enabled() { return extension_loaded('memcached'); }

}
