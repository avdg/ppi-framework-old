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

	function get($p_sKey);

	function set($p_sKey, $p_mData, $p_iTTL = 0);

	function exists($p_mKey);

	function remove($p_sKey);

	function increment($p_sKey, $p_mIncrement);

	function decrement($p_sKey, $p_mDecrement);

	function enabled();

}