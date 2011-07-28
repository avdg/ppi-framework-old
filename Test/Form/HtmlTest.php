<?php

class PPI_Form_HtmlTest extends PHPUnit_Framework_TestCase {

	function testCreateFormSetsActionAttribute() {
		$form = $this->createForm();
		$this->assertEquals('index.php', $form->attr('action'));
	}

	function testCreatingFormWithNoParametersCreatesFormWithEmptyAction() {
		$form = new PPI_Form_Tag_Form();
		$this->assertEquals('<form action=""></form>', $form->render());
	}

	function testCreateFormWithTwoParametersSetsMethod() {
		$form = $this->createFormWithMethod('post');
		$this->assertEquals('index.php', $form->attr('action'));
		$this->assertEquals('post', $form->attr('method'));
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
		$form->attr('name', 'myform');
		$expected = '<form action="index.php" name="myform"></form>';
		$this->assertEquals($expected, $form->render());
	}

	function testEmptyAttributesShouldBeRendered() {
		$form = $this->createForm();
		$form->attr('class', '');
		$expected = '<form action="index.php" class=""></form>';
		$this->assertEquals($expected, $form->render());
	}

	function testRenderedAttributesAreProperlyEscaped() {
		$form = $this->createForm('Some attributes may \'">< contain garbage');
		$expected = '<form action="Some attributes may &#039;&quot;&gt;&lt; contain garbage"></form>';
		$this->assertEquals($expected, $form->render());

	}

	private function createFormWithMethod($method, $action = 'index.php') {
		return new PPI_Form_Tag_Form(array('action' => $action, 'method' => $method));
	}

	private function createForm($action = 'index.php') {
		return new PPI_Form_Tag_Form(array('action' => $action));
	}
}

