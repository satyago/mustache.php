<?php

class Mustache_Node_Root extends Mustache_Node_Parent {
	const TEMPLATE = '<?php

		class %s extends Mustache_Template {
			public function renderInternal(Mustache_Context $context, $indent = \'\', $escape = false) {
				$buffer = \'\';
		%s

				if ($escape) {
					return %s;
				} else {
					return $buffer;
				}
			}
		%s
		}';

	const ESCAPE_TEMPLATE        = 'htmlspecialchars(%s, ENT_COMPAT, %s)';
	const CUSTOM_ESCAPE_TEMPLATE = 'call_user_func($this->mustache->getEscape(), %s)';

	public $name;

	/**
	 * Generate Mustache Template class PHP source.
	 *
	 * @param array  $tree Parse tree of Mustache tokens
	 * @param string $name Mustache Template class name
	 *
	 * @return string Generated PHP source code
	 */
	public function render(Mustache_Compiler $compiler) {
		$code    = $this->walk($compiler);
		$methods = implode("\n", $compiler->getMethods());

		return sprintf(
			$compiler->prepare(self::TEMPLATE, 0, false),
			$this->name,
			$code,
			$this->getEscapedBuffer($compiler),
			$methods
		);
	}

	private function getEscapedBuffer(Mustache_Compiler $compiler) {
		if ($compiler->getCustomEscape()) {
			return sprintf(self::CUSTOM_ESCAPE_TEMPLATE, '$buffer');
		} else {
			return sprintf(self::ESCAPE_TEMPLATE, '$buffer', var_export($compiler->getCharset(), true));
		}
	}}
