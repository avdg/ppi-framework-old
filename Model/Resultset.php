<?php

class PPI_Model_Resultset implements Iterator, ArrayAccess, Countable {

	/**
	* The instance of PDOStatement
	*/
	private $_statement;

	/**
	* The default fetch mode
	*/
	private $_fetchMode  = PDO::FETCH_ASSOC;

	/**
	* List of the acceptable fetch modes
	*/
	private $_fetchModes = array(
		'assoc'   => PDO::FETCH_ASSOC,
		'numeric' => PDO::FETCH_NUM,
		'object'  => PDO::FETCH_OBJ,
		'both'    => PDO::FETCH_BOTH
	);

	private $_dataPointer = 0;
	private $_rows = array();

	function __construct(PDOStatement $statement) {
		// Config override for fetchmode. If it's a valid fetchmode then we override
		$oConfig = PPI_Helper::getConfig();
		if(isset($oConfig->db->fetchmode) && $oConfig->db->fetchmode != '' && array_key_exists($oConfig->db->fetchmode, $this->_fetchModes)) {
			$this->_fetchMode = $oConfig->db->fetchmode;
		}
		$this->_statement = $statement;
	}

	/**
	 * Fetch the next row from the statement class
	 *
	 * @param null|string $p_sFetchMode
	 * @todo Make this an isset() lookup instead of in_array()
	 * @return unknown
	 */
	function fetch($p_sFetchMode = null) {
		// If a custom fetchmode was passed and it's a valid fetch mode then we use it otherwise defaulting to  $this->_fetchMode
		$sFetchMode = ($p_sFetchMode !== null && in_array($p_sFetchMode, $this->_fetchModes)) ? $p_sFetchMode : $this->_fetchMode;
		return $this->_statement->fetch($sFetchMode);
	}

	/**
	 * Fetch all the records from the statement class
	 *
	 * @param null|string $p_sFetchMode
	 * @todo Make this an isset() lookup instead of in_array()
	 * @return array
	 */
	function fetchAll($p_sFetchMode = null) {
		// If a custom fetchmode was passed and it's a valid fetch mode then we use it otherwise defaulting to  $this->_fetchMode
		$sFetchMode = ($p_sFetchMode !== null && in_array($p_sFetchMode, $this->_fetchModes)) ? $p_sFetchMode : $this->_fetchMode;
		return $this->_statement->fetchAll($sFetchMode);
	}

	function countRows() {
		return $this->_statement->rowCount();
	}

	function offsetExists($offset) {
		return isset($this->_rows[(int) $offset]);
	}


	function offsetGet($offset) {
		$this->_pointer = (int) $offset;
		return $this->current();
	}

	function offsetUnset($offset) {

	}

	function offsetSet($offset, $value) {
		$this->_rows[(int) $offset] = $value;
	}

	// For Countable interface
	function count() {
		return $this->countRows();
	}

	function getStatement() {
		return $this->_statement;
	}

	function current() {
		if(empty($this->_rows[$this->_dataPointer])) {
			$this->_rows[$this->_dataPointer] = $this->_statement->fetch($this->_fetchMode);
		}
		return $this->_rows[$this->_dataPointer];
	}

	function key() {
		return $this->_dataPointer;
	}

	function next() {
		++$this->_dataPointer;
	}

	function rewind() {
		$this->_dataPointer = 0;
	}

	function valid() {
		return $this->_dataPointer < $this->count();
	}

}