<?php

/**
 * @author	  Paul Dragoonis <dragoonis@php.net>
 * @license   http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright Digiflex Development
 * @package   Cache
 * @link      http://www.ppiframework.com/docs/cache.html
 * @link      https://github.com/nicolasff/phpredis
 */

class PPI_Cache_Redis implements PPI_Cache_Interface {

	protected $_defaults = array(
		'server' => '127.0.0.1:6379',
		'expiry' => 0 // Never
	);

	/**
	 * @param array $p_aOptions The options that override the default
	 */
	function __construct(array $p_aOptions = array()) {
		$this->_defaults = ($p_aOptions + $this->_defaults);
		list($ip, $port) = explode(':', $this->_defaults['server']);
		$this->_handler = new Redis();
		$this->_handler->connect($ip, $port);
	}

	/**
	 * Get a value from cache
	 * @param mixed $p_mKey The Key(s)
	 * @return mixed
	 */
	function get($p_mKey) {
		return is_array($p_mKey) ? $this->_handler->getMultiple($p_mKey) : $this->_handler->get($p_mKey);
	}

	/**
	 * Set a value in the cache
	 * @param string $p_sKey The Key
	 * @param mixed $p_mData The Data
	 * @param mixed $p_mTTL The Time To Live. Integer or String (strtotime)
	 * @return boolean True on succes, False on failure.
	 */
	function set($p_sKey, $p_mData, $p_mTTL = null) {
		if($p_mTTL !== null && is_string($p_mData)) {
			$p_mTTL = strtotime($p_mTTL);
		}
		$p_mTTL = $p_mTTL !== null ? $p_mTTL : $this->_defaults['expiry'];
		return $this->_handler->set($p_sKey, $p_mData, $p_mTTL);
	}

	/**
	 * Check if a key exists in the cache
	 * @param string $p_sKey The Key
	 * @return boolean
	 */
	function exists($p_sKey) { return $this->_handler->exists($p_sKey); }

	/**
	 * Remove a key from the cacheincre
	 * @param string $p_sKey The Key
	 * @return boolean
	 */
	function remove($p_sKey) { return $this->_handler->delete($p_sKey); }

	/**
	 * Wipe the cache contents
	 *
	 * @return boolean
	 */
	function clear() { return $this->_handler->flushdb(); }

	/**
	 * Increment a numerical value
	 *
	 * @param string $p_sKey The Key
	 * @param numeric $p_iInc The incremental value
	 * @return numeric
	 */
	function increment($p_sKey, $p_iInc = 1) { return $this->_handler->incr($p_sKey, $p_iInc); }

	/**
	 * Enter description here...
	 *
	 * @param string $p_sKey The Key
	 * @param numeric $p_iDec The decremental value
	 * @return numeric
	 */
	function decrement($p_sKey, $p_iDec = 1) { return $this->_handler->decr($p_sKey, $p_iDec); }

	/**
	 * Check if the Redis extension has been loaded and is enabled in its configuration.
	 *
	 * @return boolean
	 */
	function enabled() { return extension_loaded('redis'); }

}