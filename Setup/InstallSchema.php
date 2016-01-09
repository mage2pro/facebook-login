<?php
namespace Dfe\FacebookLogin\Setup;
use Magento\Framework\DB\Adapter;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
class InstallSchema implements InstallSchemaInterface {
	/**
	 * 2015-10-06
	 * @override
	 * @see InstallSchemaInterface::install()
	 * @param SchemaSetupInterface $setup
	 * @param ModuleContextInterface $context
	 * @return void
	 */
	public function install(SchemaSetupInterface $setup, ModuleContextInterface $context) {
		$setup->startSetup();
		/** @var Adapter\Pdo\Mysql|Adapter\AdapterInterface $conn */
		$conn = $setup->getConnection();
		/** @var string $table */
		$table = df_table('customer_entity');
		// 2015-10-10
		// Не хочу проблем из-за идиотов с длинными именами, поэтому пусть будет 255.
		$conn->addColumn($table, self::F__FULL_NAME, 'varchar(255) DEFAULT NULL');
		/**
		 * Адрес пустой картинки у меня 172 символа:
		 * https://scontent.xx.fbcdn.net/hprofile-xfp1/v/t1.0-1/c15.0.50.50/p50x50/10354686_10150004552801856_220367501106153455_n.jpg?oh=17835c9c962c70d05cc25d75008438a3&oe=5698842F
		 */
		$conn->addColumn($table, self::F__PICTURE, 'varchar(255) DEFAULT NULL');
		// В настоящее время мне Facebook возващает токены длиной 185 символов.
		// Места отвёл с запасом.
		$conn->addColumn($table, self::F__LONG_LIVED_ACCESS_TOKEN, 'varchar(255) DEFAULT NULL');
		// 2015-10-10
		// Для меня Facebook вернул token_for_business из 16 символов,
		// так что 25 вроде бы нормально.
		$conn->addColumn($table, self::F__TOKEN_FOR_BUSINESS, 'varchar(25) DEFAULT NULL');
		$setup->endSetup();
	}

	/**
	 * 2015-10-10
	 * «The person's full name»
	 * https://developers.facebook.com/docs/graph-api/reference/user
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
	 * В таблице eav_attribute длина кода свойства ограничивается 255 символами,
	 * однако в ядре в настоящее время есть дефект, ограничивающий длину 30 символами:
	 * https://mage2.pro/t/129
	 * Поэтому приходиться укладываться в 30.
	 */
	const F__TOKEN_FOR_BUSINESS = 'dfe_fb__token_for_business';
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