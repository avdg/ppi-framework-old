<?php

/**
 *
 * @version   1.0
 * @author    Paul Dragoonis <dragoonis@php.net>
 * @license   http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright Digiflex Development
 * @package   PPI
 * @subpackage core
 */

class PPI_Model_Form_Rule extends PPI_Model {
	
	public $_name = 'ppi_fb_rule';
	private $_primary = 'id';	
	private $_formName = '';
	private $_formID;
	private $_formFields = array();
	
	function __construct($p_sFormName) {
		$this->_formName = $p_sFormName;
		parent::__construct($this->_name, $this->_primary);
	}
	
	function getRuleTypes() {
		$select 		= $this->select()->from('ppi_fb_rule_type');
		$aRuleTypes 	= $select->getList($select);
		$aRules		= array();
		foreach($aRuleTypes as $aRule) {
			$aRules[$aRule['id']] = array('name' => $aRule['name'], 'message' => $aRule['default_error_message']);
		}
		return (count($aRuleTypes) > 0) ? $aRules : array();
	}	
	
}	