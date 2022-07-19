"use strict";
jQuery(function($) {


	$('.wpr_model_selector').find('.error').hide();
	$('.wpr_model_selector').find('.wpr-spinner').hide();
	$('.wpr_field_icon').hide();


	var LoginId = $('.wpr-get-login-class').attr('data-login-menu');

	var LoginUrl = $('.wpr-get-login-url').attr('data-login-url');
	if (LoginUrl) {

		var last_url = LoginUrl.split('/');
		var lastSegmenturl = last_url.pop() || last_url.pop();
		$(document).ready(function() {
			if (window.location.href.indexOf(lastSegmenturl) > -1) {
				$('#menuclass_id').modal('show');
			}
		});
	}


	var RegId = $('.wpr-get-account-class').attr('data-account-menu');



	//login model show
	$('.' + LoginId).on('click', function(e) {
		e.preventDefault();

		$('#menuclass_id').modal('show');
	});

	$('.' + RegId).on('click', function(e) {
		e.preventDefault();

		$('#menuclass_id').modal('show');
	});

	// registration form check confirm password 
	$('.wpr-pr-pass-wrapper').find('.wpr-pass-confirm-alert').hide();


	// // Submitting rgistration form 
	$(".wpr-forms").submit(function(e) {
		e.preventDefault();
		var is_validated = wpr_validate_data_form();

		var password = jQuery('#password').val();
		var password = jQuery('#password').val();
		var confirm_password = jQuery('#wpr_confirm_password').val();

		if (password != confirm_password && confirm_password != undefined) {
			var check_pass = '_ok_pass';
			is_validated = false;
		}


		if (is_validated) {
			$(this).find('.error').hide();
			$(this).find('.wpr-spinner').show();
			$(this).find('.wpr_sub_form input').prop('disabled', true);
			$(this).find('.wpr-pr-spinner-wrapper input').prop('disabled', true);

			var data = $(this).serialize();
			$.post(wpr_vars.ajax_url, data, function(data) {

				$('.wpr_model_selector').find('.wpr-spinner').hide();
				$('.wpr_model_selector').find('.wpr_sub_form input').prop('disabled', false);

				if (data.signup == 'signup') {
					WPR.alert(data.message, data.status, function() {
						if (data.status == 'error') return;

						var signup_url = data.redirect_url_signup;
						if (signup_url != null) {
							window.location.href = signup_url;
						}
						else {
							$('.wpr_model_selector').hide();
							$('.wpr-form-title').html(data.message);
						}
					});
				}
				else {
					WPR.alert(data.message, data.status);
				}

			}, 'json');
		}
		else {
			// if (confirm_password == null) continue;
			if (check_pass == '_ok_pass') {
				$(this).find(".error").html('Password did not match').show();

			}
			else {

				$(this).find(".error").html(wpr_vars.error_msg).show();
			}
		}
	});


	$('#password').keyup(function() {
		$('#password_result').html(checkStrength($('#password').val()))
	});

	/*Password Strenght Module*/

	function checkStrength(password) {
		var strength = 0
		if (password.length < 6) {
			jQuery('#password_result').removeClass()
			jQuery('#password_result').addClass('short')
			return 'Too short'
		}
		if (password.length > 7) strength += 1
		// If password contains both lower and uppercase characters, increase strength value.
		if (password.match(/([a-z].*[A-Z])|([A-Z].*[a-z])/)) strength += 1
		// If it has numbers and characters, increase strength value.
		if (password.match(/([a-zA-Z])/) && password.match(/([0-9])/)) strength += 1
		// If it has one special character, increase strength value.
		if (password.match(/([!,%,&,@,#,$,^,*,?,_,~])/)) strength += 1
		// If it has two special characters, increase strength value.
		if (password.match(/(.*[!,%,&,@,#,$,^,*,?,_,~].*[!,%,&,@,#,$,^,*,?,_,~])/)) strength += 1
		// Calculated strength value, we can return messages
		// If value is less than 2
		if (strength < 2) {
			jQuery('#password_result').removeClass()
			jQuery('#password_result').addClass('weak')
			return 'Weak'
		}
		else if (strength == 2) {
			jQuery('#password_result').removeClass()
			jQuery('#password_result').addClass('good')
			return 'Good'
		}
		else {
			jQuery('#password_result').removeClass()
			jQuery('#password_result').addClass('strong')
			return 'Strong'
		}
	}





	$('.wpr_field_selector').each(function(i, meta_field) {
		var data_name = $(meta_field).attr('data-key');
		var input_control = $('#' + data_name);
		var type = $(meta_field).attr('data-type');

		if (type === 'select') {

			$("#" + data_name).select2({
				placeholder: "Select",
				allowClear: true,
				width: "100%"
			});
		}
	});

	$('.wpr_field_wrapper').each(function(i, meta_field) {
		var required = $(meta_field).find('.wpr_field_data').attr('data-req');

		if (required == 'on') {
			$(meta_field).find('.wpr_field_icon').show();
		}
	});

});


//jQuery time
var counter = 2;
var count2 = 1;
var fieldset_length = jQuery('.wpr-step').length;
jQuery('.action-button-previous').hide();
jQuery('.wpr-action-submit').hide();

if (fieldset_length > 0) {
	var currentTab = 0; // Current tab is set to be the first tab (0)
	showTab(currentTab); // Display the current tab
}

	function showTab(n) {
		// This function will display the specified tab of the form...
		var x = document.getElementsByClassName("wpr-tab-reg");
		x[n].style.display = "block";
		//... and fix the Previous/Next buttons:
		if (n == 0) {
			document.getElementById("prevBtn").style.display = "none";
		}
		else {
			document.getElementById("prevBtn").style.display = "inline";
		}
		if (n == (x.length - 1)) {
			document.getElementById("nextBtn").style.display = "none";
			document.getElementById("submit_btn").style.display = "inline";
		}
		else {
			document.getElementById("nextBtn").style.display = "inline";
			document.getElementById("nextBtn").innerHTML = "Next";
			document.getElementById("submit_btn").style.display = "none";
		}
		//... and run a function that will display the correct step indicator:
		fixStepIndicator(n)
	}

	function nextPrev(n) {
		// This function will figure out which tab to display
		var x = document.getElementsByClassName("wpr-tab-reg");
		// Exit the function if any field in the current tab is invalid:

		if (n == 1 && !validateForm()) return false;
		// Hide the current tab:
		x[currentTab].style.display = "none";
		// Increase or decrease the current tab by 1:
		currentTab = currentTab + n;
		// if you have reached the end of the form...
		if (currentTab >= x.length) {
			// ... the form gets submitted:
			document.getElementById("regForm").submit();
			return false;
		}
		// Otherwise, display the correct tab:
		showTab(currentTab);
	}

	function validateForm() {
		var is_validated = wpr_validate_data_form();
		var resp = false;

		if (is_validated) {
			resp = true;
		}
		else {
			jQuery(this).find(".error").html(wpr_vars.error_msg).show();

		}

		return resp;
	}

	function fixStepIndicator(n) {
		// This function removes the "active" class of all steps...
		var i, x = document.getElementsByClassName("step");
		for (i = 0; i < x.length; i++) {
			x[i].className = x[i].className.replace(" active", "");
		}
		//... and adds the "active" class on the current step:
		x[n].className += " active";
	}

	function wpr_validate_data_form() {

		var has_error = true;
		jQuery('.wpr_field_selector').each(function(i, meta_field) {

			var data_name = jQuery(meta_field).attr('data-key');
			var type = jQuery(meta_field).attr('data-type');
			var input_control = jQuery("#" + data_name);
			var required = jQuery(meta_field).find('.wpr_field_data').attr('data-req');
			var msg = jQuery(meta_field).find('.wpr_field_data').attr('data-message');
			var max_checked = jQuery(meta_field).find('.wpr_field_data').attr('data-max');
			var min_checked = jQuery(meta_field).find('.wpr_field_data').attr('data-min');

			var accept_pass = jQuery(meta_field).find('.wpr_field_data').attr('data-pass');


			// password check confirm



			if (msg == '') {
				msg = 'It is required!';
			}



			if (type === 'text' || type === 'textarea' || type === 'color' || type === 'select' || type === 'email' || type === 'date' || (type === "wp" && data_name1 !== "password")) {
				if (required === 'on' && jQuery(input_control).val() === '') {

					jQuery(input_control).closest('.wpr_field_wrapper').find('span.wpr_field_error').html(msg).css('color', 'red');
					has_error = false;
				}
				else {
					jQuery(input_control).closest('.wpr_field_wrapper').find('span.wpr_field_error').html('').css({ 'border': '', 'padding': '0' });
				}

			}
			else if (type === 'radio') {
				if (required === "on" && jQuery('input:radio[name="wpr[radio][' + data_name + ']"]:checked').length === 0) {
					jQuery('input:radio[name="wpr[radio][' + data_name + ']"]').closest('.wpr_field_wrapper').find('span.wpr_field_error').html(msg).css('color', 'red');
					has_error = false;
				}
				else {
					jQuery('input:radio[name="wpr[radio][' + data_name + ']"]').closest('.wpr_field_wrapper').find('span.wpr_field_error').html('').css({ 'border': '', 'padding': '0' });
				}

			}
			else if (type === 'checkbox') {

				if (required === "on" && jQuery('input:checkbox[name="wpr[checkbox][' + data_name + '][]"]:checked').length === 0) {

					jQuery('input:checkbox[name="wpr[checkbox][' + data_name + '][]"]').closest('.wpr_field_wrapper').find('span.wpr_field_error').html(msg).css('color', 'red');
					has_error = false;
				}
				else if (min_checked != '' && jQuery('input:checkbox[name="wpr[checkbox][' + data_name + '][]"]:checked').length < min_checked) {
					jQuery('input:checkbox[name="wpr[checkbox][' + data_name + '][]"]').closest('.wpr_field_wrapper').find('span.wpr_field_error').html(msg).css('color', 'red');
					has_error = false;
				}
				else if (max_checked != '' && jQuery('input:checkbox[name="wpr[checkbox][' + data_name + '][]"]:checked').length > max_checked) {
					jQuery('input:checkbox[name="wpr[checkbox][' + data_name + '][]"]').closest('.wpr_field_wrapper').find('span.wpr_field_error').html(msg).css('color', 'red');
					has_error = false;
				}
				else {

					jQuery('input:checkbox[name="wpr[checkbox][' + data_name + '][]"]').closest('.wpr_field_wrapper').find('span.wpr_field_error').html('').css({ 'border': '', 'padding': '0' });

				}
			}
			else if (type === 'masked') {

				if (required === "on" && (jQuery(input_control).val() === '')) {
					jQuery(input_control).closest('.wpr_field_wrapper').find('span.wpr_field_error').html(msg).css('color', 'red');
					has_error = false;
				}
				else {
					jQuery(input_control).closest('.wpr_field_wrapper').find('span.wpr_field_error').html('').css({ 'border': '', 'padding': '0' });
				}
			}
			else if (type === 'autocomplete') {

				if (required == 'on' && jQuery(input_control).val() == null) {
					jQuery(input_control).closest('.wpr_field_wrapper').find('span.wpr_field_error').html(msg).css('color', 'red');
					has_error = false;
				}
				else {
					jQuery(input_control).closest('.wpr_field_wrapper').find('span.wpr_field_error').html('').css({ 'border': '', 'padding': '0' });
				}
			}
			// else if (type === 'password') {
			// var input_control = jQuery('#'+meta['data_name']);

			// if (accept_pass === "on" && (checkStrength(jQuery('#password').val()) == 'Weak' || checkStrength(jQuery('#password').val()) == 'Too short')) {
			// 	jQuery(input_control).closest('p').find('span.errors').html('Set Strong Password').css('color', 'red');
			// 	has_error = true;
			// 	// error_in = meta['data_name'];
			// }

			// }


		});
		return has_error;
	}
//}
