<?php
namespace Dfe\Facebook\Setup;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
class InstallData implements InstallDataInterface {
	/**
	 * 2015-10-06
	 * @override
	 * @see InstallDataInterface::install()
	 * @param ModuleDataSetupInterface $setup
	 * @param ModuleContextInterface $context
	 * @return void
	 */
	public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context) {
		df_eav()->addAttribute('customer', InstallSchema::F__FACEBOOK_ID, array(
			'type' => 'static',
			'label' => 'Facebook ID',
			'input' => 'text',
			'sort_order' => 1000,
			'position' => 1000,
			'visible' => false,
			'system' => false,
			'required' => false
		));
		/** @var int $attributeId */
		$attributeId = rm_first(rm_fetch_col(
			'eav_attribute', 'attribute_id', 'attribute_code', InstallSchema::F__FACEBOOK_ID
		));
		rm_conn()->insert(rm_table('customer_form_attribute'), array(
			'form_code' => 'adminhtml_customer', 'attribute_id' => $attributeId
		));
	}
}