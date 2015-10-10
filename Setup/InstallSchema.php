<?php
namespace Dfe\Facebook\Setup;
use \Magento\Framework\DB\Adapter;
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
		$table = rm_table('customer_entity');
		// 2015-10-10
		// Не хочу проблем из-за идиотов с длинными именами, поэтому пусть будет 255.
		$conn->addColumn($table, self::F__FULL_NAME, 'varchar(255) DEFAULT NULL');
		// 2015-10-10
		// Для меня Facebook вернул token_for_business из 16 символов,
		// так что 25 вроде бы нормально.
		$conn->addColumn($table, self::F__TOKEN_FOR_BUSINESS, 'varchar(25) DEFAULT NULL');
		// 2015-10-10
		// Не хочу проблем из-за идиотов с длинными адресами страниц, поэтому пусть будет 255.
		$conn->addColumn($table, self::F__URL, 'varchar(255) DEFAULT NULL');
		$setup->endSetup();
	}

	/**
	 * @used-by \Dfe\Facebook\Setup\InstallSchema::install()
	 * @used-by \Dfe\Facebook\Setup\InstallData::install()
	 * 2015-10-10
	 * «The person's full name»
	 * https://developers.facebook.com/docs/graph-api/reference/user
	 */
	const F__FULL_NAME = 'dfe_fb__full_name';
	/**
	 * @used-by \Dfe\Facebook\Setup\InstallSchema::install()
	 * @used-by \Dfe\Facebook\Setup\InstallData::install()
	 * 2015-10-10
	 * В таблице eav_attribute длина кода свойства ограничивается 255 символами,
	 * однако в ядре в настоящее время есть дефект, ограничивающий длину 30 символами:
	 * https://mage2.pro/t/129
	 * Поэтому приходиться укладываться в 30.
	 */
	const F__TOKEN_FOR_BUSINESS = 'dfe_fb__token_for_business';
	/**
	 * @used-by \Dfe\Facebook\Setup\InstallSchema::install()
	 * @used-by \Dfe\Facebook\Setup\InstallData::install()
	 * 2015-10-10
	 * «A link to the person's Timeline»
	 * https://developers.facebook.com/docs/graph-api/reference/user
	 */
	const F__URL = 'dfe_fb__url';
}