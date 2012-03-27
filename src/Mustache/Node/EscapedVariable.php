<?php

class Mustache_Node_EscapedVariable extends Mustache_Node_Variable {

	const DEFAULT_ESCAPE_TEMPLATE = 'htmlspecialchars(%s, ENT_COMPAT, %s)';
	const CUSTOM_ESCAPE_TEMPLATE  = 'call_user_func($this->mustache->getEscape(), %s)';

	/**
	 * Get the current escaper.
	 *
	 * @return string Either a custom callback, or an inline call to `htmlspecialchars`
	 */
	protected function getValue(Mustache_Compiler $compiler) {
		if ($compiler->getCustomEscape()) {
			return sprintf(self::CUSTOM_ESCAPE_TEMPLATE, '$value');
		} else {
			return sprintf(self::DEFAULT_ESCAPE_TEMPLATE, '$value', var_export($compiler->getCharset(), true));
		}
	}
}
