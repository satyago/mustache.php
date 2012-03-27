<?php

class Mustache_Node_InvertedSection extends Mustache_Node_Parent {

	const TEMPLATE = '
		// %s inverted section
		$value = $context->%s(%s);
		if (empty($value)) {
			%s
		}';

	public $name;

	public function __construct(array $token) {
		$this->name  = $token[Mustache_Tokenizer::NAME];

		if (isset($token[Mustache_Tokenizer::NODES])) {
			$this->nodes = $token[Mustache_Tokenizer::NODES];
		}
	}

	public function render(Mustache_Compiler $compiler) {
		return sprintf(
			$compiler->prepare(self::TEMPLATE, $compiler->level),
			var_export($this->name, true),
			$this->getFindMethod($this->name),
			var_export($this->name, true),
			$this->walk($compiler)
		);
	}
}
