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
 * Mustache Compiler class.
 *
 * This class is responsible for turning a Mustache token parse tree into normal PHP source code.
 */
class Mustache_Compiler {

	const INDENT_TEMPLATE = '$indent . ';

	public  $level;
	public  $indentNextLine;

	private $methods;
	private $source;
	private $customEscape;
	private $charset;

	/**
	 * Compile a Mustache token parse tree into PHP source code.
	 *
	 * @param string $source Mustache Template source code
	 * @param string $tree   Parse tree of Mustache tokens
	 * @param string $name   Mustache Template class name
	 *
	 * @return string Generated PHP source code
	 */
	public function compile($source, Mustache_Node_Root $tree, $name, $customEscape = false, $charset = 'UTF-8') {
		$this->methods        = array();
		$this->source         = $source;
		$this->level          = 0;
		$this->indentNextLine = true;
		$this->customEscape   = $customEscape;
		$this->charset        = $charset;

        $tree->name = $name;
        return $tree->render($this);
	}

	public function getCharset() {
		return $this->charset;
	}

	public function getCustomEscape() {
		return $this->customEscape;
	}

	public function addMethod($name, $source) {
		$this->methods[$name] = $source;
	}

    public function getMethods() {
        return $this->methods;
    }

	/**
	 * Get the current $indent prefix to write to the buffer.
	 *
	 * @return string "$indent . " or ""
	 */
	public function flushIndent() {
		if ($this->indentNextLine) {
			$this->indentNextLine = false;

			return self::INDENT_TEMPLATE;
		} else {
			return '';
		}
	}



    /**
     * Prepare PHP source code snippet for output.
     *
     * @param string  $text
     * @param int     $bonus          Additional indent level (default: 0)
     * @param boolean $prependNewline Prepend a newline to the snippet? (default: true)
     *
     * @return string PHP source code snippet
     */
    public function prepare($text, $bonus = 0, $prependNewline = true) {
        $text = ($prependNewline ? "\n" : '').trim($text);
        if ($prependNewline) {
            $bonus++;
        }

        return preg_replace("/\n(\t\t| {8})?/", "\n".str_repeat("\t", $bonus), $text);
    }

}
