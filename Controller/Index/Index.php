<?php
namespace Dfe\Facebook\Controller\Index;
class Index extends \Magento\Framework\App\Action\Action {
	/**
	 * @override
	 * @see \Magento\Framework\App\Action\Action::execute()
	 * @return \Magento\Framework\Controller\Result\Redirect
	 */
	public function execute() {
		/** @var string $accessToken */
		$accessToken = rm_request('accessToken');
		/** @var string $appSecret */
		$appSecret = \Dfe\Facebook\Settings\Credentials::s()->appSecret();
		/** @var string $response */
		$response = file_get_contents("http://graph.facebook.com/debug_token?input_token={$accessToken}&access_token={$appSecret}");
		return $this->resultRedirectFactory->create()->setUrl(rm_request('url'));
	}
}
