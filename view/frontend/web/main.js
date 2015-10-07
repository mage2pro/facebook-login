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
						accessToken: response.authResponse.accessToken
						,url: window.location.href
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