require([
	'jquery'
	, 'Magento_Customer/js/customer-data'
], function($, customerData) {$(function() {
	/** @type {jQuery} HTMLLIElement */
	var $li = $('li.dfe-facebook-login');
	$li.removeAttr('style');
	window.dfeFacebookLogin = function() {
		// 2015-10-08
		// Скрываем кнопку, чтобы в процессе авторизации она не мелькала.
		$li.hide();
		FB.getLoginStatus(function(response) {
			debugger;
			switch (response.status) {
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
					var $form = $('<form/>').attr({
						action: $('meta[name=dfe_facebook_url_login]').attr('content')
						,method: 'post'
					});
					var addFields = function(fields) {
						for (var name in fields) {
							$form.append($('<input/>').attr({
								type: 'hidden', name: name, value: fields[name]
							}));
						}
					};
					addFields({
						url: window.location.href
						,token: response.authResponse.accessToken
						/** https://developers.facebook.com/docs/reference/javascript/FB.getLoginStatus */
						,user: response.authResponse.userID
					});
					$('body').append($form);
					$form.submit();
					break;
				/**
				 * 2015-10-10
				 * Так и не понял, как получить эти статусы.
				 * Вроде и отказывал приложению в авторизации,
				 * и отказывал Facebook в аутентификации,
				 * но в обоих этих случаях окно авторизации просто молча закрывается,
				 * а сюда управление не попадает.
				 */
				case 'not_authorized':
					$li.show();
					//setStatus('Please log into this app.');
					break;
				default:
					$li.show();
					//setStatus('Please log into Facebook.');
			}
		});
	};
});});