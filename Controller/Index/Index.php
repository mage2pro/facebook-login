<?php
namespace Dfe\Facebook\Controller\Index;
class Index extends \Magento\Framework\App\Action\Action {
	/**
	 * @override
	 * @see \Magento\Framework\App\Action\Action::execute()
	 * @return \Magento\Framework\Controller\Result\Redirect
	 */
	public function execute() {
		try {
			/** @var \Magento\Customer\Model\Session $session */
			$session = df_o('Magento\Customer\Model\Session');
			if ($this->customer()->getId()) {
				/**
				 * 2015-10-08
				 * По аналогии с @see \Magento\Customer\Controller\Account\LoginPost::execute()
				 * https://github.com/magento/magento2/blob/54b85e93af25ec83e933d851d762548c07a1092c/app/code/Magento/Customer/Controller/Account/LoginPost.php#L84-L85
				 */
				$session->setCustomerDataAsLoggedIn($this->customer()->getDataModel());
				$session->regenerateId();
				/**
				 * По аналогии с @see \Magento\Customer\Model\Account\Redirect::updateLastCustomerId()
				 * Напрямую тот метод вызвать не можем, потому что он protected,
				 * а использовать весь класс @see \Magento\Customer\Model\Account\Redirect пробовал,
				 * но оказалось неудобно из-за слишком сложной процедуры перенаправлений.
				 */
				if ($session->getLastCustomerId() != $session->getId()) {
					$session->unsBeforeAuthUrl()->setLastCustomerId($session->getId());
				}
			}
			else {

			}
		}
		catch (\Exception $e) {
			df_message()->addErrorMessage(rm_ets($e));
		}
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
			$select->where(
				'? = ' . \Dfe\Facebook\Setup\InstallSchema::F__TOKEN_FOR_BUSINESS
				/**
				 * 2015-10-10
				 * 1) Полученный нами от браузера идентификатор пользователя Facebook
				 * не является глобальным: он разный для разных приложений.
				 * 2) Я так понял, что нельзя использовать одно и то же приложение Facebook
				 * сразу на нескольких доменах.
				 * 3) Из пунктов 1 и 2 следует, что нам нельзя идентифицировать пользователя Facebook
				 * по его идентификатору: ведь Magento — многодоменная система.
				 *
				 * Есть выход: token_for_business
				 * https://developers.facebook.com/docs/apps/upgrading#upgrading_v2_0_user_ids
				 * https://developers.facebook.com/docs/apps/for-business
				 * https://business.facebook.com/
				 */
				, $this->fbUser()->tokenForBusiness()
			);
			/**
			 * @see \Magento\Customer\Model\Resource\Customer::loadByEmail()
			 * https://github.com/magento/magento2/blob/2e2785cc6a78dc073a4d5bb5a88bd23161d3835c/app/code/Magento/Customer/Model/Resource/Customer.php#L215
			 */
			if ($result->getSharingConfig()->isWebsiteScope()) {
				/**
				 * @see \Magento\Customer\Model\CustomerRegistry::retrieveByEmail()
				 * https://github.com/magento/magento2/blob/2e2785cc6a78dc073a4d5bb5a88bd23161d3835c/app/code/Magento/Customer/Model/CustomerRegistry.php#L104
				 * @see \Magento\Customer\Model\Resource\Customer::loadByEmail()
				 * https://github.com/magento/magento2/blob/2e2785cc6a78dc073a4d5bb5a88bd23161d3835c/app/code/Magento/Customer/Model/Resource/Customer.php#L222
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
				 * https://github.com/magento/magento2/blob/54b85e93af25ec83e933d851d762548c07a1092c/app/code/Magento/Customer/Model/AccountManagement.php#L381
				 * Я так понимаю, ядро та кделает потому, что выше там код:
				 * $customer = $this->customerRepository->get($username);
				 * и этот код необязательно возвращает объект класса @see \Magento\Customer\Model\Customer
				 * а может вернуть что-то другое, поддерживающее интерфейс
				 * @see \Magento\Customer\Api\Data\CustomerInterface
				 * @see \Magento\Customer\Api\CustomerRepositoryInterface::get()
				 */
				/**
				 * По аналогии с @see \Magento\Customer\Model\AccountManagement::authenticate()
				 * https://github.com/magento/magento2/blob/54b85e93af25ec83e933d851d762548c07a1092c/app/code/Magento/Customer/Model/AccountManagement.php#L382-L385
				 */
				df_dispatch('customer_customer_authenticated', ['model' => $result, 'password' => '']);
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
