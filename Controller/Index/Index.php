<?php
namespace Dfe\Facebook\Controller\Index;
class Index extends \Magento\Framework\App\Action\Action {
	/**
	 * @override
	 * @see \Magento\Framework\App\Action\Action::execute()
	 * @return \Magento\Framework\Controller\Result\Redirect
	 */
	public function execute() {
		/** @var string $response */
		return $this->resultRedirectFactory->create()->setUrl(rm_request('url'));
	}

	/** @return \Magento\Customer\Model\Customer */
	private function customer() {
		if (!isset($this->{__METHOD__})) {
			/** @var \Magento\Customer\Model\Resource\Customer $resource */
			$resource = df_o('Magento\Customer\Model\Resource\Customer');
			/** @var \Magento\Customer\Model\Customer $result */
			$result = rm_om()->create('Magento\Customer\Model\Customer');
			/** @var \Magento\Framework\DB\Select $select */
			$select = rm_conn()->select()->from($resource->getEntityTable(), [$resource->getEntityIdField()]);
			$select->where('? = ' . \Dfe\Facebook\Setup\InstallSchema::F__FACEBOOK_ID, $this->fbUser()->id());
			/**
			 * @see \Magento\Customer\Model\Resource\Customer::loadByEmail()
			 * @link https://github.com/magento/magento2/blob/2e2785cc6a78dc073a4d5bb5a88bd23161d3835c/app/code/Magento/Customer/Model/Resource/Customer.php#L215
			 */
			if ($result->getSharingConfig()->isWebsiteScope()) {
				/**
				 * @see \Magento\Customer\Model\CustomerRegistry::retrieveByEmail()
				 * @link https://github.com/magento/magento2/blob/2e2785cc6a78dc073a4d5bb5a88bd23161d3835c/app/code/Magento/Customer/Model/CustomerRegistry.php#L104
				 * @see \Magento\Customer\Model\Resource\Customer::loadByEmail()
				 * @link https://github.com/magento/magento2/blob/2e2785cc6a78dc073a4d5bb5a88bd23161d3835c/app/code/Magento/Customer/Model/Resource/Customer.php#L222
				 */
				$select->where('? = website_id', rm_store_m()->getStore()->getWebsiteId());
			}
			$customerId = rm_conn()->fetchOne($select);
			if ($customerId) {
				$resource->load($result, $customerId);
			} else {
				$result->setData([]);
			}
			rm_log($result);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return \Dfe\Facebook\User */
	private function fbUser() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = \Dfe\Facebook\User::i(rm_request('user'), rm_request('token'));
		}
		return $this->{__METHOD__};
	}
}
