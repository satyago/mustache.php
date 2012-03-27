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
 * @group compiler
 */
class Mustache_Test_CompilerTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @dataProvider getCompileValues
	 */
	public function testCompile($source, Mustache_Node_Root $tree, $name, $customEscaper, $charset, $expected) {
		$compiler = new Mustache_Compiler;

		$compiled = $compiler->compile($source, $tree, $name, $customEscaper, $charset);
		foreach ($expected as $contains) {
			$this->assertContains($contains, $compiled);
		}
	}

	public function getCompileValues() {
		return array(
			array('', $this->rootNode(), 'Banana', false, 'ISO-8859-1', array(
				"\nclass Banana extends Mustache_Template",
				'return htmlspecialchars($buffer, ENT_COMPAT, \'ISO-8859-1\');',
				'return $buffer;',
			)),

			array(
				'',
				$this->rootNode(array($this->textNode('TEXT'))),
				'Monkey',
				false,
				'UTF-8',
				array(
					"\nclass Monkey extends Mustache_Template",
					'return htmlspecialchars($buffer, ENT_COMPAT, \'UTF-8\');',
					'$buffer .= $indent . \'TEXT\';',
					'return $buffer;',
				)
			),

			array(
				'',
				$this->rootNode(array($this->textNode('TEXT'))),
				'Monkey',
				true,
				'ISO-8859-1',
				array(
					"\nclass Monkey extends Mustache_Template",
					'$buffer .= $indent . \'TEXT\';',
					'return call_user_func($this->mustache->getEscape(), $buffer);',
					'return $buffer;',
				)
			),

			array(
				'',
				$this->rootNode(array(
					$this->textNode('foo'),
					$this->textNode("\n"),
					$this->escapedNode('name'),
					$this->escapedNode('.'),
					$this->textNode("'bar'"),
				)),
				'Monkey',
				false,
				'UTF-8',
				array(
					"\nclass Monkey extends Mustache_Template",
					'$buffer .= $indent . \'foo\'',
					'$buffer .= "\n"',
					'$value = $context->find(\'name\');',
					'$buffer .= htmlspecialchars($value, ENT_COMPAT, \'UTF-8\');',
					'$value = $context->last();',
					'$buffer .= \'\\\'bar\\\'\';',
					'return htmlspecialchars($buffer, ENT_COMPAT, \'UTF-8\');',
					'return $buffer;',
				)
			),
		);
	}

	private function textNode($value) {
		return new Mustache_Node_Text(array(Mustache_Tokenizer::VALUE => $value));
	}

	private function escapedNode($name) {
		return new Mustache_Node_EscapedVariable(array(Mustache_Tokenizer::NAME => $name));
	}

	private function rootNode($nodes = array()) {
		$node = new Mustache_Node_Root;
		$node->nodes = $nodes;

		return $node;
	}
}
