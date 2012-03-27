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
 * @group unit
 */
class Mustache_Test_Node_CommentTest extends PHPUnit_Framework_TestCase {
	public function testRender() {
		$compiler = new Mustache_Compiler;
		$comment  = new Mustache_Node_Comment(array());
		$this->assertEquals('', $comment->render($compiler));
	}
}
