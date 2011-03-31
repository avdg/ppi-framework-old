<?php

/**
 *
 * @version   1.0
 * @author    Paul Dragoonis <dragoonis@php.net>
 * @license   http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright Digiflex Development
 * @package   Cache
 * @link      www.ppiframework.com
 */

interface PPI_Cache_Interface {

    /**
     * Get cache contents
     *
     * @abstract
     * @param  string $p_sKey The Key
     * @return mixed
     */
	function get($p_sKey);

    /**
     * Set cache contents
     *
     * @abstract
     * @param string $p_sKey The Key
     * @param  mixed $p_mData The Data
     * @param int $p_iTTL The TTL (Time to live)
     * @return boolean
     */
	function set($p_sKey, $p_mData, $p_iTTL = 0);

    /**
     * Check if cache contents exists
     *
     * @abstract
     * @param mixed $p_mKey The Key(s)
     * @return boolean
     */
	function exists($p_mKey);

    /**
     * Remove cache content
     *
     * @abstract
     * @param  $p_sKey
     * @return boolean
     */
	function remove($p_sKey);

    /**
     * Increment the cache value
     *
     * @abstract
     * @param string $p_sKey The Key
     * @param numeric $p_mIncrement The Incremental Value
     * @return int
     */
	function increment($p_sKey, $p_mIncrement);

    /**
     * Decrement the cache value
     *
     * @abstract
     * @param string $p_sKey The Key
     * @param mixed $p_mDecrement The Decremental Value
     * @return int
     */
	function decrement($p_sKey, $p_mDecrement);

    /**
     * Check if a cache driver is enabled
     *
     * @abstract
     * @return boolean
     */
	function enabled();

}