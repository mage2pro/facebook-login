<?php
namespace Dfe\FacebookLogin;
# 2015-10-10
/** @used-by \Dfe\FacebookLogin\Customer::responseJson() */
final class ResponseValidator extends \Df\API\Response\Validator {
	/**
	 * 2015-10-10
	 * @override
	 * @see \Df\Core\Exception::message()
	 * @used-by df_xts()
	 */
	function message():string {return "Facebook API error of type {$this['type']}: «{$this['message']}».";}
}