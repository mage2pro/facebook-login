<?php
# 2016-11-23
namespace Dfe\FacebookLogin;
# Аргумент $s для методов этого класса не нужен,
# потому что опции этого класса считывается только на витрине для текущего магазина.
/** @method static Settings s() */
final class Settings extends \Df\Sso\Settings {
	/**
	 * 2016-11-23
	 * @override
	 * @see \Df\Config\Settings::prefix()
	 * @used-by \Df\Config\Settings::v()
	 */
	protected function prefix():string {return 'df_facebook/login';}
}


