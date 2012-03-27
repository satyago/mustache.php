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
class Mustache_Test_Node_ParentTest extends PHPUnit_Framework_TestCase {
	public function testRender() {
		$compiler = new Mustache_Compiler;
		$parent   = new Mustache_Node_Parent;
		$parent->nodes = array(new Mustache_Test_Node_StubNode, new Mustache_Test_Node_StubNode);
		$result = $parent->render($compiler);
		$this->assertEquals("<<STUB>><<STUB>>", $result);
	}
}
