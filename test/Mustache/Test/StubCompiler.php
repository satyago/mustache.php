<?php

/*
 * This file is part of Mustache.php.
 *
 * (c) 2012 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Mustache_Test_StubCompiler extends Mustache_Compiler {
	private $customEscape;
	public function __construct($customEscape = false) {
		$this->customEscape = $customEscape;
	}
	public function getCustomEscape() {
		return $this->customEscape;
	}
}
