<?php
namespace Dfe\Facebook;
class User extends \Df\Core\O {
	/** @return string */
	public function email() {return $this->r('email');}

	/** @return string */
	public function gender() {return $this->r('gender');}

	/** @return string */
	public function locale() {return $this->r('locale');}

	/** @return string */
	public function nameFirst() {return $this->r('first_name');}

	/** @return string */
	public function nameLast() {return $this->r('last_name');}

	/** @return string */
	public function nameMiddle() {return $this->r('middle_name');}

	/** @return string */
	public function nameFormat() {return $this->r('name_format');}

	/** @return string */
	public function nameFull() {return $this->r('name');}

	/** @return string */
	public function tokenForBusiness() {return $this->r('token_for_business');}

	/**
	 * 2015-10-10
	 * Полученный нами от браузера идентификатор пользователя Facebook
	 * не является глобальным: он разный для разных приложений.
	 * @return string
	 */
	private function appScopedId() {return $this[self::$P__APP_SCOPED_ID];}

	/**
	 * @param string $key
	 * @return string|null
	 */
	private function r($key) {return df_a($this->responseA(), $key);}

	/**
	 * Общую схему запрсоа взял здесь: https://github.com/thephpleague/oauth2-facebook
	 * @return array(string => mixed)
	 */
	private function responseA() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $appSecret */
			$appSecret = \Dfe\Facebook\Settings\Credentials::s()->appSecret();
			/** @var \Zend\Uri\Http $uri */
			$uri = new \Zend\Uri\Http('https://graph.facebook.com');
			/**
			 * Надо передавать именно идентификтор.
			 * «me» не работает: будет сбой «Some of the aliases you requested do not exist».
			 */
			$uri->setPath('/v2.4/' . $this->appScopedId());
			$uri->setQuery([
				/**
				 * 2015-10-10
				 * Все доступные поля перечислены здесь:
				 * https://developers.facebook.com/docs/graph-api/reference/user
				 */
				'fields' => df_csv([
					'email'
					,'first_name'
					,'gender'
					,'last_name'
					// «A link to the person's Timeline»
					,'link'
					,'locale'
					,'middle_name'
					,'name'
					,'name_format'
					,'timezone'
					/**
					 * 2015-10-10
					 * Предварительно надо настроить учётную запись на https://business.facebook.com/
					 * https://developers.facebook.com/docs/apps/for-business
					 * Иначе будет сбой: «Application must be associated with a business».
					 */
					,'token_for_business'
				]),
				'access_token' => $this->token(),
				'appsecret_proof' => hash_hmac('sha256', $this->token(), $appSecret)
			]);
			/** http://stackoverflow.com/a/3367977 */
			/** @var \Zend\Http\Client\Adapter\Curl $adapter */
			$adapter = new \Zend\Http\Client\Adapter\Curl;
			$adapter->setCurlOption(CURLOPT_SSL_VERIFYPEER, false);
			/** @var \Zend\Http\Client $httpClient */
			$httpClient = new \Zend\Http\Client();
			$httpClient
				->setAdapter($adapter)
				->setHeaders(array())
				->setUri($uri)
				/** http://framework.zend.com/manual/current/en/modules/zend.http.client.html */
				->setOptions(array('timeout' => 10))
			;
			/** @var \Zend\Http\Response $response */
			$response = $httpClient->send();
			/** @var string $responseAsJson */
			$responseAsJson = $response->getBody();
			/** @var array(string => mixed) $result */
			$result = json_decode($responseAsJson, true);
			df_result_array($result);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function token() {return $this[self::$P__TOKEN];}

	/**
	 * 2015-10-07
	 * @override
	 * @see \Df\Core\O::_construct()
	 * @used-by \Df\Core\O::__construct()
	 * @return void
	 */
	protected function _construct() {
		$this->_prop(self::$P__APP_SCOPED_ID, RM_V_STRING_NE);
		$this->_prop(self::$P__TOKEN, RM_V_STRING_NE);
	}
	/** @var string */
	private static $P__APP_SCOPED_ID = 'app_scoped_id';
	/** @var string */
	private static $P__TOKEN = 'token';

	/**
	 * @param string $appScopedId
	 * @param string $token
	 * @return User
	 */
	public static function i($appScopedId, $token) {
		return new self([self::$P__APP_SCOPED_ID => $appScopedId, self::$P__TOKEN => $token]);
	}
}

