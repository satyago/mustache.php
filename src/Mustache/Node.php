<?php

abstract class Mustache_Node {
	abstract public function render(Mustache_Compiler $compiler);

	/**
	 * Select the appropriate Context `find` method for a given $name.
	 *
	 * The return value will be one of `find`, `findDot` or `last`.
	 *
	 * @see Mustache_Context::find
	 * @see Mustache_Context::findDot
	 * @see Mustache_Context::last
	 *
	 * @param string $name Variable name
	 *
	 * @return string `find` method name
	 */
	protected function getFindMethod($name) {
		if ($name === '.') {
			return 'last';
		} elseif (strpos($name, '.') === false) {
			return 'find';
		} else {
			return 'findDot';
		}
	}
}
