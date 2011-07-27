<?php

class PPI_Form_HtmlTest extends PHPUnit_Framework_TestCase {

	function testCreateFormSetsActionAttribute() {
		$form = $this->createForm();
		$this->assertEquals('index.php', $form->getAttribute('action'));
	}

	/**
	 * @expectedException PPI_Exception
	 */
	function testCreatingFormWithEmptyActionAttributeThrowsException() {
		$form = $this->createForm('');
	}

	function testCreateFormWithTwoParametersSetsMethod() {
		$form = $this->createFormWithMethod('post');
		$this->assertEquals('index.php', $form->getAttribute('action'));
		$this->assertEquals('post', $form->getAttribute('method'));
	}

	function testRenderShouldRenderFormTagWithAttributes() {
		$form = $this->createForm();
		$this->assertEquals('<form action="index.php"></form>', $form->render());
	}

	function testToStringCallsRender() {
		$form = $this->createForm();
		$expected = '<form action="index.php"></form>';
		$this->assertEquals($expected, $form->render());
		$this->assertEquals($expected, (string) $form);
	}

	function testSetAttributesAreRendered() {
		$form = $this->createForm();
		$form->setAttribute('name', 'myform');
		$expected = '<form action="index.php" name="myform"></form>';
		$this->assertEquals($expected, $form->render());
	}

	function testEmptyAttributesArentRendered() {
		$form = $this->createFormWithMethod('');
		$form->setAttribute('class', '');
		$expected = '<form action="index.php"></form>';
		$this->assertEquals($expected, $form->render());
	}

	function testRenderedAttributesAreProperlyEscaped() {
		$form = $this->createForm('Some attributes may \'">< contain garbage');
		$expected = '<form action="Some attributes may &#039;&quot;&gt;&lt; contain garbage"></form>';
		$this->assertEquals($expected, $form->render());

	}

	private function createFormWithMethod($method, $action = 'index.php') {
		return new PPI_Form_Html($action, $method);
	}

	private function createForm($action = 'index.php') {
		return new PPI_Form_Html($action);
	}
}

