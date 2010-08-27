<?php
interface PPI_Interface_Cache {
	
	function get($p_sKey) {}
	
	function set($p_sKey, $p_mData, $p_iTTL = 0) {}
	
	function exists($p_mKey) {}
	
	function remove($p_sKey) {}
	
}