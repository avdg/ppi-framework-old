<?php

/**
 *
 * @author    Alexey Shein <confik@gmail.com>
 * @license   http://opensource.org/licenses/mit-license.php MIT
 * @package   Core
 * @link      www.ppiframework.com
 *
 */
interface PPI_Form_Tag {

	function getAttribute($name);

	function setAttribute($name, $value);

	function render();

}
