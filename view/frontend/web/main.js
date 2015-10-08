require([
	'jquery'
	, 'Magento_Customer/js/customer-data'
], function($, customerData, sectionConfig) {$(function() {
	window.dfeFacebookLogin = function() {
		var setStatus = function(status) {jQuery('#status').html(status);};
		FB.getLoginStatus(function(response) {
			switch (response.status) {
				case 'connected':
					/**
					 * 2015-10-08
					 * Мы вынуждены вручную удалять устаревшие данные посетителя из Local Storage,
					 * потому что стандартный способ ядра у нас работать не будет.
					 * Стандартный способ ядра смотрите здесь:
					 * @link https://github.com/magento/magento2/blob/54b85e93af25ec83e933d851d762548c07a1092c/app/code/Magento/Customer/view/frontend/web/js/customer-data.js#L197-L204
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
						/** @link https://developers.facebook.com/docs/reference/javascript/FB.getLoginStatus */
						,user: response.authResponse.userID
					});
					$('body').append($form);
					$form.submit();
					break;
				case 'not_authorized':
					setStatus('Please log into this app.');
					break;
				default:
					setStatus('Please log into Facebook.');
			}
		});
	};
});});