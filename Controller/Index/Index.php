<?php
namespace Dfe\FacebookLogin\Controller\Index;
use Df\Customer\Controller\Auth;
use Dfe\FacebookLogin\Setup\InstallSchema;
use Magento\Customer\Model\Session;
class Index extends Auth {
	/**
	 * 2016-06-04
	 * @override
	 * @see \Df\Customer\Controller\Auth::_dob()
	 * @used-by \Df\Customer\Controller\Auth::dob()
	 * @return \DateTime|null
	 */
	protected function _dob() {return $this->fbUser()->dob();}

	/**
	 * 2016-06-04
	 * @override
	 * @see \Df\Customer\Controller\Auth::_email()
	 * @used-by \Df\Customer\Controller\Auth::email()
	 * @return string|null
	 */
	protected function _email() {return $this->fbUser()->email();}

	/**
	 * 2016-06-04
	 * @override
	 * @see \Df\Customer\Controller\Auth::customerIdFieldName()
	 * @used-by \Df\Customer\Controller\Auth::customer()
	 * @return string
	 */
	protected function customerIdFieldName() {return InstallSchema::F__TOKEN_FOR_BUSINESS;}

	/**
	 * 2016-06-04
	 * @override
	 * @see \Df\Customer\Controller\Auth::customerIdFieldValue()
	 * @used-by \Df\Customer\Controller\Auth::customer()
	 * @return string
	 */
	protected function customerIdFieldValue() {return $this->fbUser()->tokenForBusiness();}

	/**
	 * 2016-06-04
	 * @override
	 * @see \Df\Customer\Controller\Auth::customerData()
	 * @used-by \Df\Customer\Controller\Auth::customer()
	 * @used-by \Df\Customer\Controller\Auth::register()
	 * @return array(string => mixed)
	 */
	protected function customerData() {return [
		InstallSchema::F__FULL_NAME => $this->fbUser()->nameFull()
		, InstallSchema::F__LONG_LIVED_ACCESS_TOKEN => $this->fbUser()->longLivedAccessToken()
		, InstallSchema::F__PICTURE => $this->fbUser()->picture()
	];}

	/**
	 * 2016-06-04
	 * @override
	 * @used-by \Df\Customer\Controller\Auth::register()
	 * @return int
	 */
	protected function gender() {return $this->fbUser()->genderCode();}

	/**
	 * 2016-06-04
	 * @override
	 * @see \Df\Customer\Controller\Auth::nameFirst()
	 * @used-by \Df\Customer\Controller\Auth::register()
	 * @return string
	 */
	protected function nameFirst() {return $this->fbUser()->nameFirst();}

	/**
	 * 2016-06-04
	 * @override
	 * @see \Df\Customer\Controller\Auth::nameLast()
	 * @used-by \Df\Customer\Controller\Auth::register()
	 * @return string
	 */
	protected function nameLast() {return $this->fbUser()->nameLast();}

	/**
	 * 2016-06-04
	 * @override
	 * @see \Df\Customer\Controller\Auth::nameMiddle()
	 * @used-by \Df\Customer\Controller\Auth::register()
	 * @return string|null
	 */
	protected function nameMiddle() {return $this->fbUser()->nameMiddle();}

	/**
	 * 2016-06-04
	 * @override
	 * @see \Df\Customer\Controller\Auth::password()
	 * @used-by \Df\Customer\Controller\Auth::register()
	 * @return string
	 */
	protected function password() {return substr($this->fbUser()->tokenForBusiness(), 0, 8);}

	/**
	 * 2016-06-04
	 * @override
	 * @see \Df\Customer\Controller\Auth::redirectUrl()
	 * @used-by \Df\Customer\Controller\Auth::execute()
	 * @return string
	 */
	protected function redirectUrl() {return df_request('url');}

	/** @return \Dfe\FacebookLogin\User */
	private function fbUser() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = \Dfe\FacebookLogin\User::i(df_request('user'), df_request('token'));
		}
		return $this->{__METHOD__};
	}
}
