<?php

class Mustache_Node_Parent extends Mustache_Node {
	public $nodes = array();

	public function render(Mustache_Compiler $compiler) {
		return $this->walk($compiler);
	}

	protected function walk(Mustache_Compiler $compiler) {
		$code = '';
		$compiler->level++;
		foreach ($this->nodes as $node) {
			$code .= $node->render($compiler);
		}
		$compiler->level--;

		return $code;
	}
}
