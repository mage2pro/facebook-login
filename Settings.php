<?php
// 2016-11-23
namespace Dfe\FacebookLogin;
// Аргумент $s для методов этого класса не нужен,
// потому что опции этого класса считывается только на витрине для текущего магазина.
/** @method static Settings s() */
final class Settings extends \Df\Config\Settings {
	/**
	 * 2016-11-23
	 * @override
	 * @see \Df\Config\Settings::prefix()
	 * @used-by \Df\Config\Settings::v()
	 * @return string
	 */
	protected function prefix() {return 'dfe_facebook/login';}
}


