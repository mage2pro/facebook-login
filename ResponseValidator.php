<?php
namespace Dfe\FacebookLogin;
# 2015-10-10
/** @used-by \Dfe\FacebookLogin\Customer::responseJson() */
final class ResponseValidator extends \Df\API\Response\Validator {
	/**
	 * 2015-10-10
	 * @override
	 * @see \Df\API\Exception::short()
	 * @used-by \Df\API\Client::_p()
	 * @used-by \Df\API\Exception::message()
	 */
	function short():string {
		$e = $this->r(); /** @var array(string => string) $e */
		return "Facebook API error of type {$e['type']}: «{$e['message']}».";
	}

	/**
	 * 2024-05-22 "Remove `Df\Core\Exception::$_data`": https://github.com/mage2pro/core/issues/385
	 * @override
	 * @see \Df\API\Response\Validator::valid()
	 * @used-by \Df\API\Client::_p()
	 */
	function valid():bool {return !$this->r();}
}