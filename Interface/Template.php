<?php

interface PPI_Interface_Template {

	function assign($key, $val);

	function render($p_sTemplateFile);

	function getTemplateExtension();

	function getDefaultMasterTemplate();

}