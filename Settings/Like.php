<?php
namespace Dfe\Facebook\Settings;
class Like extends \Df\Core\Settings {
	/** @return bool */
	public function enabledOnProductPage() {return !!$this->v('product');}

	/**
	 * @override
	 * @used-by \Df\Core\Settings::v()
	 * @return string
	 */
	protected function prefix() {return 'dfe_facebook/like/';}

	/** @return $this */
	public static function s() {static $r; return $r ? $r : $r = df_o(__CLASS__);}
}