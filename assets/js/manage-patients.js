jQuery( document ).ready( function( $ ) {
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

    $('#add-new-patient-submit').on('click', function() {
        $('#new-patient-modal').foundation('reveal', 'close');

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
        //        $('#modify-courses-' + patientId).foundation('reveal', 'open');
        //    }
        //});
    });
});