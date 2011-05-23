<?php
/**
 *
 * @version   1.0
 * @author	Paul Dragoonis <dragoonis@php.net>
 * @license   http://opensource.org/licenses/mit-license.php MIT
 * @copyright Digiflex Development
 * @package   Cache
 */

class PPI_Cache_Disk implements PPI_Cache_Interface {

	/**
	 * The folder where the cache contents will be placed
	 *
	 * @var string
	 */
	protected $_cacheDir = null;

	/**
	 * The options passed in upon instantiation
	 *
	 * @var array
	 */
	protected $_options = array();

	function __construct(array $p_aOptions = array()) {
		$this->_options = $p_aOptions;
		$this->_cacheDir = isset($p_aOptions['cache_dir'])
		   ? $p_aOptions['cache_dir'] : APPFOLDER . 'Cache/Disk/';
	}

	function init() {}

	/**
	 * Get the data from the specified path
	 *
	 * @param string $p_sKey
	 * @return mixed
	 */
	protected function getData($p_sPath) {
		$sContent = file_get_contents($p_sPath);
		return $sContent != '' ? unserialize($sContent) : '';
	}

	/**
	 * Remove a key from the cache
	 * @param string $p_sKey The Key
	 * @return boolean
	 */
	function remove($p_sKey) {
		return $this->removeKeyByPath($this->getKeyCachePath($p_sKey))
			&& $this->removeKeyByPath($this->getKeyMetaCachePath($p_sKey));
	}

	/**
	 * Remove a file by path
	 * @param string $p_sPath
	 * @return boolean
	 */
	function removeKeyByPath($p_sPath) {
		return file_exists($p_sPath) && unlink($p_sPath);
	}


	/**
	 * Get the full path to a cache item
	 * @param string $p_sKey
	 * @return string
	 */
	protected function getKeyCachePath($p_sKey) {
		return $this->_cacheDir . 'default--' . $p_sKey;
	}

	/**
	 * Get the full path to a cache item's metadata file
	 *
	 * @param string $p_sKey
	 * @return string
	 */
	protected function getKeyMetaCachePath($p_sKey) {
		return $this->getKeyCachePath($p_sKey) . '.metadata';
	}

	/**
	 * Get the metadata for a chosen cache file
	 *
	 * @param string $p_sKey
	 * @return array
	 */
	protected function getMetaData($p_sKey) {
		return $this->getData($this->getKeyMetaCachePath($p_sKey));
	}

	/**
	 * Set data by writing it to disk
	 *
	 * @param string $p_sPath
	 * @param mixed $p_mData
	 * @return integer
	 */
	protected function setData($p_sPath, $p_mData) {
		return file_put_contents($p_sPath, serialize($p_mData), LOCK_EX) > 0;
	}

	/**
	 * Check if a key exists in the cache
	 * @param string $p_mKey The Key(s)
	 * @return boolean
	 */
	function exists($p_sKey) {
		$sPath = $this->getKeyCachePath($p_sKey);
		if(file_exists($sPath) === false) {
			return false;
		}
		$aMetaData = $this->getMetaData($p_sKey);
		// See if the item has a ttl and if it has expired then we delete it.
		if($aMetaData['ttl'] > 0 && (int) $aMetaData['expire_time'] < time()) {
			// Remove the cache item and its metadata file.
			$this->remove($p_sKey);
			return false;
		}
		return true;
	}

	/**
	 * Set a value in the cache
	 * @param string $p_sKey The Key
	 * @param mixed $p_mData The Data
	 * @param integer $p_iTTL The Time To Live
	 * @return boolean
	 */
	function set($p_sKey, $p_mData, $p_iTTL = 0) {

		$sPath = $this->getKeyCachePath($p_sKey);
		if($this->exists($p_sKey)) {
			$this->remove($p_sKey);
		}

		$sCacheDir = dirname($sPath);
		if(!is_dir($sCacheDir)) {
			try {
			   mkdir($sCacheDir);
			} catch(PPIException $e) {
				throw new PPI_Exception('Unable to create directory:<br>(' . $sCacheDir . ')');
			}
		}
		if(is_writeable($sCacheDir) === false) {
			$aFileInfo = pathinfo(dirname($sPath));
			chmod($sCacheDir, 775);
			if(is_writable($sCacheDir) === false) {
				throw new PPI_Exception('Unable to create cache file: ' . $p_sKey. '. Cache directory not writeable.<br>(' . $this->_cacheDir . ')<br>Current permissions: ');
			}
		}

		$aMetaData = array(
		   'expire_time' => time() + (int) $p_iTTL,
		   'ttl'		 => $p_iTTL
		);

		return $this->setData($sPath, $p_mData)
		   && $this->setData($this->getKeyMetaCachePath($p_sKey), $aMetaData);
	}

	/**
	 * Get a value from cache
	 * @param string $p_sKey The Key
	 * @return mixed
	 */
	function get($p_sKey, $p_mDefault = null) {
		if($this->exists($p_sKey) === false) {
			return $p_mDefault;
		}
		return $this->getData($this->getKeyCachePath($p_sKey));
	}

	/**
	 * Check if this adapter is enabled or not.
	 *
	 * @return boolean
	 */
	function enabled() { return true; }

	/**
	 * Increment the value in the cache
	 *
	 * @param  $p_sKey The key
	 * @param  $p_mIncrement The value to increment by
	 * @return void
	 */
	function increment($p_sKey, $p_mIncrement) { }

	/**
	 * Decrement the value in the cache
	 *
	 * @param  $p_sKey The Key
	 * @param  $p_mDecrement The value to decrement by
	 * @return void
	 */
	function decrement($p_sKey, $p_mDecrement) { }

}