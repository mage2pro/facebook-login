<?php
namespace Dfe\Facebook\Block\Backend;
class Info extends \Magento\Backend\Block\Template {
	/**
	 * @override
	 * @see \Magento\Backend\Block\Template::toHtml()
	 * @return string
	 */
	public function toHtml() {return $this->getFacebookId() ? parent::toHtml() : '';}

	/**
	 * 2015-10-06
	 * Обратите внимание, что хотя идентификатор Facebook состоит только из цифр,
	 * ни в коем случае нельзя приводить его к целому числу,
	 * потому что идентификатор слишком длинный (у меня — 18 знаков),
	 * и @see intval() обрубит его.
	 * @return string|null
	 */
	private function getFacebookId() {
		if (!isset($this->{__METHOD__})) {
			/** @var array(string => mixed) $customerData */
			/** @noinspection PhpUndefinedMethodInspection */
			$customerData = $this->_backendSession->getCustomerData();
			/** @var int|null $facebookId */
			$result = df_a_deep($customerData, 'account/dfe_facebook_id');
			$this->{__METHOD__} = rm_n_set($result);
		}
		return rm_n_get($this->{__METHOD__});
	}

	/**
	 * @override
	 * @see \Magento\Backend\Block\Template::_construct()
	 * @return void
	 */
	protected function _construct() {df_metadata('dfe_facebook_id', $this->getFacebookId());}
}