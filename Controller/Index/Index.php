<?php
namespace Dfe\Facebook\Controller\Index;
class Index extends \Magento\Framework\App\Action\Action {
	/**
	 * @override
	 * @see \Magento\Framework\App\Action\Action::execute()
	 * @return \Magento\Framework\Controller\Result\Redirect
	 */
	public function execute() {
		if ($this->customer()->getId()) {
			/** @var \Magento\Customer\Model\Session $session */
			$session = df_o('Magento\Customer\Model\Session');
			/**
			 * 2015-10-08
			 * По аналогии с @see \Magento\Customer\Controller\Account\LoginPost::execute()
			 * @link https://github.com/magento/magento2/blob/54b85e93af25ec83e933d851d762548c07a1092c/app/code/Magento/Customer/Controller/Account/LoginPost.php#L84-L85
			 * Обратите внимание, что в нашем случае вызывать
			 * @see \Magento\Customer\Model\Session::regenerateId() не нужно,
			 * потому что этот метод уже вызывается из
			 * @uses \Magento\Customer\Model\Session::setCustomerAsLoggedIn()
			 */
			$session->setCustomerAsLoggedIn($this->customer());
		}
		else {

		}
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
			/** @var int $customerId */
			$customerId = rm_conn()->fetchOne($select);
			if ($customerId) {
				$resource->load($result, $customerId);
				/**
				 * 2015-10-08
				 * Ядро здесь делает так:
				 * $customerModel = $this->customerFactory->create()->updateData($customer);
				 * @see \Magento\Customer\Model\AccountManagement::authenticate()
				 * @link https://github.com/magento/magento2/blob/54b85e93af25ec83e933d851d762548c07a1092c/app/code/Magento/Customer/Model/AccountManagement.php#L381
				 * Я так понимаю, ядро та кделает потому, что выше там код:
				 * $customer = $this->customerRepository->get($username);
				 * и этот код необязательно возвращает объект класса @see \Magento\Customer\Model\Customer
				 * а может вернуть что-то другое, поддерживающее интерфейс
				 * @see \Magento\Customer\Api\Data\CustomerInterface
				 * @see \Magento\Customer\Api\CustomerRepositoryInterface::get()
				 */
				/**
				 * По аналогии с @see \Magento\Customer\Model\AccountManagement::authenticate()
				 * @link https://github.com/magento/magento2/blob/54b85e93af25ec83e933d851d762548c07a1092c/app/code/Magento/Customer/Model/AccountManagement.php#L382-L385
				 */
				df_dispatch('customer_customer_authenticated', ['model' => $result, 'password' => '']);
				/**
				 * По аналогии с @see \Magento\Customer\Model\AccountManagement::authenticate()
				 * @link https://github.com/magento/magento2/blob/54b85e93af25ec83e933d851d762548c07a1092c/app/code/Magento/Customer/Model/AccountManagement.php#L387
				 */
				df_dispatch('customer_data_object_login', ['customer' => $result]);
				/**
				 * 2015-10-08
				 * Не знаю, нужно ли это на самом деле.
				 * Сделал по аналогии с @see \Magento\Customer\Model\CustomerRegistry::retrieveByEmail()
				 * https://github.com/magento/magento2/blob/54b85e93af25ec83e933d851d762548c07a1092c/app/code/Magento/Customer/Model/CustomerRegistry.php#L133-L134
				 */
				/** @var \Magento\Customer\Model\CustomerRegistry $registry */
				$registry = df_o('Magento\Customer\Model\CustomerRegistry');
				$registry->push($result);
			}
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
