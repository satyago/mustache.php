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
 * Mustache Parser class.
 *
 * This class is responsible for turning a set of Mustache tokens into a parse tree.
 */
class Mustache_Parser {

	/**
	 * Process an array of Mustache tokens and convert them into a parse tree.
	 *
	 * @param array $tokens Set of Mustache tokens
	 *
	 * @return array Mustache token parse tree
	 */
	public function parse($source, array $tokens = array()) {
		$this->source = $source;

		$tree = new Mustache_Node_Root;
		$tree->nodes = $this->buildTree(new ArrayIterator($tokens));

		return $tree;
	}

	/**
	 * Helper method for recursively building a parse tree.
	 *
	 * @throws LogicException when nesting errors or mismatched section tags are encountered.
	 *
	 * @param ArrayIterator $tokens Stream of Mustache tokens
	 * @param array          $parent Parent token (default: null)
	 *
	 * @return array Mustache Token parse tree
	 */
	private function buildTree(ArrayIterator $tokens, Mustache_Node_Parent $parent = null) {
		$nodes = array();

		do {
			$token = $tokens->current();
			$tokens->next();

			if ($token === null) {
				continue;
			} else {
				switch ($token[Mustache_Tokenizer::TYPE]) {
					case Mustache_Tokenizer::T_SECTION:
						$nodes[] = $this->buildTree($tokens, new Mustache_Node_Section($token));
						break;

					case Mustache_Tokenizer::T_INVERTED:
						$nodes[] = $this->buildTree($tokens, new Mustache_Node_InvertedSection($token));
						break;

					case Mustache_Tokenizer::T_END_SECTION:
						if (!isset($parent)) {
							throw new LogicException('Unexpected closing tag: /'. $token[Mustache_Tokenizer::NAME]);
						}

						if ($token[Mustache_Tokenizer::NAME] !== $parent->name) {
							throw new LogicException('Nesting error: ' . $parent->name . ' vs. ' . $token[Mustache_Tokenizer::NAME]);
						}

						if ($parent instanceof Mustache_Node_Section) {
							$parent->end    = $token[Mustache_Tokenizer::INDEX];
							$parent->source = substr($this->source, $parent->index, ($parent->end - $parent->index));
						}

						$parent->nodes = $nodes;

						return $parent;
						break;

					case Mustache_Tokenizer::T_PARTIAL:
					case Mustache_Tokenizer::T_PARTIAL_2:
						$nodes[] = new Mustache_Node_Partial($token);
						break;

					case Mustache_Tokenizer::T_UNESCAPED:
					case Mustache_Tokenizer::T_UNESCAPED_2:
						$nodes[] = new Mustache_Node_Variable($token);
						break;

					case Mustache_Tokenizer::T_COMMENT:
						$nodes[] = new Mustache_Node_Comment($token);
						break;

					case Mustache_Tokenizer::T_ESCAPED:
						$nodes[] = new Mustache_Node_EscapedVariable($token);
						break;

					case Mustache_Tokenizer::T_TEXT:
						$nodes[] = new Mustache_Node_Text($token);
						break;

					default:
						throw new InvalidArgumentException('Unknown token type: '.json_encode($token));
						break;
				}
			}

		} while ($tokens->valid());

		if (isset($parent)) {
			throw new LogicException('Missing closing tag: ' . $parent->name);
		}

		return $nodes;
	}
}
