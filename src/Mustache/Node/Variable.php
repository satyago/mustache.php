<?php

class Mustache_Node_Variable extends Mustache_Node {

	const TEMPLATE = '
		$value = $context->%s(%s);
		if (!is_string($value) && is_callable($value)) {
			$value = $this->mustache
				->loadLambda((string) call_user_func($value))
				->renderInternal($context, $indent);
		}
		$buffer .= %s%s;
	';

	public $name;

	public function __construct(array $token) {
		$this->name = $token[Mustache_Tokenizer::NAME];
	}

	/**
	 * Generate Mustache Template variable interpolation PHP source.
	 *
	 * @param string  $id     Variable name
	 * @param boolean $escape Escape the variable value for output?
	 * @param int     $level
	 *
	 * @return string Generated variable interpolation PHP source
	 */
	public function render(Mustache_Compiler $compiler) {
		$method = $this->getFindMethod($this->name);

		return sprintf(
			$compiler->prepare(self::TEMPLATE, $compiler->level),
			$method,
			($method !== 'last') ? var_export($this->name, true) : '',
			$compiler->flushIndent(),
			$this->getValue($compiler)
		);
	}

	protected function getValue(Mustache_Compiler $compiler) {
		return '$value';
	}
}
