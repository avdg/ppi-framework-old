<?php

abstract class PPI_Request_Abstract
implements ArrayAccess, IteratorAggregate, Countable
{
	protected $_array = array();

	protected $_isCollected = true;

	/**
	 * Checks if the data was collected or manual set
	 *
	 * Returns true if all data is collected
	 * from php's environment, false if the current
	 * data is set manuals
	 *
	 * @return bool
	 */
	public function isCollected()
	{
		return $this->_isCollected;
	}

	/**
	 * ArrayAccess implementation for offsetExists
	 *
	 * @param string $offset
	 *
	 * @return bool
	 */
	public function offsetExists($offset)
	{
		return isset($this->_array[$offset]);
	}

	/**
	 * ArrayAccess implementation for offsetGet
	 *
	 * @param string $offset
	 *
	 * @return mixed
	 */
	public function offsetGet($offset)
	{
		if ($this->offsetExists($offset)) {
			return $this->_array[$offset];
		}
		return null;
	}

	/**
	 * ArrayAccess implementation for offsetSet
	 *
	 * @param string $offset
	 * @param mixed  $value
	 *
	 * @return void
	 */
	public function offsetSet($offset, $value)
	{
		if ($value === null) {
			return $this->offsetUnset($offset);
		}

		$this->_array[$offset] = $value;
	}

	/**
	 * ArrayAccess implementation for offsetUnset
	 *
	 * @param string $offset
	 *
	 * @return void
	 */
	public function offsetUnset($offset)
	{
		unset($this->_array[$offset]);
	}

	/**
	 * IteratorAggregate implementation for getIterator
	 *
	 * @return arrayIterator
	 */
	public function getIterator()
	{
		return new arrayIterator($this->_array);
	}

	/**
	 * Countable implementation for count
	 *
	 * @return int
	 */
	public function count()
	{
		return count($this->_array);
	}
}