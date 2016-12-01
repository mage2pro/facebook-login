<?php
namespace Dfe\FacebookLogin\Controller\Index;
use Df\Sso\CustomerReturn as _P;
use Dfe\FacebookLogin\Customer;
use Dfe\FacebookLogin\Setup\InstallSchema as Schema;
/** @method Customer c() */
class Index extends _P {
	/**
	 * 2016-06-04
	 * @override
	 * @see _P::customerIdFieldName()
	 * @used-by _P::customer()
	 * @return string
	 */
	protected function customerIdFieldName() {return Schema::F__TOKEN_FOR_BUSINESS;}

	/**
	 * 2016-06-04
	 * @override
	 * @see _P::customerData()
	 * @used-by _P::customer()
	 * @used-by _P::register()
	 * @return array(string => mixed)
	 */
	protected function customerData() {return $this->customerDataCustom() + parent::customerData();}

	/**
	 * 2016-06-06
	 * Перечень свойств покупателя, которые надо обновить в Magento
	 * после их изменения в сторонней системе авторизации.
	 * @see _P::customerFieldsToSync()
	 * @used-by _P::customer()
	 * @return string[]
	 */
	protected function customerFieldsToSync() {return array_keys($this->customerDataCustom());}

	/**
	 * 2016-06-05
	 * https://code.dmitry-fedyuk.com/m2e/facebook-login/blob/7c2b601/view/frontend/web/main.js#L46
	 * @override
	 * @see _P::redirectUrlKey()
	 * @used-by _P::execute()
	 * @return string
	 */
	protected function redirectUrlKey() {return 'url';}

	/**
	 * 2016-06-06
	 * @used-by customerData()
	 * @used-by customerFieldsToSync()
	 * @return array(mixed => mixed)
	 */
	private function customerDataCustom() {return dfc($this, function() {return [
		Schema::F__FULL_NAME => $this->c()->nameFull()
		,Schema::F__LONG_LIVED_ACCESS_TOKEN => $this->c()->longLivedAccessToken()
		,Schema::F__PICTURE => $this->c()->picture()
	];});}
}