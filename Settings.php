<?php
// 2016-11-23
namespace Dfe\FacebookLogin;
// Аргумент $s для методов этого класса не нужен,
// потому что опции этого класса считывается только на витрине для текущего магазина.
/** @method static Settings s() */
class Settings extends \Df\Core\Settings {
	/**
	 * 2016-11-23
	 * @override
	 * @see \Df\Core\Settings::prefix()
	 * @used-by \Df\Core\Settings::v()
	 * @return string
	 */
	protected function prefix() {return 'dfe_facebook/login/';}
}


