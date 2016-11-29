define([
	'df'
	,'Df_Core/my/redirectWithPost'
	,'jquery'
	,'Magento_Customer/js/customer-data'
], function(df, redirectWithPost, $, customerData) {return (
	/**
	 * @param {Object} config
	 * @param {String} config.redirect
	 * @param {String} config.selector
	 * @param {?String} config.style
	 * @param {String} config.type
	 * @param {HTMLAnchorElement} element
	 * @returns void
	 */
	function(config, element) {
		/**
		 * 2016-11-28
		 * Система клонирует меню из блока «header.links» в видимый только в мобильном режиме
		 * (но присутствующий в DOM и в настольном режиме) блок «store.links»:
				$('.panel.header > .header.links').clone().appendTo('#store\\.links');
		 * https://github.com/magento/magento2/blob/2.1.2/app/design/frontend/Magento/blank/web/js/theme.js#L26-L26
		 * https://mage2.pro/t/2336
		 * По этой причине у нас сразу 2 одинаковых кнопки в шапке: одна видимая и вторая — невидимая.
		 * Обе эти кнопки инициализируются независимо (сюда мы попадаем для каждой из этих кнопок отдельно),
		 * но имеют одинаковые идентификаторы.
		 * При этом код document.getElementById('<идентификатор>') или $('#<идентификатор>')
		 * вернёт только первую из кнопок.
		 * Найти вторую можно по селектору: $(config.selector)
		 * При этом такой поиск по селектору может вернуть и третью кнопку,
		 * потому что на страницах регистрации и аутентификации наша кнопка аутентификации
		 * может быть одновременно расположена как в шапке, так и над блоком регистрации/аутентификации.
		 */
		/** @type {jQuery} HTMLAnchorElement */
		var $c = $(element);
		if ($c.closest('.nav-sections').length) {
			element.id += '-nav-sections';
		}
		else if ($c.closest('.page-header').length) {
			element.id += '-page-header';
		}
		window.dfeFacebookLogin = window.dfeFacebookLogin || function() {
			/** @type {jQuery} HTMLDivElement[] */
			var $cs = $(config.selector);
			// 2015-10-08
			// Скрываем кнопку, чтобы в процессе аутентификации она не мелькала.
			// 2016-11-27
			// На странице может быть расположено сразу 2 кнопки аутентификации Facebook:
			// в шапке и, например, в в блоке регистрации.
			// Скрывать надо все эти кнопки, а не только нажатую.
			$cs.hide();
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
						$cs.show();
				}
			});
		};
		// 2015-10-08
		// Чтобы кнопка при авторизации не елозила по экрану.
		// http://www.question2answer.org/qa/15546/facebook-changed-height-login-button-template-design-breaks?show=15561#a15561
		$c.removeAttr('style');
		switch (config.type) {
			case 'L':
			case 'U':
				$c.click(window.dfeFacebookLogin);
				break;
			case 'N':
				$c.css({display: 'inline-block', 'margin-right': '15px', width: '50px'});
		}
	});
});