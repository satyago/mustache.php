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
class Mustache_Test_Node_InvertedSectionTest extends PHPUnit_Framework_TestCase {
	public function testRender() {
		$compiler = new Mustache_Compiler;
		$section  = new Mustache_Node_InvertedSection(array(
			Mustache_Tokenizer::NAME => 'foo',
			Mustache_Tokenizer::NODES => array(new Mustache_Test_Node_StubNode),
		));
		$result = $section->render($compiler);
		$this->assertContains('$value = $context->find(\'foo\');', $result);
		$this->assertContains('if (empty($value))', $result);
		$this->assertContains('<<STUB>>', $result);
		$this->assertNotContains('htmlspecialchars', $result);
	}
}
