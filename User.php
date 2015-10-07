<?php
namespace Dfe\Facebook;
class User extends \Df\Core\O {
	/** @return string */
	public function email() {return $this->r('email');}

	/** @return string */
	public function gender() {return $this->r('gender');}

	/** @return string */
	public function id() {return $this[self::$P__ID];}

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
	public function locale() {return $this->r('locale');}

	/**
	 * @param string $key
	 * @return string|null
	 */
	private function r($key) {return df_a($this->responseA(), $key);}

	/**
	 * Общую схему запрсоа взял здесь: @link https://github.com/thephpleague/oauth2-facebook
	 * @return array(string => mixed)
	 */
	private function responseA() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $appSecret */
			$appSecret = \Dfe\Facebook\Settings\Credentials::s()->appSecret();
			/** @var \Zend\Uri\Http $uri */
			$uri = new \Zend\Uri\Http('https://graph.facebook.com');
			// Надо передавать именно идентификтор.
			// «me» не работает: будет сбой «Some of the aliases you requested do not exist».
			$uri->setPath('/v2.4/' . $this->id());
			$uri->setQuery([
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
				'access_token' => $this->token(),
				'appsecret_proof' => hash_hmac('sha256', $this->token(), $appSecret)
			]);
			/** @link http://stackoverflow.com/a/3367977 */
			/** @var \Zend\Http\Client\Adapter\Curl $adapter */
			$adapter = new \Zend\Http\Client\Adapter\Curl;
			$adapter->setCurlOption(CURLOPT_SSL_VERIFYPEER, false);
			/** @var \Zend\Http\Client $httpClient */
			$httpClient = new \Zend\Http\Client();
			$httpClient
				->setAdapter($adapter)
				->setHeaders(array())
				->setUri($uri)
				/** @link http://framework.zend.com/manual/current/en/modules/zend.http.client.html */
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
		$this->_prop(self::$P__ID, RM_V_STRING_NE);
		$this->_prop(self::$P__TOKEN, RM_V_STRING_NE);
	}
	/** @var string */
	private static $P__ID = 'id';
	/** @var string */
	private static $P__TOKEN = 'token';

	/**
	 * @param string $id
	 * @param string $token
	 * @return User
	 */
	public static function i($id, $token) {
		return new self([self::$P__ID => $id, self::$P__TOKEN => $token]);
	}
}

