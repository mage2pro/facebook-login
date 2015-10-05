<?php
namespace Dfe\Facebook\Block;
class Metadata extends \Magento\Framework\View\Element\AbstractBlock {
	/**
	 * 2015-10-05
	 * @override
	 * @see \Magento\Framework\View\Element\AbstractBlock::_construct()
	 * @return void
	 */
	protected function _construct() {
		df_metadata('dfe-facebook-app-id', \Dfe\Facebook\Settings\Credentials::s()->appId());
	}
}