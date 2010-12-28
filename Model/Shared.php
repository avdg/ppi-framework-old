<?php
/**
 *
 * @version   1.0
 * @author    Paul Dragoonis <dragoonis@php.net>
 * @license   http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright Digiflex Development
 * @package   PPI
 */

class PPI_Model_Shared extends PPI_Model {

	/**
	 * The shared model you can pass in a dynamic table name, primary key into
	 *
	 * @param string $p_sTableName The table name
	 * @param string $p_sPrimaryKey The primary key
	 */
	function __construct($p_sTableName, $p_sPrimaryKey) {
		parent::__construct($p_sTableName, $p_sPrimaryKey);
	}

}