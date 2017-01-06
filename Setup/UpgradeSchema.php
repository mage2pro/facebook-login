<?php
namespace Dfe\FacebookLogin\Setup;
class UpgradeSchema extends \Df\Sso\Upgrade\Schema {
	/**
	 * 2016-06-04
	 * 2015-10-10
	 * В таблице eav_attribute длина кода свойства ограничивается 255 символами,
	 * однако в ядре в настоящее время есть дефект, ограничивающий длину 30 символами:
	 * https://mage2.pro/t/129
	 * Поэтому приходиться укладываться в 30.
	 * @override
	 * @used-by \Df\Sso\Upgrade\Schema::_process()
	 * @return string
	 */
	public static function fId() {return 'dfe_fb__token_for_business';}

	/**
	 * 2016-06-05
	 * @override
	 * @see \Df\Sso\Upgrade\Schema::_process()
	 * @used-by \Df\Framework\Upgrade::process()
	 * @return void
	 */
	protected function _process() {
		parent::_process();
		if ($this->isInitial()) {
			// 2015-10-10
			// Не хочу проблем из-за идиотов с длинными именами, поэтому пусть будет 255.
			$this->column(self::F__FULL_NAME, 'varchar(255) DEFAULT NULL');
			// Адрес пустой картинки. У меня — 172 символа:
			// https://scontent.xx.fbcdn.net/hprofile-xfp1/v/t1.0-1/c15.0.50.50/p50x50/10354686_10150004552801856_220367501106153455_n.jpg?oh=17835c9c962c70d05cc25d75008438a3&oe=5698842F
			/**
			 * 2016-08-22
			 * Помимо добавления поля в таблицу «customer_entity» надо ещё добавить атрибут
			 * что мы делаем в методе @see \Dfe\FacebookLogin\Setup\UpgradeData::_install()
			 * иначе данные не будут сохраняться:
			 * https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Eav/Model/Entity/AbstractEntity.php#L1262-L1265
			 */
			$this->column(self::F__PICTURE, 'varchar(255) DEFAULT NULL');
			// В настоящее время мне Facebook возващает токены длиной 185 символов.
			// Места отвёл с запасом.
			$this->column(self::F__LONG_LIVED_ACCESS_TOKEN, 'varchar(255) DEFAULT NULL');
		}
	}

	/**
	 * 2015-10-10
	 * «The person's full name»
	 * https://developers.facebook.com/docs/graph-api/reference/user
	 * @used-by _install()
	 * @used-by \Dfe\FacebookLogin\Setup\UpgradeData::_install()
	 */
	const F__FULL_NAME = 'dfe_fb__full_name';
	/**
	 * 2015-10-10
	 * « long-lived tokens usually have a lifetime of about 60 days»
	 * https://developers.facebook.com/docs/facebook-login/access-tokens#termtokens
	 * https://developers.facebook.com/docs/facebook-login/access-tokens#extending
	 */
	const F__LONG_LIVED_ACCESS_TOKEN = 'dfe_fb__access_token';
	/**
	 * 2015-10-10
	 * «A Picture for a Facebook User.»
	 * https://developers.facebook.com/docs/graph-api/reference/user/picture/
	 */
	const F__PICTURE = 'dfe_fb__picture';
	/**
	 * 2015-10-10
	 * Обратите внимание, что получить адрес страницы пользователя
	 * мы в 2015 году уже не можем: http://stackoverflow.com/questions/29152500
	 * «link» возвращает адрес типа
	 * https://www.facebook.com/app_scoped_user_id/10206714043186313/
	 * толку нам от него мало.
	 */
}