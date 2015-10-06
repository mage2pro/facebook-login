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
		df_eav()->addAttribute('customer', 'dfe_facebook_id', array(
			'type' => 'static',
			'label' => 'Facebook ID',
			'input' => 'text',
			'sort_order' => 1000,
			'position' => 1000,
			'visible' => false,
			'system' => false,
			'required' => false
		));
	}
}