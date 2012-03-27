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
 * @group parser
 */
class Mustache_Test_ParserTest extends PHPUnit_Framework_TestCase {

	/**
	 * @dataProvider getTokenSets
	 */
	public function testParse($tokens, $expected)
	{
		$parser = new Mustache_Parser;
		$this->assertEquals($expected, $parser->parse('', $tokens));
	}

	public function getTokenSets()
	{
		return array(
			array(
				array(),
				$this->rootNode()
			),

			array(
				array(array(
					Mustache_Tokenizer::TYPE  => Mustache_Tokenizer::T_TEXT,
					Mustache_Tokenizer::VALUE => 'text'
				)),
				$this->rootNode(array(
					$this->textNode('text')
				)),
			),

			array(
				array(array(
					Mustache_Tokenizer::TYPE => Mustache_Tokenizer::T_ESCAPED,
					Mustache_Tokenizer::NAME => 'name'
				)),
				$this->rootNode(array(
					$this->escapedNode('name')
				)),
			),

			array(
				array(
					array(
						Mustache_Tokenizer::TYPE  => Mustache_Tokenizer::T_TEXT,
						Mustache_Tokenizer::VALUE => 'foo'
					),
					array(
						Mustache_Tokenizer::TYPE  => Mustache_Tokenizer::T_INVERTED,
						Mustache_Tokenizer::INDEX => 123,
						Mustache_Tokenizer::NAME  => 'parent'
					),
					array(
						Mustache_Tokenizer::TYPE  => Mustache_Tokenizer::T_ESCAPED,
						Mustache_Tokenizer::NAME  => 'name'
					),
					array(
						Mustache_Tokenizer::TYPE  => Mustache_Tokenizer::T_END_SECTION,
						Mustache_Tokenizer::INDEX => 456,
						Mustache_Tokenizer::NAME  => 'parent'
					),
					array(
						Mustache_Tokenizer::TYPE  => Mustache_Tokenizer::T_TEXT,
						Mustache_Tokenizer::VALUE => 'bar'
					),
				),
				$this->rootNode(array(
					$this->textNode('foo'),
					$this->invertedNode('parent', 123, 456, array(
						$this->escapedNode('name'),
					)),
					$this->textNode('bar'),
				)),
			),

		);
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testCompilerThrowsUnknownNodeTypeException() {
		$parser = new Mustache_Parser;
		$parser->parse('', array(array(Mustache_Tokenizer::TYPE => 'invalid')));
	}

	/**
	 * @dataProvider getBadParseTrees
	 * @expectedException \LogicException
	 */
	public function testParserThrowsExceptions($tokens) {
		$parser = new Mustache_Parser;
		$parser->parse('', $tokens);
	}

	public function getBadParseTrees() {
		return array(
			// no close
			array(
				array(
					array(
						Mustache_Tokenizer::TYPE  => Mustache_Tokenizer::T_SECTION,
						Mustache_Tokenizer::NAME  => 'parent',
						Mustache_Tokenizer::INDEX => 123,
						Mustache_Tokenizer::END   => 456,
						Mustache_Tokenizer::OTAG  => '{{',
						Mustache_Tokenizer::CTAG  => '}}',
					),
				),
			),

			// no close inverted
			array(
				array(
					array(
						Mustache_Tokenizer::TYPE  => Mustache_Tokenizer::T_INVERTED,
						Mustache_Tokenizer::NAME  => 'parent',
						Mustache_Tokenizer::INDEX => 123,
						Mustache_Tokenizer::END   => 456,
						Mustache_Tokenizer::OTAG  => '{{',
						Mustache_Tokenizer::CTAG  => '}}',
					),
				),
			),

			// no opening inverted
			array(
				array(
					array(
						Mustache_Tokenizer::TYPE  => Mustache_Tokenizer::T_END_SECTION,
						Mustache_Tokenizer::NAME  => 'parent',
						Mustache_Tokenizer::INDEX => 123,
						Mustache_Tokenizer::END   => 456,
						Mustache_Tokenizer::OTAG  => '{{',
						Mustache_Tokenizer::CTAG  => '}}',
					),
				),
			),

			// weird nesting
			array(
				array(
					array(
						Mustache_Tokenizer::TYPE  => Mustache_Tokenizer::T_SECTION,
						Mustache_Tokenizer::NAME  => 'parent',
						Mustache_Tokenizer::INDEX => 123,
						Mustache_Tokenizer::END   => 456,
						Mustache_Tokenizer::OTAG  => '{{',
						Mustache_Tokenizer::CTAG  => '}}',
					),
					array(
						Mustache_Tokenizer::TYPE  => Mustache_Tokenizer::T_SECTION,
						Mustache_Tokenizer::NAME  => 'child',
						Mustache_Tokenizer::INDEX => 123,
						Mustache_Tokenizer::END   => 456,
						Mustache_Tokenizer::OTAG  => '{{',
						Mustache_Tokenizer::CTAG  => '}}',
					),
					array(
						Mustache_Tokenizer::TYPE  => Mustache_Tokenizer::T_END_SECTION,
						Mustache_Tokenizer::NAME  => 'parent',
						Mustache_Tokenizer::INDEX => 123,
						Mustache_Tokenizer::END   => 456,
						Mustache_Tokenizer::OTAG  => '{{',
						Mustache_Tokenizer::CTAG  => '}}',
					),
					array(
						Mustache_Tokenizer::TYPE  => Mustache_Tokenizer::T_END_SECTION,
						Mustache_Tokenizer::NAME  => 'child',
						Mustache_Tokenizer::INDEX => 123,
						Mustache_Tokenizer::END   => 456,
						Mustache_Tokenizer::OTAG  => '{{',
						Mustache_Tokenizer::CTAG  => '}}',
					),
				),
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

	private function invertedNode($name, $index, $end, $nodes) {
		$node = new Mustache_Node_InvertedSection(array(
			Mustache_Tokenizer::NAME  => $name,
			Mustache_Tokenizer::INDEX => $index,
			Mustache_Tokenizer::END   => $end,
		));
		$node->nodes = $nodes;

		return $node;
	}
}
