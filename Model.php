<?php
/**
 * @author    Paul Dragoonis <dragoonis@php.net>
 * @license   http://opensource.org/licenses/mit-license.php MIT
 * @copyright Digiflex Development
 * @package   Model
 * @link      www.ppiframework.com
 */
abstract class PPI_Model {

    /**
     * The last query executed
     *
     * @var string
     */
	protected $sLastQuery;

    /**
     * The last database error reported
     *
     * @var string
     */
	protected $sLastError;

    /**
     * The current table's name
     *
     * @var string
     */
	protected $sTableName;

    /**
     * The current table's primary key field name
     *
     * @var string
     */
	protected $sTableIndex;
	protected $sDbConnection;
	protected $parent_name;

    /**
     * The currently in use database hostname
     *
     * @var string
     */
	protected $sHostName;

    /**
     * The currently in use database username
     *
     * @var string
     */
	protected $sUserName;

    /**
     * The currently in use database password
     *
     * @var string
     */
	protected $sPassword;

    /**
     * The currently in use database name
     *
     * @var string
     */
	protected $sDataBase;

    /**
     * The default and current fetch mode for PDO
     *
     * @var int
     */
	protected $sFetchMode      = PDO::FETCH_ASSOC;
	private $aQueries          = array();

    /**
     * Attributes storing the users meta data if you use __get(),__set() stuff
     *
     * @var array
     */
	private $metaAttributes    = array();

    /**
     * The PDO Instance
     *
     * @var null|PDO
     */
    protected $rHandler = null;

	/**
	 * Constructor for the SQL API.
	 * Used for making connections and setting up instance information.
	 * This information is used throughout this class's lifecycle
	 * @param string $p_sTableName  The name of the DB Table
	 * @param string $p_sTableIndex  The index of the primary key
	 * @param string $p_sDBKey  The database section to use for the DSN
	 * @param integer $p_iRecord  To obtain the record and set it as meta data
	 * @throws PPI_Exception
	 */

	function __construct($p_sTableName = "", $p_sTableIndex = "", $p_sDBKey = "", $p_iRecordID = 0) {

		if (!extension_loaded('pdo')) {
			throw new PPI_Exception('PDO is not enabled');
		}

		if (empty($p_sDBKey)) {
			$p_sDBKey = "default";
		}
		if ($p_sTableName == '' || $p_sTableIndex == '') {
			throw new PPI_Exception('Table name or index not found');
		}
		$this->sTableIndex = $p_sTableIndex;
		$this->sTableName  = $p_sTableName;

		$oConfig = $this->getConfig();

		// Multiple DB Check and Verify their key exists
		if($p_sDBKey !== 'default') {
			if(!isset($oConfig->db->$p_sDBKey)) {
				throw new PPI_Exception('Unable to find database connection information for key: ' . $p_sDBKey);
			}

		// Look for db.default in the config, if it doesn't exist revert back to db.*
		} else {
			$dbInfo = isset($oConfig->db->default) ? $oConfig->db->default->toArray() : $oConfig->db->toArray();
		}

		// Verification that all the required DB fields are setup properly
		foreach(array('host', 'username', 'password', 'database', 'enabled') as $field) {
			if(!isset($dbInfo[$field])) {
				throw new PPI_Exception('Database configuration error. Unable to find '.$field.' in '.$p_sDBKey);
			}
			// @todo: Why are we checking for 'password' here ?
			if($field !== 'password' && $dbInfo[$field] == '') {
				throw new PPI_Exception('No information found for database configuration option: '.$field);
			}
		}
		if(!$dbInfo['enabled']) {
			throw new PPI_Exception('Trying to use database configuration for <b>'.$p_sDBKey.'</b> and it is turned off. Check your database configuration file');
		}

		$this->sHostName = $dbInfo['host'];
		$this->sUserName = $dbInfo['username'];
		$this->sPassword = $dbInfo['password'];
		$this->sDataBase = $dbInfo['database'];

		// Try our PDO connection. this checks for persistent connections and charsets too
		try {
			$persistent = false;
			if(isset($dbInfo['persistent']) && $dbInfo['persistent'] == true) {
				$persistent = true;
			}
			$this->rHandler = new PDO($this->makeDSN(),
									 $this->sUserName,
									 $this->sPassword,
									 $persistent ? array(PDO::ATTR_PERSISTENT => true) : array()
			);
			$this->rHandler->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		} catch (PDOException $e) {
			throw new PPI_Exception('Database Connection Error: ' . $e->getMessage());
		}

		// Lets store our charset being overridable from the config, and defaulting to utf8.
		$bIsCharsetOverride = isset($dbInfo['charset']) && $dbInfo['charset'] != '';
		$charset            = strtolower($bIsCharsetOverride ? $oConfig->db->charset : 'utf8');
		// If the charset is overriden lets check this charset exists in the mysqld. Exception upon non-existant charset
		if($bIsCharsetOverride && !$this->isValidCharset($charset)) {
			throw new PPI_Exception('Invalid charset specified');
		}

		// Set the correct charset in the db
		$this->exec('SET NAMES ' . $charset, false);

		// Lets check that our table exists that our model is setup against
		if(!$this->isTableExist($this->sTableName)) {
			throw new PPI_Exception('Table: ' . $p_sTableName . ' doesn\'t exist');
		}

		// Meta data handling, if the constructor has been passed a record ID, lets load the meta data
		if($p_iRecordID != 0) {
			$this->find($p_iRecordID, true);
		}
	}

	/**
	 * Create a DSN string
     *
	 * @todo Have this accept params
	 * @return string The DSN
	 */
	function makeDSN() {
		return 'mysql:host=' . $this->sHostName . ';dbname=' . $this->sDataBase;
	}


	/**
	 * Returns the PPI Select object to allow object-oriented query selecting
     *
	 * @return PPI_Model_Select
	 */
	function select() {
		return new PPI_Model_Select($this);
	}

    /**
     * Append to a list of queries in the registry
     *
     * @param string $p_sQuery The Query
     * @return void
     */
    private function appendQueryList($p_sQuery) {
        $oRegistry = PPI_Helper::getRegistry();
        $oRegistry->set('PPI_Model::Queries_Backtrace', array_merge($oRegistry->get('PPI_Model::Queries_Backtrace', array()), (array) $p_sQuery));
    }

	/**
	 * Performs the actual query with some error handling.
	 * Returns what mysql_query() returns on success, else false.
     *
	 * @param string $p_sQuery The Query
	 * @param boolean $p_bLogQuery Default is true. If true will log the query.
	 * @throws PPI_Exception
	 * @return array
	 */
	function query($p_sQuery, $p_bLogQuery = true) {
		try {
			if($p_bLogQuery) {
				$this->appendQueryList($p_sQuery);
			}
			$ret = $this->rHandler->prepare($p_sQuery);
			$ret->execute();
			return new PPI_Model_Resultset($ret);
		} catch(PDOException $e) {
			throw new PPI_Exception($e->getMessage());
		}
	}

	/**
	 * Deletes a record by primary key
	 *
	 * @param integer $p_iRecordID Optional Record ID to delete. If nothing passed then will look for record ID in meta data
	 * @throws PPI_Exception
	 * @return integer The affected rows
	 */
	function delete($p_iRecordID = 0) {
		if($p_iRecordID === 0) {
			$p_iRecordID = isset($this->metaAttributes[$this->sTableIndex]) ? $this->metaAttributes[$this->sTableIndex] : 0;
			if($p_iRecordID === 0) {
				throw new PPI_Exception('Unable to delete item');
			}
		}
        $sQuery = "DELETE FROM {$this->sTableName} WHERE {$this->sTableIndex} = ".$this->quote($p_iRecordID);
        return $this->exec($sQuery);
	}

	/**
	 * Wrapper for PDO::exec. This will execute a query such as a DELETE or UPDATE.
     *
	 * @param string $p_sQuery The Query
	 * @return integer The affected rows
	 */
	function rawQuery($p_sQuery) {
        return $this->exec($p_sQuery);
	}

	/**
	 * Wrapper for PDO::exec. This will execute a query such as a DELETE or UPDATE.
     *
	 * @param string $p_sQuery The Query
	 * @return integer The affected rows
	 */
	function exec($p_sQuery, $p_bLogQuery = true) {
		if($p_bLogQuery === true) {
			$this->appendQueryList($p_sQuery);
		}
		try {
			return $this->rHandler->exec($p_sQuery);
		} catch(PDOException $e) {
			throw new PPI_Exception($e->getMessage());
		}
	}

	/**
	 * Set a piece of meta data
     *
	 * @param string $p_sName The name of the meta data
	 * @param mixed $p_mValue The value of the meta data
	 * @return Return the current class, useful for method chaining
	 */
	function __set($p_sName, $p_mValue) {
		if(trim($p_sName) != '') {
			$this->metaAttributes[$p_sName] = $p_mValue;
		}
		return $this;
	}

	/**
	 * Checks if meta data has been set
     *
	 * @param string $p_sName The field name
     * @return boolean
	 */
	function __isset($p_sName) {
	    return array_key_exists($p_sName, $this->metaAttributes);
	}

	/**
	 * Unset meta handler so you can unset the meta data
     *
	 * @param string $p_sName The field name
     * @return void
	 */
	function __unset($p_sName) {
		if(array_key_exists($p_sName, $this->metaAttributes)) {
			unset($this->metaAttributes[$p_sName]);
		}
	}

	/**
	 * Obtain the value of the meta data set
     *
	 * @param string $p_sName The Property Name
	 * @return mixed
	 */
	function __get($p_sName) {
		return array_key_exists($p_sName, $this->metaAttributes) ? $this->metaAttributes[$p_sName] : null;
	}

	/**
	 * Fire off the meta attributes to putRecord and clear the attributes
     *
	 * @param boolean True will clear the meta data after saving
	 * @return array
	 */
	function save($p_bClear = true) {
		return $this->putRecord($this->getMetaAttributes($p_bClear));
	}

	/**
	 * Go through each attribute and add it as meta data
     *
	 * @param array $p_aAttributes The attributes to be set
	 * @return void
	 */
	function setMetaAttributes(array $p_aAttributes) {
		foreach($p_aAttributes as $name => $attr) {
			$this->metaAttributes[$name] = $attr;
		}
	}

	/**
	 * Obtain the metaAttributes that have been set. Optionally clear them at the same time.
     *
	 * @param boolean $p_bClearAttrs Whether to clean the attributes before returning them
	 * @return array
	 */
	function getMetaAttributes($p_bClearAttrs = false) {
		$attrs = $this->metaAttributes;
		if($p_bClearAttrs) {
			$this->metaAttributes = array();
		}
		if(isset($attrs['rHandler'])) {
			unset($attrs['rHandler']);
		}
		return $attrs;
	}

	/**
	 * If the record exists, then we will perform the update function
	 * This will be called unless the update function has been overloaded by the applications model
	 * If the record doesn't exist, then we will perform the insert function
	 * This will be called unless the insert function has been overloaded by the applications model
     *
	 * @param array $p_aRecord
	 * @param array $p_aOptions
	 * @throws PDO_Exception
	 * @return integer
	 */
	function putRecord(array $p_aRecord) {
		// If the primary key is found, its an update
		if (isset($p_aRecord[$this->sTableIndex]) && $p_aRecord[$this->sTableIndex] !== null) {
			$this->update($p_aRecord, $this->sTableIndex . ' = ' . $p_aRecord[$this->sTableIndex]);
			return $p_aRecord[$this->sTableIndex];

		// No primary key found its an insert
		} else {
			return $this->insert($p_aRecord);
		}
	}

	/**
	 * Insert a record into the current table
     *
	 * @param array $p_aRecord
	 * @return integer Last Insert ID
	 */
	function insert(array $p_aRecord) {
		$sKeys   = '`' . implode('`,`', array_keys($p_aRecord)) . '`';
		$sValues = rtrim(str_repeat('?,', count($p_aRecord)), ',');
		$sQuery  = "INSERT INTO {$this->sTableName} ($sKeys) VALUES ($sValues)";
		try {
			$oResult 		= $this->rHandler->prepare($sQuery);
			$oQueryResult 	= $oResult->execute(array_values($p_aRecord));
		} catch(PDOException $e) {
			throw new PPI_Exception($e->getMessage());
		}
		return $this->rHandler->lastInsertId();
	}

	/**
	 * Performs extended inserts
     *
	 * @param array $p_aRecords Multi Dimensional array of records to insert
	 * @return integer
	 */
	function insertMultiple(array $p_aRecords) {
		if(!isset($p_aRecords[0]) || !is_array($p_aRecords[0])) {
			throw new PPI_Exception('Invalid data structure format passed to insertMultiple');
		}

		$iMaxAllowedPacket = $this->getSystemVar('max_allowed_packet');
		$iThreshold = $iMaxAllowedPacket - 1024;
		$sKeys      = implode(',', $this->parse_reserved_keys(array_keys($p_aRecords[0])));
		$fullRecordsCount = count($p_aRecords);
		$sQuery = '';
		$selectedRecords = array();
		$iSelectedRecordsLength = $i = $modifiedRecords = 0;
		// Loop over the rows - have each row append its strlen() to a counter for length check.
		// Unset the record from all records. put this loop in a function and have it return
		foreach($p_aRecords as $key => $aRecord) {

			// Sanitize and setup the values
			$sValues = '';
			$recordValues = array_values($aRecord);
			foreach($recordValues as $field) {
				$sValues .= $this->quote($field) . ',';
			}
			if($sValues != '') {
				$sValues = substr($sValues, 0, -1);	 // Remove the last comma
			}
			// Append selected value
			$selectedRecords[] = $sValues;
			// Update the length counter
			$iSelectedRecordsLength += strlen($sValues);

			// If we exceed our threshold
			if($iSelectedRecordsLength >= $iThreshold || $i == ($fullRecordsCount - 1)) {
				// Append the next record to the query.
				$sQuery                 .= "INSERT INTO {$this->sTableName} ($sKeys) VALUES (" . implode(' ), ( ', $selectedRecords) . ')';
				$modifiedRecords        += $this->exec($sQuery); // Execute the query
				$sQuery                 = ''; // Blank the query
				$selectedRecords        = array(); // Blank the selected records
				$iSelectedRecordsLength = 0;

			}
			$i++;
		}
		return $modifiedRecords;
	}

	/**
	 * Update a record
     *
	 * @param array $p_aRecord Record Data
	 * @param string $p_mWhere Optional where clause
	 * @return integer
	 * @throws PPI_Exception
	 */
	function update($p_aRecord, $p_mWhere = null) {
		$aWhere = array();
		if($p_mWhere !== null) {
			$aWhere = is_string($p_mWhere) ? array($p_mWhere) : $p_mWhere;
		}

		$sData 		= implode(' = ?, ', $this->parse_reserved_keys(array_keys($p_aRecord))) . ' = ?';	// Setup the field = ? template
		$sQuery 	= "UPDATE {$this->sTableName} SET $sData";
		$sQuery     .= !empty($aWhere) ? ' WHERE ' . implode(' AND ', $aWhere) : '';
		try {
			$oResult = $this->rHandler->prepare($sQuery);
			$oResult->execute(array_values($p_aRecord));
			return $oResult->rowCount();
		} catch(PDOException $e) {
			throw new PPI_Exception($e->getMessage());
		}
	}


	/**
	 * This function is used to backtick all fields
     *
	 * @param array $p_aKeys The array of key names
	 * @return array
	 */
	function parse_reserved_keys($p_aKeys) {
		foreach($p_aKeys as $key => $val) {
			$p_aKeys[$key] = "`$val`";
		}
		return $p_aKeys;
	}

	/**
	 * This function is the same as getList() however it will return you its first row directly as an array
     *
	 * @see $this->getList()
	 * @todo Perform a fetchMode check to check the return type
	 * @param mixed $p_mFilter The filter.
	 * @param string $p_sOrder The order clause
	 * @param integer $p_iLimit The limit clause
	 * @param array $p_aExtras
	 * @return array
	 */
	function getRecord($p_mFilter, $p_sOrder = "", $p_iLimit = "", $p_sGroup = '') {
		return $this->getList($p_mFilter, $p_sOrder, $p_iLimit, $p_sGroup)->fetch();
	}

	/**
	 * From the instance information, retreives information from the table.
	 * Building up query of orders, limits and clauses it returns a data structure of the records found.
	 * If you do not have instance information, or would like to access another table then $p_aExtras should be populated.
     *
	 * @param string $p_mFilter WHERE
	 * @param string $p_sOrder ORDER BY
	 * @param string $p_iLimit LIMIT
	 * @param string $p_sGroup GROUP BY
	 * @throws PPI_Exception
	 * @throws PDO_Exception
	 * @return array
	 */
	function getList($p_mFilter = '', $p_sOrder = '', $p_iLimit = '', $p_sGroup = '') {
		try {
			// Where
			/* Turn the filters into a string (if not already) */
			if (is_array($p_mFilter) && !empty($p_mFilter)) {
				$sFilter = 'WHERE ' . implode(' AND ', $p_mFilter);
			} elseif(is_string($p_mFilter) && $p_mFilter != '') {
				$sFilter = 'WHERE ' . $p_mFilter;
			} else {
				$sFilter = '';
			}
			

			// Group
			$sGroup = $p_sGroup != '' ? ' GROUP BY ' . $p_sGroup : '';			

			// Order
			$sOrder = is_array($p_sOrder) && !empty($p_sOrder) ? 'ORDER BY ' : '';
			if($sOrder == '') {
				$sOrder = ($p_sOrder != '') ? 'ORDER BY ' : '';
			}
			foreach ((array) $p_sOrder as $key => $val) {
				$sOrder .= $val.',';
			}
			$sOrder = substr($sOrder, 0, -1);

			// Limit
			$sLimit = ($p_iLimit != '') ? 'LIMIT ' . $p_iLimit : '';
			$sQuery = "SELECT * FROM {$this->sTableName} $sFilter $sOrder $sGroup $sLimit";
			return $this->query(rtrim($sQuery));

		} catch(PDOException $e) {
			throw new PPI_Exception($e->getMessage());
		} catch(PPI_Exception $e) {
			throw new PPI_Exception($e->getMessage());
		}
	}

	/**
	 * Fetch a singular row from the getList()
     *
	 * @see PPI_Model->getList()
	 */
	function fetch($p_mFilter = '', $p_sOrder = '', $p_iLimit = '', $p_sGroup = '') {
		return $this->getList($p_mFilter, $p_sOrder, $p_iLimit, $p_sGroup)->fetch();
	}


	/**
	 * This will return you one row by getting a record by its primary key.
     *
	 * @param integer $search
	 * @param boolean $p_bSetMetaData Default is false. If true will set the meta data upon successfull fetch() of the record
	 * @return array
	 */
	function find($search, $p_bSetMetaData = false) {
		try {
			$sQuery = 'SELECT * FROM `'.$this->sTableName. '` WHERE `' . $this->sTableIndex . '` = ' . $this->quote($search);
			$oResult = $this->rHandler->prepare($sQuery);
			$this->appendQueryList($sQuery);
			$oResult->execute();
			$aRecord = $oResult->fetch($this->sFetchMode);
			if($p_bSetMetaData !== false && $aRecord !== false) {
				/*
				if(isset($aRecord['rHandler'])) {
					unset($aRecord['rHandler']);
				}
				*/
				$this->setMetaAttributes($aRecord);
			}
		} catch(PDOException $e) {
			throw new PPI_Exception($e->getMessage());
		}
		return ($aRecord !== false && !empty($aRecord)) ? $aRecord : array();
	}

	/**
	 * Identify if a record exists or not, specified by the primary key
	 *
	 * @param integer $p_iPrimaryKey
	 * @return boolean
	 */
	function isExist($p_iPrimaryKey) {
		return (bool) $this->find($p_iPrimaryKey);
	}

	/**
	 * This sets the fetch mode for PDO to retreive records
     *
	 * @param string $p_sMode
	 * @return void
	 */
	function setFetchMode($p_sMode) {
		switch($p_sMode) {
			case 'assoc':
			case 'array':
				$this->sFetchMode = PDO::FETCH_ASSOC;
				break;
			case 'object':
				$this->sFetchMode = PDO::FETCH_OBJ;
				break;
			case 'number';
			case 'numeric';
				$this->sFetchMode = PDO::FETCH_NUM;
				break;

			default:
				throw new PPI_Exception('Invalid Fetch Mode: ' . $p_sMode);
				break;

		}
	}

	/**
	 * Create an IN statement from an input such as a string or an array
     *
	 * @param mixed $p_mValues The values to be added to the IN()
	 * @return mixed On error return false. On sucess return the IN() filled string
	 */
	function makeIN($p_mValues = '') {

		if(is_scalar($p_mValues)) {
			if($p_mValues == '') {
				return '';
			}
			return "IN($p_mValues)";
		}
		if(is_array($p_mValues)) {
			$sql = 'IN(';
			foreach($p_mValues as $key => $val) {
				$p_mValues[$key] = "'$val'";
			}
			$sql .= implode(',', $p_mValues) . ')';
			return $sql;
		}
		return false;
	}

	/**
	 * Perform a MySQL MAX() lookup on the current table.
	 *
	 * @param string $p_sField The field name you wish to perform the max() on
	 * @param string $p_sClause The optional clause for the query
	 * @return integer
	 */
	function findMax($p_sField, $p_sClause = '') {
		return $this->findMinMax('MAX', $p_sField, $p_sClause);
	}

	/**
	 * Perform a MySQL MAX() lookup on the current table.
	 *
	 * @param string $p_sField The field name you wish to perform the min() on
	 * @param string $p_sClause The optional clause for the query
	 * @return integer
	 */
	function findMin($p_sField, $p_sClause = '') {
		return $this->findMinMax('MIN', $p_sField, $p_sClause);
	}

	/**
	 * Handler function for MIN() and MAX()
     *
	 * @param string $p_sType Type ('MIN' or 'MAX')
	 * @param string $p_sField The field to perform the minmax on
	 * @param string $p_sClause Optional clause to apply to the query
	 * @return integer
	 */
	private function findMinMax($p_sType, $p_sField, $p_sClause = '') {
		$query = "SELECT $p_sType($p_sField) value FROM $this->sTableName";
		if($p_sClause != '') {
			$query .= " WHERE $p_sClause";
		}
		$query .= ' LIMIT 1';
		$row = $this->query($query);
		return (bool) $row ? $row[0]['value'] : 0;
	}

	/**
	 * Check if record(s) exist
     *
	 * @param mixed $p_sRecordName
	 * @param mixed $p_sRecordValue
	 * @return boolean
	 */
	function isRecordExist($p_sRecordName, $p_sRecordValue) {
		if($p_sRecordName != '' && $p_sRecordValue != '') {
			$ret = $this->query("SELECT count({$this->sTableIndex}) total FROM {$this->sTableName} WHERE $p_sRecordName = '$p_sRecordValue'");
			return isset($ret[0]) && $ret[0]['total'] > 0;
		}
		return false;
	}

	/**
	 * This checks if the table for your model exists
     *
	 * @param string $p_sTableName
	 * @return boolean
	 */
	function isTableExist($p_sTableName) {
		return (bool) $this->query("SHOW TABLES LIKE " . $this->quote($p_sTableName), false)->fetch();
	}

	/**
	 * This checks if a DB exists or not.
     *
	 * @param string $p_sDBName
	 * @return boolean
	 */
	function isDBExist($p_sDBName) {
		return (bool) $this->query("SHOW DATABASES LIKE " . $this->quote($p_sDBName), false)->fetch();
	}

	/**
	 * Obtain a random record from the table, with optional where clause and ability to specify the amount of random items.
	 *
	 * @param string $p_sWhere Where clause
	 * @param integer $p_iAmount Amount of rows to return
	 * @return array
	 */
	function getRandom($p_sWhere = '', $p_iAmount = 1) {
		$rows = $this->getRecord($p_sWhere, 'RAND()', $p_iAmount);
		return ($p_iAmount == 1 && isset($rows[0])) ? $rows[0] : $rows;
	}

	/**
	 * Counts the number of records returned from your table
     *
	 * @param string $p_sFilter The Clause
	 * @param string $p_iLimit The Limit
	 * @param string $p_sGroup The Group By
	 * @return integer
	 */
	function countRecords($p_sFilter = '', $p_iLimit = '', $p_sGroup = '') {
		$query = "SELECT COUNT({$this->sTableIndex}) as total FROM `{$this->sTableName}`";
		$query .= $p_sFilter != '' ? ' WHERE '    . $p_sFilter : '';
		$query .= $p_sGroup != ''  ? ' GROUP BY ' . $p_sGroup  : '';
		$query .= $p_iLimit != ''  ? ' LIMIT '    . $p_iLimit  : '';
		$aRows = $this->query($query);
		return ($aRows !== false && !empty($aRows)) ? $aRows[0]['total'] : 0;
	}

	/**
	 * Delete a record by clause
	 *
	 * @param mixed $p_mClause The where clause
	 * @return integer The number of rows affected
	 */
	function delRecord($p_mClause = null) {
		$aWhere = ($p_mClause !== null) ? array_merge(array(), (array) $p_mClause) : array();
		if(empty($aWhere)) {
			throw new PPI_Exception('Unable to wipe table contents, use truncation specific method instead');
		}
		$sQuery = "DELETE FROM `{$this->sTableName}` WHERE " . implode(' AND ', $aWhere);
		return $this->exec($sQuery);
	}

	/**
     * Truncate the table assigned to this model
     *
     * @return int
     */
	function truncate() {
		return $this->exec("TRUNCATE TABLE `{$this->sTableName}`");
	}


	/**
     * Returns the string representation for an ORDER BY
     *
     * @param string $p_sFieldID
     * @param string $p_sDirection
     * @return string
     */
	function getOrder ($p_sFieldID = "", $p_sDirection="") { return $p_sFieldID." ".$p_sDirection; }

	/**
     * Returns the string representation for a LIMIT
     *
     * @param string $p_iStart
     * @param string $p_iEnd
     * @return string
     */
	function getLimit ($p_iStart = "", $p_iEnd = "") { return "LIMIT ".$p_iStart.",".$p_iEnd; }

	/**
     * Return the last query performed
     *
     * @return string
     */
	function getLastQuery () { return $this->sLastQuery; }

	/**
     * Return the last mysql_error() generated
     *
     * @return string
     */
	function getLastError () { return $this->sLastError; }

	/**
     * return the connection handler generated from mysql_connect()
     *
     * @return PDO
     */
	function getHandler() { return $this->rHandler; }

	/**
	 * Get the primary key assigned to this Model
     *
	 * @return string
	 */
	function getPrimaryKey() {
		return $this->sTableIndex;
	}

	/**
	 * Get the table name assigned to this Model
     *
	 * @return string
	 */
	function getTableName() {
		return $this->sTableName;
	}

	/**
	 * Sanitize DB input
     *
	 * @param mixed $var
     * @return string
	 */
	function quote($var) {
		if(is_int($var) || is_float($var)) { return $var; }
		return $this->rHandler->quote($var);
	}

	/**
     * Get the config object
     *
     * @return object
     */
	function getConfig() {
		return PPI_Helper::getConfig();
	}

	/**
     * Get the registry object
     *
     * @return object
     */
	function getRegistry() {
		return PPI_Helper::getRegistry();
	}

	/**
	 * Get the session object
     *
     * @return object
	 */
	function getSession() {
		return PPI_Helper::getSession();
	}

	/**
	 * Check if a charset exists
     *
	 * @param string $charset The Charset
	 * @return boolean
	 */
	function isCharsetExists($charset) {
		return (bool) $this->query("SHOW CHARACTER SET LIKE " . $this->quote($charset))->fetch();
	}

	/**
	 * Check if a charset exists in the current database
	 *
	 * @param string $charset The Charset
	 * @return bool
	 */
	function isValidCharset($charset) {
		$oReg = $this->getRegistry();
		// If the charset we're using has been verified to be a correct charset.
		if($oReg->exists('PPI_Model::lastUsedCharset')) {
			return true;
		}
		// We have never called isValidCharset before and therefore we must
		if($this->isCharsetExists($charset)) {
			// Set in the registry
			$oReg->set('PPI_Model::lastUsedCharset', $charset);
			return true;
		}
		return false;
	}

	/**
	 * Return a systemvar from the MySQLd
	 *
	 * @param string $p_sVar Var Name
	 * @return mixed
	 */
	function getSystemVar($p_sVar) {
		$oReg = $this->getRegistry();
		$existing = $oReg->get('PPI_Model::sysvar::' . $p_sVar, false);
		if($existing === false) {
			$ret = $this->query('SELECT @@' . $p_sVar . ' sysvar', false)->fetchAll();
			if(empty($ret)) {
				throw new PPI_Exception('Invalid MySQL Server Variable: ' . $p_sVar);
			}
			$sysVar = $ret[0]['sysvar'];
			$oReg->set('PPI_Model::sysvar::' . $p_sVar, $sysVar);
			return $sysVar;
		}
		return $existing;
	}
}
