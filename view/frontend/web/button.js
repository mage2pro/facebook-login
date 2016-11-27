define([
	'df'
	,'Df_Core/my/redirectWithPost'
	,'jquery'
	,'Magento_Customer/js/customer-data'
], function(df, redirectWithPost, $, customerData) {return (
	/**
	 * @param {Object} config
	 * @param {String} config.domId
	 * @param {String} config.redirect
	 * @param {String} config.type
	 * @returns void
	 */
	function(config) {
		/** @type {jQuery} HTMLDivElement */
		var $container = $(document.getElementById(config.domId));
		// 2015-10-08
		// Чтобы кнопка при авторизации не елозила по экрану.
		// http://www.question2answer.org/qa/15546/facebook-changed-height-login-button-template-design-breaks?show=15561#a15561
		$container.removeAttr('style');
		if ('N' === config.type) {
			$container.css({display: 'inline-block', 'margin-right': '15px', width: '50px'});
		}
		window.dfeFacebookLogin = function() {
			// 2015-10-08
			// Скрываем кнопку, чтобы в процессе авторизации она не мелькала.
			$container.hide();
			// 2016-11-26
			// https://developers.facebook.com/docs/facebook-login/web#checklogin
			FB.getLoginStatus(function(response) {
				switch (response.status) {
					// 2016-11-26
					// «The person is logged into Facebook, and has logged into your app.»
					case 'connected':
						/**
						 * 2015-10-08
						 * Мы вынуждены вручную удалять устаревшие данные посетителя из Local Storage,
						 * потому что стандартный способ ядра у нас работать не будет.
						 * Стандартный способ ядра смотрите здесь:
						 * https://github.com/magento/magento2/blob/54b85e93af25ec83e933d851d762548c07a1092c/app/code/Magento/Customer/view/frontend/web/js/customer-data.js#L197-L204
						 * Стандартный способ на отсылку форм на сервер анализирует адрес отсылки,
						 * и по этому адресу определяет, какие ключи Local Storage устарели.
						 * Мы то же самое делаем вручную.
						 */
						customerData.invalidate(['*']);
						redirectWithPost(config.redirect, {
							token: response.authResponse.accessToken
							/**
							 * 2016-06-05
							 * Запоминаем адрес страницы, на которой находился посетитель
							 * непосредственно перед авторизацией.
							 * Когда сервис авторизации вернёт посетителя обратно в наш магазин,
							 * мы перенаправим посетителя на эту страницу.
							 * https://code.dmitry-fedyuk.com/m2e/facebook-login/blob/7c2b601d/Controller/Index/Index.php#L50
							 */
							,url: window.location.href
							// https://developers.facebook.com/docs/reference/javascript/FB.getLoginStatus
							,user: response.authResponse.userID
						});
						break;
					/**
					 * 2015-10-10
					 * Так и не понял, как получить эти статусы.
					 * Вроде и отказывал приложению в авторизации,
					 * и отказывал Facebook в аутентификации,
					 * но в обоих этих случаях окно авторизации просто молча закрывается,
					 * а сюда управление не попадает.
					 * 2016-11-26
					 * «The person is logged into Facebook, but has not logged into your app.»
					 */
					case 'not_authorized':
					// 2016-11-26
					// «The person is logged into Facebook, but has not logged into your app.»
					case 'unknown':
					default:
						$container.show();
				}
			});
		};
	});
});