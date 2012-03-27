<?php

/*
 * This file is part of Mustache.php.
 *
 * (c) 2012 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Mustache_Test_Node_StubNode extends Mustache_Node {
	public function render(Mustache_Compiler $compiler) {
		return '<<STUB>>';
	}
}
