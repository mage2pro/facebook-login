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
		$this->attribute(InstallSchema::F__FULL_NAME, 'Facebook User Full Name');
		$this->attribute(InstallSchema::F__URL, 'Facebook User URL');
		$this->attribute(InstallSchema::F__TOKEN_FOR_BUSINESS, 'Facebook User Token for Business');
	}

	/**
	 * 2015-10-10
	 * @param string $name
	 * @param string $label
	 * @return void
	 */
	private function attribute($name, $label) {
		/** @var int $ordering */
		static $ordering = 1000;
		df_eav()->addAttribute('customer', $name, array(
			'type' => 'static',
			'label' => $label,
			'input' => 'text',
			'sort_order' => $ordering,
			'position' => $ordering++,
			'visible' => false,
			'system' => false,
			'required' => false
		));
		/** @var int $attributeId */
		$attributeId = rm_first(rm_fetch_col('eav_attribute', 'attribute_id', 'attribute_code', $name));
		rm_conn()->insert(rm_table('customer_form_attribute'), array(
			'form_code' => 'adminhtml_customer', 'attribute_id' => $attributeId
		));
	}
}