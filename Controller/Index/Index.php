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
		/** @var string $proof */
		$proof = hash_hmac('sha256', $accessToken, $appSecret);
		$baseUrl = 'https://graph.facebook.com/v2.4';
		$params = http_build_query([
			'fields' => df_csv([
				'email'
				,'first_name'
				,'gender'
				,'last_name'
				,'locale'
				,'middle_name'
				,'name'
				,'name_format'
				,'timezone'
			]),
			'access_token' => $accessToken,
			'appsecret_proof' => $proof
		]);
		/** @var string $response */
		$response = file_get_contents($baseUrl . '/me' . $params);
		return $this->resultRedirectFactory->create()->setUrl(rm_request('url'));
	}
}
