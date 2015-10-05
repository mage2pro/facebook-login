require([
	'jquery'
	, 'jquery/jquery.cookie'
], function($) {$(function() {
	var process = function() {};
	'undefined' !== typeof FB && FB.dfInitialized
		? process()
		: document.addEventListener('dfeFacebookInitialized', process)
	;
});});
window.dfeFacebookLogin = function() {
	var setStatus = function(status) {jQuery('#status').html(status);};
	FB.getLoginStatus(function(response) {
		switch (response.status) {
			case 'connected':
				var fields = [
					'email'
					,'first_name'
					,'gender'
					,'last_name'
					,'locale'
					,'middle_name'
					,'name'
					,'name_format'
					,'timezone'
				];
				FB.api('/me', {fields: fields}, function(response) {
					console.log(response);
					setStatus(response.name);
			    });
				break;
			case 'not_authorized':
				setStatus('Please log into this app.');
				break;
			default:
				setStatus('Please log into Facebook.');
		}
	});
};