<?php
namespace Dfe\FacebookLogin;
use DateTime as DT;
use Df\Customer\Model\Gender;
use Dfe\FacebookLogin\ResponseValidator as RV;
use Dfe\FacebookLogin\Settings\Credentials;
use Laminas\Http\Client as zClient;
use Laminas\Http\Client\Adapter\Curl;
use Laminas\Uri\Http as zHttp;
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
	function email():string {return df_is_email($r = $this->r('email')) ? $r : '';}

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
	 * @used-by \Dfe\FacebookLogin\Controller\Index\Index::customerIdFieldValue()
	 * @used-by \Dfe\FacebookLogin\Customer::password()
	 */
	function id():string {return $this->r('token_for_business');}

	/**
	 * https://developers.facebook.com/docs/facebook-login/access-tokens#extending
	 * @used-by self::r()
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
	function picture():string {return df_result_sne(dfa_deep($this->req('picture', ['redirect' => 'false']), 'data/url'));}

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
	protected function _dob():?DT {return dfc($this, function():?DT {
		$r = null; /** @var ?DT $r */
		if ($raw = $this->r('birthday')) { /** @var string $raw */
			$a = df_int(explode('/', $raw)); /** @var string[] $a */
			$count = count($a); /** @var int $count */
			$r = new DT;
			# 2022-10-27 https://3v4l.org/WEpEk
			$r->setDate(...(1 === $count ? [$raw, 1, 1] : [2 === $count ? 1900 : $a[2], $a[0], $a[1]]));
		}
		return $r;
	});}

	/**
	 * Общую схему запроса взял здесь: https://github.com/thephpleague/oauth2-facebook
	 * @used-by self::_dob()
	 * @used-by self::email()
	 * @used-by self::gender()
	 * @used-by self::id()
	 * @used-by self::nameFirst()
	 * @used-by self::nameFull()
	 * @used-by self::nameLast()
	 * @used-by self::nameMiddle()
	 */
	private function r(string $k):string {return dfaoc($this, function():array {return $this->req('', [
		# 2015-10-10
		# 1) Все доступные поля перечислены здесь: https://developers.facebook.com/docs/graph-api/reference/user
		# 2) Получить адрес страницы пользователя мы в 2015 году уже не можем: http://stackoverflow.com/questions/29152500
		# «link» возвращает адрес типа https://www.facebook.com/app_scoped_user_id/10206714043186313/
		# Толку нам от него мало.
		'fields' => df_csv(
			'email'
			,'first_name'
			,'gender'
			,'last_name'
			,'locale'
			,'middle_name'
			,'name'
			,'name_format'
			,'timezone'
			# 2015-10-10
			# Предварительно надо настроить учётную запись на https://business.facebook.com/
			# https://developers.facebook.com/docs/apps/for-business
			# Иначе будет сбой: «Application must be associated with a business».
			,'token_for_business'
			# 2015-10-12
			# 1) Администратор Magento в состоянии назначить дату рождения
			# обязательной для указания покупателями.
			# 2) Facebook может не вернуть дату, а также вернуть её лишь частично:
			# https://developers.facebook.com/docs/graph-api/reference/user
			# «The person's birthday.
			# This is a fixed format string, like MM/DD/YYYY.
			# However, people can control who can see the year they were born
			# separately from the month and day
			# so this string can be only the year (YYYY) or the month + day (MM/DD)»
			,'birthday'
		)
	]);}, $k, '');}

	/**
	 * @used-by self::picture()
	 * @used-by self::responseA()
	 * @param array(string => mixed) $params
	 * @return array(string => mixed)
	 * @throws RV
	 */
	private function req(string $path, array $params):array {
		# 2015-10-10
		# Полученный нами от браузера идентификатор пользователя Facebook не является глобальным:
		# он разный для разных приложений.
		$appScopedId = df_request('user'); /** @var string $appScopedId */
		$fullPath = '/' . implode('/', df_clean(['v2.5', $appScopedId, $path])); /** @var string $fullPath */
		$responseAsJson = $this->requestBasic($fullPath, $params + [
			'access_token' => $this->longLivedAccessToken(),
			'appsecret_proof' => hash_hmac('sha256', $this->longLivedAccessToken(), Credentials::s()->appSecret())
		]); /** @var string $responseAsJson */
		return $this->responseJson($responseAsJson);
	}

	/**
	 * @used-by self::longLivedAccessToken()
	 * @used-by self::req()
	 * @param array(string => mixed) $params
	 */
	private function requestBasic(string $path, array $params):string {
		$u = df_zuri('https://graph.facebook.com'); /** @var zHttp $u */
		$u->setPath($path);
		$u->setQuery($params);
		/** http://stackoverflow.com/a/3367977 */
		$adapter = new Curl; /** @var Curl $adapter */
		$adapter->setCurlOption(CURLOPT_SSL_VERIFYPEER, false);
		$c = new zClient; /** @var zClient $c */
		$c
			->setAdapter($adapter)
			->setHeaders([])
			->setUri($u)
			->setOptions(['timeout' => 10])
		;
		$res = $c->send(); /** @var \Laminas\Http\Response $res */
		return $res->getBody();
	}

	/**
	 * 2017-04-06
	 * @used-by self::longLivedAccessToken()
	 * @used-by self::r()
	 * @return array(satring => mixed)
	 * @throws RV
	 */
	private function responseJson(string $j):array {
		df_assert_array($r = df_json_decode($j)); /** @var array(string => mixed) $r */
		RV::assert(dfa($r, 'error', []));
		return $r;
	}

	/** @used-by self::longLivedAccessToken() */
	private function token():string {return df_request('token');}
}