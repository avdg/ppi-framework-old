<?php
class PPI_Cache_Apc implements PPI_Interface_Cache {
	
	function get($p_sKey) { return apc_fetch($p_sKey); }
	
	function set($p_sKey, $p_mData, $p_iTTL = 0) { return apc_store($p_sKey, $p_mData, $p_iTTL); }
	
	function exists($p_mKey) { return apc_exists($p_sKey); }
	
	function remove($p_sKey) { return apc_delete($p_sKey); }
	
}