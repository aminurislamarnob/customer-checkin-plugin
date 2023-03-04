<?php
namespace IncPath\Base;

class Deactivate
{
	public static function deactivate() {
		flush_rewrite_rules();
	}
}