<?php
namespace Dfe\FacebookLogin;
use Df\Customer\Model\Gender;
use Dfe\FacebookLogin\Settings\Credentials;
final class Customer extends \Df\Sso\Customer {
	/**
	 * 2015-10-12
	 * Пользователь мог быть зарегистрирован на Facebook по номеру телефона,
	 * и тогда почтового адреса мы не узнаем
	 * (хотя у пользователя всё равно есть на самом деле адрес на домене facebook.com).
	 * https://developers.facebook.com/docs/graph-api/reference/user
	 * «The person's primary email address listed on their profile.
	 * This field will not be returned if no valid email address is available».
	 * @override
	 * @see \Df\Sso\Customer::email()
	 * @used-by \Df\Sso\CustomerReturn::customerData()
	 */
	function email():string {return df_contains($r = $this->r('email'), '@') ? $r : '';}

	/**
	 * @override
	 * @see \Df\Sso\Customer::gender()
	 * @used-by \Df\Sso\CustomerReturn::register()
	 */
	function gender():int {
		switch ($this->r('gender')) {
			case 'male':
				$r = Gender::MALE;
				break;
			case 'female':
				$r = Gender::FEMALE;
				break;
			default:
				$r = Gender::UNKNOWN;
		}
		return $r;
	}

	/**
	 * https://developers.facebook.com/docs/facebook-login/access-tokens#extending
	 * @used-by self::request()
	 * @used-by \Dfe\FacebookLogin\Controller\Index\Index::customerData()
	 */
	function longLivedAccessToken():string {return dfc($this, function():string {
		$s = Credentials::s(); /** @var Credentials $s */
		return df_result_sne(dfa($this->responseJson($this->requestBasic('/oauth/access_token', [
			'grant_type' => 'fb_exchange_token'
			,'client_id' => $s->appId()
			,'client_secret' => $s->appSecret()
			,'fb_exchange_token' => $this->token()
		])), 'access_token'));
	});}

	/**
	 * @override
	 * @see \Df\Sso\Customer::nameFirst()
	 * @used-by \Df\Sso\CustomerReturn::register()
	 */
	function nameFirst():string {return $this->r('first_name');}

	/**
	 * @override
	 * @see \Df\Sso\Customer::nameLast()
	 * @used-by \Df\Sso\CustomerReturn::register()
	 */
	function nameLast():string {return $this->r('last_name');}

	/**
	 * @override
	 * @see \Df\Sso\Customer::nameMiddle()
	 * @used-by \Df\Sso\CustomerReturn::register()
	 */
	function nameMiddle():string {return $this->r('middle_name');}

	/** @used-by \Dfe\FacebookLogin\Controller\Index\Index::customerData() */
	function nameFull():string {return $this->r('name');}

	/**
	 * https://developers.facebook.com/docs/graph-api/reference/user/picture/
	 * https://developers.facebook.com/docs/graph-api/reference/profile-picture-source/
	 * @used-by \Dfe\FacebookLogin\Controller\Index\Index::customerData()
	 */
	function picture():string {return df_result_sne(dfa_deep($this->request('picture', ['redirect' => 'false']), 'data/url'));}

	/**
	 * @used-by \Dfe\FacebookLogin\Controller\Index\Index::customerIdFieldValue()
	 * @used-by \Dfe\FacebookLogin\Customer::password()
	 */
	function id():string {return $this->r('token_for_business');}

	/**
	 * 2015-10-12
	 * Facebook может не вернуть дату, а также вернуть её лишь частично:
	 * https://developers.facebook.com/docs/graph-api/reference/user
	 * «The person's birthday.
	 * This is a fixed format string, like MM/DD/YYYY.
	 * However, people can control who can see the year they were born
	 * separately from the month and day
	 * so this string can be only the year (YYYY) or the month + day (MM/DD)»
	 * @override
	 * @see \Df\Sso\Customer::_dob()
	 * @used-by \Df\Sso\Customer::dob()
	 * @return \DateTime|null
	 */
	protected function _dob() {return dfc($this, function() {
		$r = null; /** @var \DateTime|null $r */
		if ($raw = $this->r('birthday')) { /** @var string|null $raw */
			$a = df_int(explode('/', $raw)); /** @var string[] $a */
			$count = count($a); /** @var int $count */
			$r = new \DateTime;
			# 2022-10-27 https://3v4l.org/WEpEk
			$r->setDate(...(1 === $count ? [$raw, 1, 1] : [2 === $count ? 1900 : $a[2], $a[0], $a[1]]));
		}
		return $r;
	});}

	/**
	 * @param string $key
	 * @return string|null
	 */
	private function r($key) {return dfa($this->responseA(), $key);}

	/**
	 * @param string $path
	 * @param array(string => mixed) $params
	 * @return array(string => mixed)
	 * @throws Exception
	 */
	private function request($path, array $params) {
		# 2015-10-10
		# Полученный нами от браузера идентификатор пользователя Facebook не является глобальным: н разный для разных приложений.
		$appScopedId = df_request('user'); /** @var string $appScopedId */
		$fullPath = '/' . implode('/', df_clean(['v2.5', $appScopedId, $path])); /** @var string $fullPath */
		$responseAsJson = $this->requestBasic($fullPath, $params + [
			'access_token' => $this->longLivedAccessToken(),
			'appsecret_proof' => hash_hmac('sha256', $this->longLivedAccessToken(), Credentials::s()->appSecret())
		]); /** @var string $responseAsJson */
		return $this->responseJson($responseAsJson);
	}

	/**
	 * @param string $path
	 * @param array(string => mixed) $params
	 * @return string
	 */
	private function requestBasic($path, array $params) {
		/** @var \Zend\Uri\Http $uri */
		$uri = new \Zend\Uri\Http('https://graph.facebook.com');
		$uri->setPath($path);
		$uri->setQuery($params);
		/** http://stackoverflow.com/a/3367977 */
		$adapter = new \Zend\Http\Client\Adapter\Curl; /** @var \Zend\Http\Client\Adapter\Curl $adapter */
		$adapter->setCurlOption(CURLOPT_SSL_VERIFYPEER, false);
		$httpClient = new \Zend\Http\Client(); /** @var \Zend\Http\Client $httpClient */
		$httpClient
			->setAdapter($adapter)
			->setHeaders([])
			->setUri($uri)
			->setOptions(['timeout' => 10])
		;
		/** @var \Zend\Http\Response $response */
		$response = $httpClient->send();
		return $response->getBody();
	}

	/**
	 * Общую схему запроса взял здесь: https://github.com/thephpleague/oauth2-facebook
	 * @return array(string => mixed)
	 * @throws Exception
	 */
	private function responseA() {return dfc($this, function() {return $this->request('', [
		/**
		 * 2015-10-10
		 * Все доступные поля перечислены здесь:
		 * https://developers.facebook.com/docs/graph-api/reference/user
		 *
		 * Обратите внимание, что получить адрес страницы пользователя
		 * мы в 2015 году уже не можем: http://stackoverflow.com/questions/29152500
		 * «link» возвращает адрес типа
		 * https://www.facebook.com/app_scoped_user_id/10206714043186313/
		 * толку нам от него мало.
		 */
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
			/**
			 * 2015-10-10
			 * Предварительно надо настроить учётную запись на https://business.facebook.com/
			 * https://developers.facebook.com/docs/apps/for-business
			 * Иначе будет сбой: «Application must be associated with a business».
			 */
			,'token_for_business'
			/**
			 * 2015-10-12
			 * 1) Администратор Magento в состоянии назначить дату рождения
			 * обязательной для указания покупателями.
			 * 2) Facebook может не вернуть дату, а также вернуть её лишь частично:
			 * https://developers.facebook.com/docs/graph-api/reference/user
			 * «The person's birthday.
			 * This is a fixed format string, like MM/DD/YYYY.
			 * However, people can control who can see the year they were born
			 * separately from the month and day
			 * so this string can be only the year (YYYY) or the month + day (MM/DD)»
			 */
			,'birthday'
		])
	]);});}

	/**
	 * 2017-04-06
	 * @used-by self::longLivedAccessToken()
	 * @used-by self::request()
	 * @param string $json
	 * @return array(satring => mixed)
	 * @throws Exception
	 */
	private function responseJson($json) {
		df_assert_array($r = df_json_decode($json)); /** @var array(string => mixed) $r */
		if ($error = dfa($r, 'error')) { /** @var array(string => string)|null $error */
			throw new Exception($error);
		}
		return $r;
	}

	/** @return string */
	private function token() {return df_request('token');}
}