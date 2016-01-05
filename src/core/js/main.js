/* 
Package: BathTime{r}
File: main.js
(c) 2015 Brooke Dukes All Rights Reserved

This is the main JavaScript file which is used to post via AJAX and make the inerface appear smoother

This file is under Creative Commons 3.0 BY-SA and not the GPL v3
http://creativecommons.org/licenses/by-sa/3.0/
See readme.md for more info
*/
$(document).ready(function() {
   // get device id and access token from the form. 
   var accessToken = $("#accesstoken").val();
   var deviceID = $("#deviceid").val();

   //check if device is online:
   photonConnect();

   // since JavaScript is enabled we do not need to post the form this file will do all that for us. 
   // This removes action and method from our forms 
   $('form#timer').removeAttr('action').removeAttr('method');
   $('form#access').removeAttr('action').removeAttr('method');


   //hide our results message div
   $(".result").hide();

   //show the form if the reset link has been clicked
   $(".reset-timer").click(function(event) {
      $('.result').hide();
      $('#timer').fadeIn(500);
      photonConnect();

      // Prevent default link action
      event.preventDefault();

   });


   /* ===== AJAX jQuery Code ==================================================
      The following AJAX request is a modified version of the code found here:	
      http://stackoverflow.com/a/5004276/172964
     ========================================================================== */ // 

   /* AJAX to set our Device ID and Acess Token */
   // Variable to hold request
   var request;

   // Bind to the submit event of our form
   $("#access").submit(function(event) {

      // Abort any pending request
      if (request) {
         request.abort();
      }
      // setup some local variables
      var $form = $(this);

      // Let's select and cache all the fields
      var $inputs = $form.find("input, select, button, textarea");

      // Serialize the data in the form
      var serializedData = $form.serialize();

      // Let's disable the inputs for the duration of the Ajax request.
      // Note: we disable elements AFTER the form data has been serialized.
      // Disabled form elements will not be serialized.
      $inputs.prop("disabled", true);

      // Fire off the request to /form.php
      request = $.ajax({
         url: "setAccess.php",
         method: "POST",
         data: serializedData
      });

      // Callback handler that will be called on success
      request.done(function(response, textStatus, jqXHR) {
         // Log a message to the console
         history.replaceState(null, null, '?d=' + encodeURIComponent($("#deviceid").val()) + '&a=' + encodeURIComponent($("#accesstoken").val()));
         $('#setAccess').hide();
         $('.result').removeClass('fail');
         $('.result').fadeIn(500).addClass('success').removeClass('fail');
         $('.message').html("Device ID and Access ID set.<br/> You may bookmark this URL for later use.");
         $('#timer').css({
            "display": "block"
         });
      });

      // Callback handler that will be called on failure
      request.fail(function(jqXHR, textStatus, errorThrown) {
         $('.result').removeClass('success');
         $('.result').addClass('fail').fadeIn(500);
         $('.message').html("Oops, an error has occurred, please try again. ");
      });

      // Callback handler that will be called regardless
      // if the request failed or succeeded
      request.always(function() {
         // Reenable the inputs
         $inputs.prop("disabled", false);
      });

      // Prevent default posting of form
      event.preventDefault();

   });


   /* Send Timer data to Particle */

   // Variable to hold request
   var request;

   // Bind to the submit event of our form
   $("#timer").submit(function(event) {
	   
   	  //validate a number bigger than 0 was entered
      if (!$.isNumeric($("#minutes").val()) || ($('#minutes').val() == 0)) {
         $('.result').removeClass('success');
         $('.result').addClass('fail').fadeIn(500);
         $('.message').html("Please enter a numeric value larger than 0");
         $('.reset-timer').hide();

         return false;
      }
      
      if (photonConnect() == false) {
         $('.result').removeClass('success');
         $('.result').addClass('fail').fadeIn(500);
         $('.message').html("Please wake the device");
         $('.reset-timer').hide();

         return false;
      }
    
      // Abort any pending request
      if (request) {
         request.abort();
      }
      // setup some local variables
      var $form = $(this);

      // Let's select and cache all the fields
      var $inputs = $form.find("input, select, button, textarea");

      // Serialize the data in the form
      var serializedData = $form.serialize();

      // Let's disable the inputs for the duration of the Ajax request.
      // Note: we disable elements AFTER the form data has been serialized.
      // Disabled form elements will not be serialized.
      $inputs.prop("disabled", true);
      $(".main section .loading-icon").html('<img src="img/request.gif" /> Sending Data...');
	  $("#timer").hide();
	  
	  photonConnect();

      // Fire off the request to /form.php
      request = $.ajax({
         url: "https://api.particle.io/v1/devices/" + deviceID + "/settimer",
         type: "POST",
         data: {
            args: $("#minutes").val(),
            access_token: accessToken
         },
         dataType: "json"
      });

      // Callback handler that will be called on success
      request.done(function(response, textStatus, jqXHR) {
	      
	   /* if (timerStatus == "true") {
	     $('.result').removeClass('success');
         $('.result').addClass('fail').fadeIn(500);
         $('.message').html("Please wait until the timer completes before sending a new request.");
         $('.reset-timer').hide();

         //return false;
         }*/
         
         // Log a message to the console
         $('.result').removeClass('fail');
         $('.result').fadeIn(500).addClass('success').removeClass('fail');
         $('.message').html("Timer successfully set.");
         $('.reset-timer').show().css("display", "block");
        
  
      });

      // Callback handler that will be called on failure
      request.fail(function(jqXHR, textStatus, errorThrown) {
         $('.result').removeClass('success');
         $('.result').addClass('fail').fadeIn(500);
         $('.message').html("Oops, an error has occurred, please try again.");
         $('.reset-timer').show().css("display", "block");

      });

      // Callback handler that will be called regardless
      // if the request failed or succeeded
      request.always(function() {
         // Reenable the inputs
         $inputs.prop("disabled", false);
      });
      photonConnect();
      
      $(".main section .loading-icon").html('');


      // Prevent default posting of form
      event.preventDefault();

   });

   //function to check photon's status	
   function photonConnect() {

      var photonConnected = [];
      $.ajax({
         url: "https://api.particle.io/v1/devices/" + deviceID + "?access_token=" + accessToken,
         async: false,
         type: "GET",
         dataType: 'json',
         success: function(data) {
            photonConnected = data.connected;
         }
      });
      //if offline update status
      if (photonConnected == false) {
         $(".status").html("<span class='genericon genericon-dot asleep'></span> Asleep").css('cursor', 'default');
      } else {
         //request to see if timer is running
         var timerStatus = [];
         $.ajax({
            url: "https://api.particle.io/v1/devices/" + deviceID + "/status?access_token=" + accessToken,
            async: false,
            type: "GET",
            dataType: 'json',
            success: function(data) {
               timerStatus = data.result;
            }
         });

         if (timerStatus == "false") {
            $(".status").html("<span class='genericon genericon-dot ready'></span> Ready<span class='shutdown'> (click to sleep)</span></span>").css(
               'cursor', 'pointer');
               
            $.ajax({
				url: "https://api.particle.io/v1/devices/" + deviceID + "/battery?access_token=" + accessToken,
				async: false,
				type: "GET",
				dataType: 'json',
				success: function(data) {
					$(".battery").html("Battery: " + Number(Math.round(data.result * 10) / 10).toFixed(1) + "%");
         		}
      		});

            $('.status').click(function(event) {
               $("#timer").hide(100);

               $(".main section .loading-icon").html('<img src="img/request.gif" /> Sending Data...');
               $.ajax({
                  url: "https://api.particle.io/v1/devices/" + deviceID + "/shutdown/",
                  type: "POST",
                  async: false,
                  data: {
                     args: "true",
                     access_token: accessToken
                  },
                  dataType: "json"
               });
               setTimeout(function() {
                  $(".main section .loading-icon").delay(5000).html('');
                  $(".status").delay(5000).html("<span class='genericon genericon-dot asleep'></span> Asleep").css('cursor', 'default');
                  $('.result').removeClass('fail');
                  $('.result').fadeIn(500).addClass('success').removeClass('fail');
                  $('.message').html("Timer set to sleep mode. Please wake the device before futher use");
                  $('.reset-timer').hide();

               }, 1000);
               setTimeout(function() {
                  $('#timer').fadeIn(500);
               }, 60000);

               event.preventDefault();


            });
         } // end false check
         else {
            $(".status").html("<span class='genericon genericon-dot ready'></span> Ready</span>").css(
               'cursor', 'default');
			}
      } // end  if connected check

      return photonConnected;

   }

//check the status of the photon and battery every minute
   setInterval(function() {
      photonConnect();
   }, 1000 * 60 * 1);


});