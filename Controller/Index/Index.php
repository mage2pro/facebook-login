<?php
namespace Dfe\Facebook\Controller\Index;
use Dfe\Facebook\Setup\InstallSchema;
class Index extends \Magento\Framework\App\Action\Action {
	/**
	 * @override
	 * @see \Magento\Framework\App\Action\Action::execute()
	 * @return \Magento\Framework\Controller\Result\Redirect
	 */
	public function execute() {
		try {
			/** @var \Magento\Customer\Model\Session $session */
			$session = df_o(\Magento\Customer\Model\Session::class);
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
		catch (\Exception $e) {
			df_message()->addErrorMessage(df_ets($e));
		}
		return $this->resultRedirectFactory->create()->setUrl(df_request('url'));
	}

	/** @return \Magento\Customer\Model\Customer */
	private function customer() {
		if (!isset($this->{__METHOD__})) {
			/** @var \Magento\Customer\Model\ResourceModel\Customer $resource */
			$resource = df_o(\Magento\Customer\Model\ResourceModel\Customer::class);
			/** @var \Magento\Customer\Model\Customer $result */
			$result = df_om()->create(\Magento\Customer\Model\Customer::class);
			/** @var \Magento\Framework\DB\Select $select */
			$select = df_conn()->select()->from($resource->getEntityTable(), [$resource->getEntityIdField()]);
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
			$select->where(
				'? = ' . InstallSchema::F__TOKEN_FOR_BUSINESS, $this->fbUser()->tokenForBusiness()
			);
			/**
			 * @see \Magento\Customer\Model\ResourceModel\Customer::loadByEmail()
			 * https://github.com/magento/magento2/blob/2e2785cc6a78dc073a4d5bb5a88bd23161d3835c/app/code/Magento/Customer/Model/Resource/Customer.php#L215
			 */
			if ($result->getSharingConfig()->isWebsiteScope()) {
				/**
				 * @see \Magento\Customer\Model\CustomerRegistry::retrieveByEmail()
				 * https://github.com/magento/magento2/blob/2e2785cc6a78dc073a4d5bb5a88bd23161d3835c/app/code/Magento/Customer/Model/CustomerRegistry.php#L104
				 * @see \Magento\Customer\Model\ResourceModel\Customer::loadByEmail()
				 * https://github.com/magento/magento2/blob/2e2785cc6a78dc073a4d5bb5a88bd23161d3835c/app/code/Magento/Customer/Model/Resource/Customer.php#L222
				 */
				$select->where('? = website_id', df_store_m()->getStore()->getWebsiteId());
			}
			/** @var int $customerId */
			$customerId = df_conn()->fetchOne($select);
			if (!$customerId) {
				$this->register($result);
			}
			else {
				$resource->load($result, $customerId);
				// Обновляем в нашей БД полученую от Facebook информацию о покупателе.
				$result->addData($this->dataForUpdate())->save();
			}
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
			$registry = df_o(\Magento\Customer\Model\CustomerRegistry::class);
			$registry->push($result);
			/**
			 * 2015-12-10
			 * Иначе новый покупатель не попадает в таблицу customer_grid_flat
			 */
			$result->reindex();
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return array(string => mixed) */
	private function dataForUpdate() {
		return [
			InstallSchema::F__FULL_NAME => $this->fbUser()->nameFull()
			, InstallSchema::F__LONG_LIVED_ACCESS_TOKEN => $this->fbUser()->longLivedAccessToken()
			, InstallSchema::F__PICTURE => $this->fbUser()->picture()
		];
	}

	/**
	 * 2015-10-12
	 * Регистрация нового клиента.
	 * @param \Magento\Customer\Model\Customer $customer
	 * @return void
	 */
	private function register(\Magento\Customer\Model\Customer $customer) {
		/**
		 * 2015-10-12
		 * https://github.com/magento/magento2/issues/2087
		 * Приходится присваивать магазин в 2 шага...
		 */
		/** @var \Magento\Store\Api\Data\StoreInterface|\Magento\Store\Model\Store $store */
		$store = df_store_m()->getStore();
		$customer->setStore($store);
		$customer->setGroupId(df_customer_group_m()->getDefaultGroup($store->getId())->getId());
		/** @var string $email */
		$email = $this->fbUser()->email();
		if (!$email) {
			$email = df_next_increment('customer_entity') . '@none.com';
		}
		$customer->addData([
			'firstname' => $this->fbUser()->nameFirst()
			,'lastname' => $this->fbUser()->nameLast()
			,'middlename' => $this->fbUser()->nameMiddle()
			,'email' => $email
			,'password' => substr($this->fbUser()->tokenForBusiness(), 0, 8)
			/**
			 * Facebook возвращает male / female,
			 * а Magento использует Male / Female
			 */
			,'gender' => $this->fbUser()->genderCode()
			//ucfirst($this->fbUser()->gender())
			,InstallSchema::F__TOKEN_FOR_BUSINESS => $this->fbUser()->tokenForBusiness()
			/**
				if ($customer->getForceConfirmed() || $customer->getPasswordHash() == '') {
					$customer->setConfirmation(null);
				}
			    elseif (!$customer->getId() && $customer->isConfirmationRequired()) {
					$customer->setConfirmation($customer->getRandomConfirmationKey());
				}
			 * https://github.com/magento/magento2/blob/6fa09047a6d4a1ec71494fadec5a42284ba7cc1d/app/code/Magento/Customer/Model/ResourceModel/Customer.php#L133
			 */
			,'force_confirmed' => true
		]);
		/** @var \DateTime $dob */
		$dob = $this->fbUser()->dob();
		if (!$dob && df_is_customer_attribute_required('dob')) {
			$dob = new \DateTime;
			$dob->setDate(1900, 1, 1);
		}
		if ($dob) {
			$customer['dob'] = $dob;
		}
		$customer->addData($this->dataForUpdate());
		if (df_is_customer_attribute_required('taxvat')) {
			$customer['taxvat'] = '000000000000';
		}
		$customer->save();
		//df_customer_save($customer->getDataModel());
		/** @var \Magento\Customer\Model\Address $address */
		$address = df_om()->create(\Magento\Customer\Model\Address::class);
		$address->setCustomer($customer);
		$address->addData([
			'firstname' => $this->fbUser()->nameFirst()
			,'lastname' => $this->fbUser()->nameLast()
			,'middlename' => $this->fbUser()->nameMiddle()
			,'country_id' => df_visitor()->iso2()
			,'region_id' => null
			,'region' => df_visitor()->regionName()
			,'city' => df_visitor()->city()
			,'telephone' => '000000'
			,'street' => '---'
			,'is_default_billing' => 1
			,'is_default_shipping' => 1
			,'save_in_address_book' => 1
		]);
		$postCode = df_visitor()->postCode();
		if (!$postCode && df_is_postcode_required(df_visitor()->iso2())) {
			$postCode = '000000';
		}
		$address['postcode'] = $postCode;
		$address->save();
		df_dispatch('customer_register_success', ['account_controller' => $this, 'customer' => $customer]);
	}

	/** @return \Dfe\Facebook\User */
	private function fbUser() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = \Dfe\Facebook\User::i(df_request('user'), df_request('token'));
		}
		return $this->{__METHOD__};
	}
}
