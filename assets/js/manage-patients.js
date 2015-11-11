jQuery( document ).ready( function( $ ) {
    $(function() {
        //verify the input by the user when adding a new patient
        verifyPatientInput();

        $( "#available-courses, #user-courses" ).sortable({
            connectWith: ".connectedSortable",
            revert: true
        }).disableSelection();

        $("#update-patient-courses-submit").on('click', function(e) {
            e.preventDefault();

            var patientId = $("#patientId").val();
            var courseIds = $("#user-courses").sortable("toArray");
            var data = {
                action: 'i4_lms_handle_update_patient_courses',
                patient_id: patientId,
                courses: courseIds
            };

            $.post(wpcw_js_consts_fe.ajaxurl, data);
        });

        var newPatientForm = $j('#add-new-patient-form form');
        var newPatientId;

        // The submit button.
        $('#add-new-patient-submit').on('click', function(e){
            e.preventDefault();
            $('#new-patient-modal').foundation('reveal', 'close');

            var i4_patient_email = $j('#patient_email').val(); //retrieve the patients email
            var i4_patient_username = $j('#patient_username').val(); //retrieve the patients email
            var i4_patient_firstname = $j('#patient_fname').val();
            var i4_patient_lastname = $j('#patient_lname').val();

        // Trigger AJAX request to allow the user to retake the quiz.
          var data = {
            action              : 'i4_lms_handle_add_new_patient',
            security            : wpcw_js_consts_fe.new_patient_nonce,
            patient_email       : i4_patient_email,
            patient_username    : i4_patient_username,
            patient_fname       : i4_patient_firstname,
            patient_lname       : i4_patient_lastname

            };

        jQuery.post(wpcw_js_consts_fe.ajaxurl, data, function(response)
          {
              if( response.status == 200 ){
                  newPatientId = response.patient_id;
                  $('#modify-courses-2').foundation('reveal', 'open');
                  alert(newPatientId);
              }

          }, 'json');
        });


        //$('#add-new-patient-submit').on('click', function() {
        //    $('#new-patient-modal').foundation('reveal', 'close');

            // Create new patient
            //var patientId = -1;
            //var data = {
            //    action: 'i4_lms_handle_create_patient',
            //    email: ...,
            //    name: ...,
            //    ...
            //};
            //$.post(wpcw_js_consts_fe.ajaxurl, data, function(response) {
            //    if (response is success) {
            //        // Get patient ID and open modal
            //        patientId = response.patientId;
            //        $('#modify-courses-2').foundation('reveal', 'open');
            //    }
            //});
        //});
    });

   /**
    * Verifies patient information prior to allowing the new patient to be inserted
    *
    */
    function verifyPatientInput(){

        //set the emailCheck and usernameCheck to false before we do anything
        var emailCheck = false;
        var usernameCheck = false;

        //declare the patient's email and username variables
        var i4_patient_email;
        var i4_patient_username;

        var nextButton = $j('#add-new-patient-submit'); //store the nextButton element

        nextButton.prop( "disabled", true ); //lets disable the button immediately.

        //the patient email field changes
        $j("#patient_email").change(function (e) {

            emailCheck = false; //assume the email is false everytime we begin this
            nextButton.prop( "disabled", true ); //disable the button in case it was enabled previously

            i4_patient_email = $j(this).val(); //retrieve the patients email

            var data = {
             action           : 'i4_lms_handle_check_email',
             security         : wpcw_js_consts_fe.new_patient_nonce,
             patient_email    : i4_patient_email
            };


            jQuery.post(wpcw_js_consts_fe.ajaxurl, data, function(response)
               {
                   $j('#i4_email_availability_status').html(response.icon);

                   if(response.status == 200 ){ //OK response
                        emailCheck = true;
                   }

                   if(usernameCheck && emailCheck){
                       nextButton.prop("disabled", false);
                   }
               }, 'json');

        }); //end patient email field changes

        //the patient username field changes
        $j("#patient_username").change(function (e) {
            usernameCheck = false; // assume the username is false everytime this field is changed
            nextButton.prop( "disabled", true ); //disable the button in case it was enabled previously

            i4_patient_username = $j(this).val(); //retrieve the patients email

            var data = {
                action                 : 'i4_lms_handle_check_username',
                security               : wpcw_js_consts_fe.new_patient_nonce,
                patient_username       : i4_patient_username
            };

            jQuery.post(wpcw_js_consts_fe.ajaxurl, data, function(response)
            {
                $j('#i4_username_availability_status').html(response.icon);

                if(response.status == 200 ){ //OK response
                     usernameCheck = true;
                }

                if(usernameCheck && emailCheck){
                    nextButton.prop("disabled", false);
                }
            }, 'json');

        });

   }
});
