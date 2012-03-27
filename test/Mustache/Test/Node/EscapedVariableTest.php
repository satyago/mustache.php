<?php

/*
 * This file is part of Mustache.php.
 *
 * (c) 2012 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * @group unit
 */
class Mustache_Test_Node_EscapedVariableTest extends PHPUnit_Framework_TestCase {
	public function testRender() {
		$compiler = new Mustache_Compiler;
		$var      = new Mustache_Node_EscapedVariable(array(
			Mustache_Tokenizer::NAME => 'foo',
		));
		$result = $var->render($compiler);
		$this->assertContains('$value = $context->find(\'foo\');', $result);
		$this->assertContains('$buffer .= htmlspecialchars($value, ENT_COMPAT, NULL);', $result);
	}

	public function testCustomEscaper() {
		$compiler = new Mustache_Test_StubCompiler(true);
		$var      = new Mustache_Node_EscapedVariable(array(
			Mustache_Tokenizer::NAME => 'foo',
		));
		$result = $var->render($compiler);
		$this->assertContains('$value = $context->find(\'foo\');', $result);
		$this->assertContains('$buffer .= call_user_func($this->mustache->getEscape(), $value);', $result);
	}
}

