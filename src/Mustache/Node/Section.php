<?php

class Mustache_Node_Section extends Mustache_Node_Parent {

	const TEMPLATE = '
		// %s section
		$buffer .= $this->section%s($context, $indent, $context->%s(%s));
	';

	const FUNCTION_TEMPLATE = '
		private function section%s(Mustache_Context $context, $indent, $value) {
			$buffer = \'\';
			if (!is_string($value) && is_callable($value)) {
				$source = %s;
				$buffer .= $this->mustache
					->loadLambda((string) call_user_func($value, $source)%s)
					->renderInternal($context, $indent);
			} elseif (!empty($value)) {
				$values = $this->isIterable($value) ? $value : array($value);
				foreach ($values as $value) {
					$context->push($value);%s
					$context->pop();
				}
			}

			return $buffer;
		}';

	public $name;
	public $index;
	public $end;
	public $otag;
	public $ctag;
	public $source;
	public $nodes;

	public function __construct(array $token) {
		$this->name   = $token[Mustache_Tokenizer::NAME];
		$this->index  = $token[Mustache_Tokenizer::INDEX];
		$this->otag   = $token[Mustache_Tokenizer::OTAG];
		$this->ctag   = $token[Mustache_Tokenizer::CTAG];

		if (isset($token[Mustache_Tokenizer::NODES])) {
			$this->nodes = $token[Mustache_Tokenizer::NODES];
		}
	}

	/**
	 * Generate Mustache Template section PHP source.
	 *
	 * @param Mustache_Compiler $compiler
	 *
	 * @return string Generated section PHP source code
	 */
	public function render(Mustache_Compiler $compiler) {
		if ($this->otag !== '{{' || $this->ctag !== '}}') {
			$delims = ', '.var_export(sprintf('{{= %s %s =}}', $this->otag, $this->ctag), true);
		} else {
			$delims = '';
		}

		$key = $this->getKey();
		$compiler->addMethod(
			$key,
			sprintf(
				$compiler->prepare(self::FUNCTION_TEMPLATE),
				$key,
				var_export($this->source, true),
				$delims,
				$this->walk($compiler)
			)
		);

		return sprintf(
			$compiler->prepare(self::TEMPLATE, $compiler->level),
			var_export($this->name, true),
			$key,
			$this->getFindMethod($this->name),
			var_export($this->name, true)
		);
	}

	protected function walk(Mustache_Compiler $compiler) {
		$compiler->level++;
		$code = parent::walk($compiler);
		$compiler->level--;

		return $code;
	}

	private function getKey() {
		return ucfirst(md5(sprintf("%s\n%s\n%s", $this->otag, $this->ctag, $this->source)));
	}
}
