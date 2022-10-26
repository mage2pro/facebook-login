<?php
namespace Dfe\FacebookLogin\Controller\Index;
use Df\Sso\CustomerReturn as _P;
use Dfe\FacebookLogin\Setup\UpgradeSchema as Schema;
/**
 * 2016-06-04
 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
 * @method \Dfe\FacebookLogin\Customer c()
 */
class Index extends _P {
	/**
	 * 2016-06-04
	 * @override
	 * @see _P::customerData()
	 * @used-by _P::customer()
	 * @used-by _P::register()
	 * @return array(string => mixed)
	 */
	final protected function customerData() {return $this->customerDataCustom() + parent::customerData();}

	/**
	 * 2016-06-06
	 * Перечень свойств покупателя, которые надо обновить в Magento
	 * после их изменения в сторонней системе авторизации.
	 * @see _P::customerFieldsToSync()
	 * @used-by _P::customer()
	 * @return string[]
	 */
	final protected function customerFieldsToSync() {return array_merge(
		array_keys($this->customerDataCustom()), parent::customerFieldsToSync()
	);}

	/**
	 * 2016-06-06
	 * @used-by self::customerData()
	 * @used-by self::customerFieldsToSync()
	 * @return array(mixed => mixed)
	 */
	private function customerDataCustom() {return dfc($this, function() {return [
		Schema::F__FULL_NAME => $this->c()->nameFull()
		,Schema::F__LONG_LIVED_ACCESS_TOKEN => $this->c()->longLivedAccessToken()
		,Schema::F__PICTURE => $this->c()->picture()
	];});}
}