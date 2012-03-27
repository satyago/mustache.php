<?php

class Mustache_Node_Text extends Mustache_Node {

	const TEMPLATE      = '$buffer .= %s%s;';
	const LINE_TEMPLATE = '$buffer .= "\n";';

	public $value;

	public function __construct(array $token) {
		$this->value = $token[Mustache_Tokenizer::VALUE];
	}

	/**
	 * Generate Mustache Template output Buffer call PHP source.
	 *
	 * @param Mustache_Compiler $compiler
	 *
	 * @return string Generated output Buffer call PHP source
	 */
	public function render(Mustache_Compiler $compiler) {
		if ($this->value === "\n") {
			$compiler->indentNextLine = true;

			return $compiler->prepare(self::LINE_TEMPLATE, $compiler->level);
		} else {
			return sprintf(
				$compiler->prepare(self::TEMPLATE, $compiler->level),
				$compiler->flushIndent(),
				var_export($this->value, true)
			);
		}
	}
}
