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
class Mustache_Test_Node_SectionTest extends PHPUnit_Framework_TestCase {
	public function testRender() {
		$compiler = new Mustache_Compiler;
		$section  = new Mustache_Node_Section(array(
			Mustache_Tokenizer::NAME  => 'foo',
			Mustache_Tokenizer::INDEX => 1,
			Mustache_Tokenizer::END   => 2,
			Mustache_Tokenizer::OTAG  => '[[',
			Mustache_Tokenizer::CTAG  => ']]',
			Mustache_Tokenizer::NODES => array(new Mustache_Test_Node_StubNode),
		));

		$section->source = 'SOURCED!';

		$result = $section->render($compiler);
		$this->assertRegExp('/\$buffer .= \$this->section\w+\(\$context, \$indent, \$context->find\(\'foo\'\)\);/', $result);

		$matches = array();
		preg_match('/\$this->section(\w+)/', $result, $matches);
		$key = $matches[1];

		$methods = $compiler->getMethods();
		$this->assertArrayHasKey($key, $methods);

		$code = $methods[$key];

		$this->assertContains('if (!is_string($value) && is_callable($value))', $code);
		$this->assertContains('$source = \'SOURCED!\'', $code);
		$this->assertContains('->loadLambda((string) call_user_func($value, $source), \'{{= [[ ]] =}}\')', $code);
		$this->assertContains('$context->push($value)', $code);
		$this->assertContains('<<STUB>>', $code);
		$this->assertContains('$context->pop()', $code);
	}
}
