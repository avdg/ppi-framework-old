<?php

/**
 *
 * @version   1.0
 * @author    Paul Dragoonis <dragoonis@php.net>
 * @license   http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright Digiflex Development
 * @package   PPI
 */

class PPI_Model_Select {

    const INNER_JOIN     = 'INNER JOIN';
    const LEFT_JOIN      = 'LEFT JOIN';
    const RIGHT_JOIN     = 'RIGHT JOIN';
    const FULL_JOIN      = 'FULL JOIN';
    const CROSS_JOIN     = 'CROSS JOIN';
    const NATURAL_JOIN   = 'NATURAL JOIN';

    const SQL_WILDCARD   = '*';
    const SQL_SELECT     = 'SELECT';
    const SQL_UNION      = 'UNION';
    const SQL_UNION_ALL  = 'UNION ALL';
    const SQL_FROM       = 'FROM';
    const SQL_WHERE      = 'WHERE';
    const SQL_DISTINCT   = 'DISTINCT';
    const SQL_GROUP_BY   = 'GROUP BY';
	const SQL_LIMIT		 = 'LIMIT';
    const SQL_ORDER_BY   = 'ORDER BY';
    const SQL_HAVING     = 'HAVING';
    const SQL_FOR_UPDATE = 'FOR UPDATE';
    const SQL_AND        = 'AND';
    const SQL_AS         = 'AS';
    const SQL_OR         = 'OR';
    const SQL_ON         = 'ON';
    const SQL_ASC        = 'ASC';
    const SQL_DESC       = 'DESC';

	private $_connection;
	private $_logConnection;
	protected $_name;
	private $_primary;
	private $_where	= array();
	private $_whereParts = array();
	private $_order = '';
	// this error when we try to assign it to self::$_name;
	private $_from = '';
	private $_columns = self::SQL_WILDCARD;
	private $_limit = array();
	private $_group = '';
	private $_query = '';
	private $_queryRet = null;
	private $_fetchMode = 'assoc';
	private $_innerJoin = array();
	private $_joinOrder = array();
	private $_joinCount = 0;
	private $_queries = array();
	private $_model;


	function __construct($model) {
		$this->_model = $model;
	}

	/**
	 * Set the FROM table
	 * @param string $name The Table Name
	 */
	function from($name = '') {
		$this->_from = ($name != '') ? $name : $this->_name;
		return $this;
	}

	/**
	 * Perform an INNER JOIN
	 * @param string $table The Table
	 * @param string $on The ON Clause
	 */
	function join($table, $on) {
		$this->innerJoin($table, $on);
		return $this;
	}
	
	/**
	 * Perform an INNER JOIN
	 * @param string $table The Table
	 * @param string $on The ON Clause
	 */		
	function innerJoin($table, $on) {
		$this->addJoin($table, $on, self::INNER_JOIN);
		return $this;
	}
	
	/**
	 * Perform a LEFT JOIN
	 * @param string $table The Table
	 * @param string $on The ON Clause
	 */		
	function leftJoin($table, $on) {
		$this->addJoin($table, $on, self::LEFT_JOIN);
		return $this;
	}
	
	/**
	 * Perform a RIGHT JOIN
	 * @param string $table The Table
	 * @param string $on The ON Clause
	 */		
	function rightJoin($table, $on) {
		$this->addJoin($table, $on, self::RIGHT_JOIN);
		return $this;
	}
	
	/**
	 * Add a join to the joinOrder
	 * @param string $table The Table Name
	 * @param string $on The ON Clause
	 * @param string $type ('left', 'right', 'inner')
	 */	
	function addJoin($table, $on, $type) {
		$this->_joinCount++;
		array_push($this->_joinOrder, array(
			'table' => $table,
			'on' 	=> $on,
			'type' 	=> $type
		));
	}

	/**
	 * Set the where clause(s)
	 * @param mixed $clause The Clause(s)
	 */
	function where($clause = null) {
		$this->_where = array_merge($this->_where, (array) $clause);
		return $this;
	}

	/**
	 * Set the ORDER BY
	 * @param string $order The ORDER BY
	 */
	function order($order = '') {
		$this->_order = $order;
		return $this;
	}

	/**
	 * Set the GROUP BY
	 * @param string $group The GROUP BY
	 */
	function group($group = '') {
		$this->_group = $group;
		return $this;
	}

	/**
	 * Set the LIMIT
	 * @param string $limit
	 */
	function limit($limit) {
		$this->_limit = $limit;
		return $this;
	}
	
	/**
	 * Set the columns on the SELECt 
	 * @param string $columns The Columns
	 */
	function columns($columns) {
		$this->_columns = ($columns != '') ? $columns : self::SQL_WILDCARD;
		return $this;
	}

	/**
	 * Get the rows back from $this->query()
	 * @return PPI_Model_Resultset
	 */
	function getList() {
		$this->generateQuery();
		return $this->query();
	}

	/**
	 * Magic string cast function, return the query name
	 */
	function __toString() {
		if($this->_query == '') {
			$this->generateQuery();
		}
		return $this->_query;
	}
	
	/**
	 * Generate the query
	 */
	private function generateQuery() {
		$query = '';
		// Security Cleanup prior to query generation
		// select + from
		$query .= self::SQL_SELECT . ' ' . $this->_columns . ' ' . self::SQL_FROM . ' ' . $this->_from;

		if(count($this->_joinOrder) > 0) {
			foreach($this->_joinOrder as $join) {
				$query .= ' ' . $join['type'] . ' ' . $join['table'] . ' ' . self::SQL_ON . ' ' . $join['on'];
			}
		}
		// where
		if(!empty($this->_where)) {
			$query .= ' ' . self::SQL_WHERE . ' (' . implode(' ' . self::SQL_AND . ' ', $this->_where) . ')';
		}
		
		// group by
		if($this->_group != '') {
			$query .= ' ' . self::SQL_GROUP_BY . ' ' . $this->_group;
		}		
		
		// order by
		if($this->_order != '') {
			$query .=  ' ' . self::SQL_ORDER_BY . ' ' . $this->_order;
		}

		// limit
		if(is_array($this->_limit) && count($this->_limit) > 0) {
			if(count($this->_limit) == 1) {
				$limit = ' ' . self::SQL_LIMIT . ' ' . $this->_limit[0];
				$query .= $limit;
			} elseif(count($this->_limit) == 2) {
				$limit = ' ' . self::SQL_LIMIT . ' ' . $this->_limit[0] . ', ' . $this->_limit[1];
				$query .= $limit;
			}
		} elseif(is_string($this->_limit) && $this->_limit != '') {
			$query .= ' ' . self::SQL_LIMIT . ' ' . $this->_limit;
		}
		$this->_query = $query;
	}

	/**
	 * Log the error in the d
	 */
	private function logError() {
		// check if we need to email
		// product debugging information
		throw new PPI_Exception("SQL Error: " . mysql_error($this->_connection), $this->_queries);
	}

	private function generateObjectQuery() {
		$this->_query = $query;
	}

	/**
	 * Run the query
	 *
	 * @return PPI_Model_Resultset
	 */
	private function query() {
		return $this->_model->query($this->_query);

	}
}
