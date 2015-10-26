<?php
namespace Dfe\Facebook;
use Dfe\Facebook\Settings\Credentials;
class User extends \Df\Core\O {
	/**
	 * 2015-10-12
	 * Facebook может не вернуть дату, а также вернуть её лишь частично:
	 * https://developers.facebook.com/docs/graph-api/reference/user
	 * «The person's birthday.
	 * This is a fixed format string, like MM/DD/YYYY.
	 * However, people can control who can see the year they were born
	 * separately from the month and day
	 * so this string can be only the year (YYYY) or the month + day (MM/DD)»
	 * @return \DateTime|null
	 */
	public function dob() {
		if (!isset($this->{__METHOD__})) {
			/** @var \DateTime|null $result */
			$result = null;
			/** @var string|null $raw */
			$raw = $this->r('birthday');
			if ($raw) {
				/** @var string[] $rawA */
				$rawA = rm_int(explode('/', $raw));
				/** @var int $count */
				$count = count($rawA);
				/** @var int $year */
				/** @var int $month */
				/** @var int $day */
				if (1 === $count) {
					$year = $raw;
					$month = 1;
					$day = 1;
				}
				else {
					$month = $rawA[0];
					$day = $rawA[1];
					$year = 2 === $count ? 1900 : $rawA[2];
				}
				$result = new \DateTime;
				$result->setDate($year, $month, $day);
			}
			$this->{__METHOD__} = rm_n_set($result);
		}
		return rm_n_get($this->{__METHOD__});
	}

	/**
	 * 2015-10-12
	 * Пользователь мог быть зарегистрирован на Facebook по номеру телефона,
	 * и тогда почтового адреса мы не узнаем
	 * (хотя у пользователя всё равно есть на самом деле адрес на домене facebook.com).
	 * https://developers.facebook.com/docs/graph-api/reference/user
	 * «The person's primary email address listed on their profile.
	 * This field will not be returned if no valid email address is available».
	 * @return string|null
	 */
	public function email() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $result */
			$result = $this->emailRaw();
			$this->{__METHOD__} = rm_n_set(rm_contains($result, '@') ? $result : null);
		}
		return rm_n_get($this->{__METHOD__});
	}

	/** @return string */
	public function gender() {return $this->r('gender');}

	/** @return int */
	public function genderCode() {
		switch ($this->gender()) {
			case 'male':
				$result = 1;
				break;
			case 'female':
				$result = 2;
				break;
			default:
				$result = 3;
		}
		return $result;
	}

	/** @return string */
	public function locale() {return $this->r('locale');}

	/**
	 * https://developers.facebook.com/docs/facebook-login/access-tokens#extending
	 * @return string
	 */
	public function longLivedAccessToken() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $response */
			$response = $this->requestBasic('/oauth/access_token', [
				'grant_type' => 'fb_exchange_token'
				,'client_id' => Credentials::s()->appId()
				,'client_secret' => Credentials::s()->appSecret()
				,'fb_exchange_token' => $this->token()
			]);
			/** @var array(string => string) $responseA */
			parse_str($response, $responseA);
			/** @var string $result */
			$result = df_a($responseA, 'access_token');
			df_result_string_not_empty($result);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

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

	/**
	 * https://developers.facebook.com/docs/graph-api/reference/user/picture/
	 * https://developers.facebook.com/docs/graph-api/reference/profile-picture-source/
	 * @return string
	 */
	public function picture() {
		if (!isset($this->{__METHOD__})) {
			/** @var array(string => mixed) $response */
			$response = $this->request('picture', ['redirect' => 'false']);
			/** @var string $result */
			$result = df_a_deep($response, 'data/url');
			df_result_string($result);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function tokenForBusiness() {return $this->r('token_for_business');}

	/** @return string */
	public function url() {return $this->r('link');}

	/**
	 * 2015-10-10
	 * Полученный нами от браузера идентификатор пользователя Facebook
	 * не является глобальным: он разный для разных приложений.
	 * @return string
	 */
	private function appScopedId() {return $this[self::$P__APP_SCOPED_ID];}

	/** @return string */
	private function emailRaw() {return $this->r('email');}

	/**
	 * @param string $key
	 * @return string|null
	 */
	private function r($key) {return df_a($this->responseA(), $key);}

	/**
	 * @param string $path
	 * @param array(string => mixed) $params
	 * @return array(string => mixed)
	 * @throws Exception
	 */
	private function request($path, array $params) {
		/** @var string $fullPath */
		$fullPath = '/' . implode('/', df_clean(['v2.5', $this->appScopedId(), $path]));
		/** @var string $responseAsJson */
		$responseAsJson = $this->requestBasic($fullPath, $params + [
			'access_token' => $this->longLivedAccessToken(),
			'appsecret_proof' => hash_hmac(
				'sha256', $this->longLivedAccessToken(), Credentials::s()->appSecret()
			)
		]);
		/** @var array(string => mixed) $result */
		$result = json_decode($responseAsJson, true);
		df_result_array($result);
		/** @var array(string => string)|null $error */
		$error = df_a($result, 'error');
		if ($error) {
			throw new Exception($error);
		}
		return $result;
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
		/** @var \Zend\Http\Client\Adapter\Curl $adapter */
		$adapter = new \Zend\Http\Client\Adapter\Curl;
		$adapter->setCurlOption(CURLOPT_SSL_VERIFYPEER, false);
		/** @var \Zend\Http\Client $httpClient */
		$httpClient = new \Zend\Http\Client();
		$httpClient
			->setAdapter($adapter)
			->setHeaders(array())
			->setUri($uri)
			->setOptions(array('timeout' => 10))
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
	private function responseA() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->request('', [
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
			]);
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
		parent::_construct();
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

