<?php


	/**
	 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
	 * @copyright (c) Digiflex Development Team
	 * @version 1.0
	 * @author Paul Dragoonis <paul@digiflex.org>
	 * @since Version 1.0
	 * @subpackage Select DAL
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


		function __construct($model, $connInfo) {
			$this->_model = $model;
			//$this->_connection = mysql_connect($connInfo['host'], $connInfo['user'], $connInfo['pass']) or die(mysql_error());
			// this should be handled better instead of using die's, unless theres a reason we're doing DIE's here.
	     //   mysql_select_db($connInfo['db'], $this->_connection) or die("Error Selecting DB: " . $connInfo['db']. " - " . mysql_error());
		}

		function getConnection() {
			return $this->_connection;
		}

		function from($name = '') {
			$this->_from = ($name != '') ? $name : $this->_name;
			return $this;
		}

		function join($table, $on) {
			$this->innerJoin($table, $on);
			return $this;
		}
		function innerJoin($table, $on) {
			$this->addJoin($table, $on, self::INNER_JOIN);
			return $this;
		}
		function leftJoin($table, $on) {
			$this->addJoin($table, $on, self::LEFT_JOIN);
			return $this;
		}
		function rightJoin($table, $on) {
			$this->addJoin($table, $on, self::RIGHT_JOIN);
			return $this;
		}
		function addJoin($table, $on, $type) {
			$this->_joinCount++;
			array_push($this->_joinOrder, array(
				'table' => $table,
				'on' 	=> $on,
				'type' 	=> $type
			));
		}

		function where($clause = '') {
			$this->_where = array_merge($this->_where, (array) $clause);
			return $this;
		}

		function order($order = '') {
			$this->_order = $order;
			return $this;
		}

		function group($group = '') {
			$this->_group = $group;
			return $this;
		}

		function limit($limit) {
			$this->_limit = $limit;
			return $this;
		}
		function columns($columns) {
			$this->_columns = ($columns != '') ? $columns : self::SQL_WILDCARD;
			return $this;
		}

		function setFetchMode($mode = '') {
			// should make the default fetch mode a configuration value
			$this->_fetchMode = ($mode != '') ? $mode : 'assoc';
		}
		function getList($where = 'deprecated') {
			$this->generateQuery();
			return $this->query();
			//return $this->getRows();
		}

		private function getRows() {
			$rows = array();
			if($this->_fetchMode == 'assoc') {
				while($row = mysql_fetch_assoc($this->_queryRet)) {
					$rows[] = $row;
				}
				mysql_free_result($this->_queryRet);
			} elseif($this->_fetchMode == 'num') {
				while($row = mysql_fetch_row($this->_queryRet)) {
					$rows[] = $row;
				}
				mysql_free_result($this->_queryRet);
			}
			$this->setDefaults(); // Can't rememebr what this is for.
			return $rows;
		}

		// can't remember what this was made for
		function setDefaults() {

		}

		function __toString() {
			if($this->_query == '') {
				$this->generateQuery();
			}
			return $this->_query;
		}
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
			// order by
			if($this->_order != '') {
				$query .=  ' ' . self::SQL_ORDER_BY . ' ' . $this->_order;
			}
			// group by
			if($this->_group != '') {
				$query .= ' ' . self::SQL_GROUP_BY . ' ' . $this->_group;
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

		private function logError() {
			// check if we need to email
			// product debugging information
			throw new PPI_Exception("SQL Error: " . mysql_error($this->_connection), $this->_queries);
		}

		private function generateObjectQuery() {
			$this->_query = $query;
		}

		/**
		 * Run the actual query, make this run a PPI_Model query instead
		 *
		 * @return unknown
		 */
		private function query() {
			
			// new 
			return $this->_model->query($this->_query);
			
			
			// add to a list of arrays for logging purposes
			array_push($this->_queries, $this->_query);
			$this->_queryRet = mysql_query($this->_query, $this->_connection);
			if(mysql_error($this->_connection) != '') {
				$this->logError(mysql_error($this->_connection));
			}
			return $this->_queryRet;
		}
	}
