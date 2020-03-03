/*
 * Get the site key query parameter from multi_captcha.js script src address
 */
var url_query = $("script[src*='/multi_captcha.js']").attr('src').split('?');
var url_query_params = url_query[1].split('&');
var params = {};
url_query_params.forEach(function(item){
	var param = item.split("=");
	params[param[0]] = param[1];
});

/* array variable to store all reCaptchas unique ids */
var renderCaptchaIds = [];
/* Get the reCaptcha api and call the onload method */
$.getScript("https://www.google.com/recaptcha/api.js?onload=initCaptcha&render=explicit&hl="+params["locale"]);	

/* Function to render multiple recaptchas after the api is loaded */
function initCaptcha() {

	$('.g-recaptcha').each(function() {
		renderCaptchaIds[$(this).attr('id')] = grecaptcha.render($(this).attr('id'), { 'sitekey': params['site-key'] }); 
	});
}

$(document).ready(function() {

	/* Set ajax error response as false by default */
	var invalid_captcha = false;

	/* ajaxDone function to reset reCaptcha component on successfull ajax submission */
	$("form").on('ajaxDone',function(event,request,options,text) {

		var form_triggered = $(event.target).closest('form');
		resetCaptcha(form_triggered);
	});

	/* ajaxFail function to display validation error message */
	$("form").on('ajaxFail',function(event, request, options, text) {

		var form_triggered = $(event.target).closest('form');

		if($(form_triggered).find('.g_recaptcha_error_response').length > 0 && text && text.responseJSON && text.responseJSON.result && text.status === 406) {
			
			var error_response;
			try	{
				error_response = JSON.parse(text.responseJSON.result);
			}
			catch(exception) {
				error_response = '';
			}
			
			if(error_response && error_response.input_request && error_response.input_request === 'g-recaptcha-response' && error_response.message) {
				$(form_triggered).find('.g_recaptcha_error_response').html(error_response.message);
				invalid_captcha = true;
			}
		}

		resetCaptcha(form_triggered);
	});

	function resetCaptcha(form_triggered) {

		if($(form_triggered).find('.g-recaptcha').length > 0) {

			$(form_triggered).find('.g-recaptcha').each(function() {
				grecaptcha.reset(renderCaptchaIds[$(this).attr('id')]);
			});

			if(!invalid_captcha) {
				$(form_triggered).find('.g_recaptcha_error_response').html('');
			}
			else {
				invalid_captcha = false;
			}
		}
	}
});
