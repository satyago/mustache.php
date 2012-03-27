<?php

class Mustache_Node_Partial extends Mustache_Node {

	const TEMPLATE = '
		if ($partial = $this->mustache->loadPartial(%s)) {
			$buffer .= $partial->renderInternal($context, %s);
		}
	';

	public $name;
	public $indent;

	public function __construct(array $token) {
		$this->name   = $token[Mustache_Tokenizer::NAME];
		$this->indent = isset($token[Mustache_Tokenizer::INDENT]) ? $token[Mustache_Tokenizer::INDENT] : '';
	}

	/**
	 * Generate Mustache Template partial call PHP source.
	 *
	 * @param Mustache_Compiler $compiler
	 *
	 * @return string Generated partial call PHP source code
	 */
	public function render(Mustache_Compiler $compiler) {
		return sprintf(
			$compiler->prepare(self::TEMPLATE, $compiler->level),
			var_export($this->name, true),
			var_export($this->indent, true)
		);
	}
}
