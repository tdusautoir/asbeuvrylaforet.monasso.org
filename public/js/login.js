			// résolution pb. height mobile iOS/android

			const appHeight = () => {
				const doc = document.documentElement;
				doc.style.setProperty('--app-height', `${window.innerHeight}px`);
			}
			window.addEventListener('resize', appHeight);
			appHeight();
			
			//******
			
			
			$("#mail").change(function(){
				if ($("#mail").val().length > 0){
					$("#mail").parent('div').addClass('field_label_top_filled');
				}
				if ($("#mail").val().length == 0) {
					$("#mail").parent('div').removeClass('field_label_top_filled');
				};
			});
			
			$("#password").change(function(){
				if ($("#password").val().length > 0){
					$('#password').parent('div').addClass('field_label_top_filled');
				}
				if ($("#password").val().length == 0) {
					$('#password').parent('div').removeClass('field_label_top_filled');
				};
			});
			$("#new_password").change(function(){
				if ($("#new_password").val().length > 0){
					$('#new_password').parent('div').addClass('field_label_top_filled');
				}
				if ($("#new_password").val().length == 0) {
					$('#new_password').parent('div').removeClass('field_label_top_filled');
				};
			});
			$( "input" ).focus(function() {
  				$(this).parent('div').addClass('field_label_top_focused');
				$(this).addClass('field_label_top_focused');
			});
			$("input").focusout(function(){
 				$(this).parent('div').removeClass('field_label_top_focused');
				$(this).removeClass('field_label_top_focused');
			});

			//fonction pour voir/cacher le mot de passe
			$('.view_password_link').click(function(){
			if ($('.view_password_link i').hasClass('fa-eye')){
				$('.view_password_link i').removeClass('fa-eye').addClass('fa-eye-slash');
				$('#password').attr('type', 'text');
			} else if ($('.view_password_link i').hasClass('fa-eye-slash')){
				$('.view_password_link i').removeClass('fa-eye-slash').addClass('fa-eye');
				$('#password').attr('type', 'password');
			}
			});
		

		//validation email
		function validateEmail(){
			// get value of input email
			var email=$("#mail").val();
			// use reular expression
			var reg = /^\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/
			if(reg.test(email)){
				return true;
			}else{
				return false;
			}

		}
		// use keyup event on email field
		$("#mail").keyup(function(){
			if(validateEmail()){
				// if the email is validated
				// set input email border green
				$("#mail").css("border-bottom","1px solid #e6e6e6");
				$(".form_field_error_mail").hide();
				// and set label 
			} else{
				// if the email is not validated
				// set border red
					$(".form_field_error_mail span").html("Veuillez saisir une adresse e-mail valide.");
					$("#mail").css("border-bottom","2px solid #d20000");
					$(".form_field_error_mail").show();
			}
		});
		$("#password").focusout(function(){
			if($("#password").val().length == 0){
			$(".form_field_error_password span").html("Saisissez votre mot de passe.");
			$("#password").css("border-bottom","2px solid #d20000");
			$(".form_field_error_password").show();
			} else{
				$("#password").css("border-bottom","1px solid #e6e6e6");
				$(".form_field_error_password").hide();
			}
		});
		$("#password").keyup(function(){
			if($("#password").val().length > 0){
				$("#password").css("border-bottom","1px solid #e6e6e6");
				$(".form_field_error_password").hide();
			}
		});
		$("#mail").focusout(function(){
			if($("#mail").val().length == 0){
			$(".form_field_error_mail span").html("Saisissez votre adresse e-mail.");
			$("#mail").css("border-bottom","2px solid #d20000");
			$(".form_field_error_mail").show();
			} else if(validateEmail()){
				$("#mail").css("border-bottom","1px solid #e6e6e6");
				$(".form_field_error_mail").hide();
			}
		});