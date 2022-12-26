<?php
namespace Dfe\FacebookLogin;
# 2015-10-10
final class Exception extends \Df\Core\Exception {
	/**
	 * 2015-10-10
	 * @override
	 * @see \Df\Core\Exception::message()
	 * @used-by df_xts()
	 */
	function message():string {return "Facebook API error of type {$this['type']}: «{$this['message']}».";}
}