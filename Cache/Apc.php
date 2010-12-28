<?php

/**
 *
 * @version   1.0
 * @author    Paul Dragoonis <dragoonis@php.net>
 * @license   http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright Digiflex Development
 * @package   PPI
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
	 * @param integer $p_iTTL The Time To Live
	 */
	function set($p_sKey, $p_mData, $p_iTTL = 0) { return apc_store($p_sKey, $p_mData, $p_iTTL); }
	
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
	
}