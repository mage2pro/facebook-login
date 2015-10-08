require(['jquery', 'jquery/jquery.cookie'], function($) {$(function() {
	window.dfeFacebookLogin = function() {
		var setStatus = function(status) {jQuery('#status').html(status);};
		FB.getLoginStatus(function(response) {
			switch (response.status) {
				case 'connected':
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
						/**
						 * 2015-10-08
						 * Этот параметр должен называться именно так!
						 * @used-by \Magento\Customer\Model\Account\Redirect::processLoggedCustomer()
						 * @link https://github.com/magento/magento2/blob/54b85e93af25ec83e933d851d762548c07a1092c/app/code/Magento/Customer/Model/Account/Redirect.php#L177-L183
						 */
						referer: window.location.href
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