<?php

class Mustache_Node_Comment extends Mustache_Node {

	public function __construct(array $token) {
		// nothing to do here
	}

	public function render(Mustache_Compiler $compiler) {
		return '';
	}
}
