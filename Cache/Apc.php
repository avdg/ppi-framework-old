<?php

/**
 *
 * @version   1.0
 * @author    Paul Dragoonis <dragoonis@php.net>
 * @license   http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright Digiflex Development
 * @package   Cache
 */

class PPI_Cache_Apc implements PPI_Cache_Interface {

	/**
	 * Get a value from cache
	 * @param string $p_sKey The Key
	 * @return mixed
	 */
	function get($p_sKey) { return apc_fetch($p_sKey); }

	/**
	 * Set a value in the cache
	 * @param string $p_sKey The Key
	 * @param mixed $p_mData The Data
	 * @param mixed $p_iTTL The Time To Live. Integer or String (strtotime)
	 * @return boolean True on succes, False on failure.
	 */
	function set($p_sKey, $p_mData, $p_mTTL = 0) {
		return apc_store($p_sKey, $p_mData, (is_numeric($p_mTTL) ? $p_mTTL : strtotime($p_mTTL)));
	}

	/**
	 * Check if a key exists in the cache
	 * @param string $p_mKey The Key
	 * @return boolean
	 */
	function exists($p_mKey) { return apc_exists($p_sKey); }

	/**
	 * Remove a key from the cache
	 * @param string $p_sKey The Key
	 * @return boolean
	 */
	function remove($p_sKey) { return apc_delete($p_sKey); }

	/**
	 * Wipe the cache contents
	 *
	 * @return unknown
	 */
	function clear() { return apc_clear_cache('user'); }

	/**
	 * Increment a numerical value
	 *
	 * @param string $p_sKey The Key
	 * @param numeric $p_iInc The incremental value
	 * @return numeric
	 */
	function increment($p_sKey, $p_iInc = 1) { return apc_inc($p_sKey, $p_iInc); }

	/**
	 * Enter description here...
	 *
	 * @param string $p_sKey The Key
	 * @param numeric $p_iDec The decremental value
	 * @return numeric
	 */
	function decrement($p_sKey, $p_iDec = 1) { return apc_dec($p_sKey, $p_iDec); }

	/**
	 * Check if the APC extension has been loaded and is enabled in its configuration.
	 *
	 * @return boolean
	 */
	function enabled() {
		return extension_loaded('apc') && (php_sapi_name() === 'cli' && ini_get('apc.enabled'));
	}

}