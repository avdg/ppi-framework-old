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

	public function __construct(array $p_aOptions = array()) {
		$this->_options = $p_aOptions;
		$this->_cacheDir = ifset($p_aOptions['cache_dir'], APPFOLDER . 'Cache/Disk/');
	}

	public function init() {}

	/**
	 * Remove a key from the cache
	 * @param string $p_sKey The Key
	 * @param bool $p_bExist flag if we know of the existence
	 * @return boolean
	 */
	public function remove($p_sKey, $p_bExists=false) {

		$sPath = $this->getKeyCachePath($p_sKey);

		if ($p_bExists || $this->exists($sPath)) {
			unlink($sPath);
			unlink($this->getKeyMetaCachePath($p_sKey));
			return true;
		}
		return false;
	}

	/**
	 * Get the full path to a cache item
	 * @param string $p_sKey
	 * @return string
	 */
	protected function getKeyCachePath($p_sKey) {
		// @TODO robert: add leveled key paths to avoid slow disk seeks
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
	 * Check if a key exists in the cache
	 * @param string $p_mKey The Key(s)
	 * @return boolean
	 */
	public function exists($p_sKey) {
		$sPath = $this->getKeyCachePath($p_sKey);
		if(false === file_exists($sPath)) {
			return false;
		}
		$aMetaData = unserialize(file_get_contents($this->getKeyMetaCachePath($p_sKey)));
		// See if the item has a ttl and if it has expired then we delete it.
		if(is_array($aMetaData) && $aMetaData['ttl'] > 0 && $aMetaData['expire_time'] < time()) {
			// Remove the cache item and its metadata file.
			$this->remove($p_sKey, true); // if we don't expect the existence, we could get an endless loop!
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
	public function set($p_sKey, $p_mData, $p_iTTL = 0) {

		$sPath = $this->getKeyCachePath($p_sKey);

		$this->remove($p_sKey);

		$sCacheDir = dirname($sPath);
		if(!is_dir($sCacheDir)) {
			try {
			   mkdir($sCacheDir);
			} catch(PPIException $e) {
				throw new PPI_Exception('Unable to create directory:<br>(' . $sCacheDir . ')');
			}
		}
		if(false === is_writeable($sCacheDir)) {
			$aFileInfo = pathinfo(dirname($sPath));
			chmod($sCacheDir, 775);
			if(false === is_writable($sCacheDir)) {
				throw new PPI_Exception('Unable to create cache file: ' . $p_sKey. '. Cache directory not writeable.<br>(' . $this->_cacheDir . ')<br>Current permissions: ');
			}
		}

		$aMetaData = array(
		   'expire_time' => time() + (int) $p_iTTL,
		   'ttl'		 => $p_iTTL
		);

		return file_put_contents($sPath, serialize($p_mData), LOCK_EX) > 0
			&& file_put_contents($this->getKeyMetaCachePath($p_sKey), serialize($aMetaData), LOCK_EX) > 0;
	}

	/**
	 * Get a value from cache
	 * @param string $p_sKey The Key
	 * @return mixed
	 */
	public function get($p_sKey, $p_mDefault = null) {
		if(false === $this->exists($p_sKey)) {
			return $p_mDefault;
		}
		return unserialize(file_get_contents($this->getKeyCachePath($p_sKey)));
	}

	/**
	 * Check if this adapter is enabled or not.
	 *
	 * @return boolean
	 */
	public function enabled() { return true; }

	/**
	 * Increment the value in the cache
	 *
	 * @param  $p_sKey The key
	 * @param  $p_mIncrement The value to increment by
	 * @return void
	 */
	public function increment($p_sKey, $p_mIncrement) { }

	/**
	 * Decrement the value in the cache
	 *
	 * @param  $p_sKey The Key
	 * @param  $p_mDecrement The value to decrement by
	 * @return void
	 */
	public function decrement($p_sKey, $p_mDecrement) { }

}