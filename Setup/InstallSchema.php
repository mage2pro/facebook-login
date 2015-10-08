<?php
namespace Dfe\Facebook\Setup;
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
		$setup->getConnection()->addColumn(rm_table('customer_entity'),
			self::F__FACEBOOK_ID, 'varchar(25) DEFAULT NULL'
		);
		$setup->endSetup();
	}

	/**
	 * @used-by \Dfe\Facebook\Setup\InstallSchema::install()
	 * @used-by \Dfe\Facebook\Setup\InstallData::install()
	 */
	const F__FACEBOOK_ID = 'df_facebook_id';
}