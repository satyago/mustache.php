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
class Mustache_Test_Node_PartialTest extends PHPUnit_Framework_TestCase {
    public function testRender() {
        $compiler = new Mustache_Compiler;
        $partial  = new Mustache_Node_Partial(array(
            Mustache_Tokenizer::NAME => 'foo'
        ));
        $result = $partial->render($compiler);
        $this->assertContains('if ($partial = $this->mustache->loadPartial(\'foo\'))', $result);
        $this->assertContains('$buffer .= $partial->renderInternal($context, \'\')', $result);
    }

	public function testRenderWithIndent() {
		$compiler = new Mustache_Compiler;
		$partial  = new Mustache_Node_Partial(array(
			Mustache_Tokenizer::NAME => 'foo',
            Mustache_Tokenizer::INDENT => 'banana',
		));
		$result = $partial->render($compiler);
		$this->assertContains('if ($partial = $this->mustache->loadPartial(\'foo\'))', $result);
		$this->assertContains('$buffer .= $partial->renderInternal($context, \'banana\')', $result);
	}
}
