jQuery(document).ready(function ($) {
    $(function() {
        $(document).confirmWithReveal({
            ok_class: 'button blue confirm-button',
            cancel_class: 'button secondary cancel-button',
            footer_class: 'confirm-buttons'
        });

        //verify the input by the user when adding a new patient
        verifyPatientInput();

        $('#available-courses, #user-courses').sortable({
            connectWith: ".connectedSortable",
            revert: true
        }).disableSelection();

        var managePatientsTable = $('.manage-patients-table');
        // When clicking the modify courses button
        $(managePatientsTable).on('click', '.fa-list', function () {
            var patientId = $(this).closest('tr').attr('id');
            var patientName = $(this).closest('td').siblings('.patient-name').text();
            showModifyCoursesModal(patientId, patientName);
        });

        // When clicking the edit patient button
        $(managePatientsTable).on('click', '.fa-pencil', function () {
            var patientId = $(this).closest('tr').attr('id');
            //editPatient(patientId);
        });

        // When clicking the remove patient button
        $(managePatientsTable).on('click', '.fa-times', function () {
            var patientName = $(this).closest('td').siblings('.patient-name').text();
            $(this).closest('a').attr('data-confirm', '{"title": "Are you sure you want to remove <i>' + patientName + '</i>?"}');
        });

        // Handle the event where the user has confirmed the deletion of the patient
        $('.remove-patient').on('confirm.reveal', function() {
            var patientId = $(this).closest('tr').attr('id');
            var data = {
                action: 'i4_lms_remove_patient',
                patientId: patientId
            };
            $.post(wpcw_js_consts_fe.ajaxurl, data, function() {
                // Remove the confirm modal
                $('.reveal-modal').remove();
                $('.reveal-modal-bg').hide();

                // Remove the patient
                $('#' + patientId).remove();
            });
        });

        $('#update-patient-courses-submit').on('click', function (e) {
            e.preventDefault();

            var courseIds = $("#user-courses").sortable("toArray");
            var patientId = $("#patientId").val();

            var data = {
                action: 'i4_lms_handle_update_patient_courses',
                patientId: patientId,
                courses: courseIds
            };

            $.post(wpcw_js_consts_fe.ajaxurl, data, function () {
//                var patientCourses = $('#' + patientId).find('.patient-courses');
//                patientCourses.empty();
                $('#modify-courses-modal').foundation('reveal', 'close');
            });
        });

        // The new user submit button.
        $('#add-new-patient-submit').on('click', function (e) {
            e.preventDefault();

            var i4_patient_email = $('#patient_email').val(); //retrieve the patients email
            var i4_patient_username = $('#patient_username').val(); //retrieve the patients email
            var i4_patient_firstname = $('#patient_fname').val();
            var i4_patient_lastname = $('#patient_lname').val();

            // Trigger AJAX request to allow the user to retake the quiz.
            var data = {
                action: 'i4_lms_handle_add_new_patient',
                security: wpcw_js_consts_fe.new_patient_nonce,
                patient_email: i4_patient_email,
                patient_username: i4_patient_username,
                patient_fname: i4_patient_firstname,
                patient_lname: i4_patient_lastname
            };

            $.post(wpcw_js_consts_fe.ajaxurl, data, function (response) {
                if (response.status == 200) {
                    var patient = {
                        id: response.patient_id,
                        name: i4_patient_firstname + " " + i4_patient_lastname,
                        email: i4_patient_email
                    };

                    $.when(showModifyCoursesModal(patient)).done(function () {
                        insertPatient(patient);
                        // Hide the new user modal
                        $('#new-patient-modal').foundation('reveal', 'close');
                    });
                }
            }, 'json');
        });

        function clearModifyCoursesModal() {
            // Unset the patient ID and name in the modal
            $('#patientId').removeAttr('value');
            $('#modifyCoursesTitle').children('i').empty();

            // Unset the courses in the sortable
            $('#available-courses').empty();
            $('#user-courses').empty();
        }

        function showModifyCoursesModal(patient) {
            clearModifyCoursesModal();

            var data = {
                action: 'i4_lms_get_user_courses',
                patientId: patient.id
            };
            $.get(wpcw_js_consts_fe.ajaxurl, data, function (response) {
                // Set the patient ID and name in the modal
                $('#patientId').val(patient.id);
                $('#modifyCoursesTitle').children('i').html(patient.name);

                // Set the courses in the sortables
                var unassignedCourses = $('#available-courses');
                $.each(response.unassigned_courses, function (id, name) {
                    var li = courseToListItem(id, name);
                    unassignedCourses.append(li);
                });

                var assignedCourses = $('#user-courses');
                $.each(response.assigned_courses, function (id, name) {
                    var li = courseToListItem(id, name);
                    assignedCourses.append(li);
                });

                // Open the modify courses modal
                $('#modify-courses-modal').foundation('reveal', 'open');
            }, 'json');
        }

        function courseToListItem(id, name) {
            var li = document.createElement("li");
            $(li).attr('id', id).text(name);
            return li;
        }

        function insertPatient(patient) {
            var patientRow = createRow(patient);

            var patientName = patient.name.toLowerCase();
            var names = $('td:first-child').map(function () {
                return $(this).text().toLowerCase()
            });
            names.push(patientName);
            var sorted = $.makeArray(names.sort());
            var insertIndex = sorted.indexOf(patientName);
            $('tr:nth-child(' + (insertIndex + 1) + ')', managePatientsTable).before(patientRow);
        }

        function createRow(patient) {
            var tr = document.createElement('tr');
            $(tr).attr('id', patient.id);

            var name = createCell(patient.name, 'patient-name');
            var email = createCell(patient.email, 'patient-email');
            var courses = createCell('', 'patient-courses');
            var actions = createCell('', 'patient-actions');
            var editPatientAction = createAction('Edit Patient', 'fa-pencil');
            var modifyCoursesAction = createAction('Modify Courses', 'fa-list');
            var removePatientAction = createAction('Remove Patient', 'fa-times');

            tr.appendChild(name);
            tr.appendChild(email);
            tr.appendChild(courses);

            actions.appendChild(editPatientAction);
            actions.appendChild(modifyCoursesAction);
            actions.appendChild(removePatientAction);
            tr.appendChild(actions);

            return tr;
        }

        function createCell(text, className) {
            var td = document.createElement('td');
            $(td).text(text);
            $(td).addClass(className);
            return td;
        }

        function createAction(title, icon) {
            var span = document.createElement('span');
            $(span).addClass('manage-patient-action');

            var a = document.createElement('a');
            $(a).attr({
                href: '#',
                title: title
            });

            var i = document.createElement('i');
            $(i).addClass('fa ' + icon);

            a.appendChild(i);
            span.appendChild(a);

            return span;
        }
    });

    /**
     * Verifies patient information prior to allowing the new patient to be inserted
     *
     */
    function verifyPatientInput() {

        //set the emailCheck and usernameCheck to false before we do anything
        var emailCheck = false;
        var usernameCheck = false;

        //declare the patient's email and username variables
        var i4_patient_email;
        var i4_patient_username;

        var nextButton = $j('#add-new-patient-submit'); //store the nextButton element

        nextButton.prop("disabled", true); //lets disable the button immediately.

        //the patient email field changes
        $j("#patient_email").change(function (e) {

            emailCheck = false; //assume the email is false everytime we begin this
            nextButton.prop("disabled", true); //disable the button in case it was enabled previously

            i4_patient_email = $j(this).val(); //retrieve the patients email

            var data = {
                action: 'i4_lms_handle_check_email',
                security: wpcw_js_consts_fe.new_patient_nonce,
                patient_email: i4_patient_email
            };


            jQuery.post(wpcw_js_consts_fe.ajaxurl, data, function (response) {
                $j('#i4_email_availability_status').html(response.icon);

                if (response.status == 200) { //OK response
                    emailCheck = true;
                }

                if (usernameCheck && emailCheck) {
                    nextButton.prop("disabled", false);
                }
            }, 'json');

        }); //end patient email field changes

        //the patient username field changes
        $j("#patient_username").change(function (e) {
            usernameCheck = false; // assume the username is false everytime this field is changed
            nextButton.prop("disabled", true); //disable the button in case it was enabled previously

            i4_patient_username = $j(this).val(); //retrieve the patients email

            var data = {
                action: 'i4_lms_handle_check_username',
                security: wpcw_js_consts_fe.new_patient_nonce,
                patient_username: i4_patient_username
            };

            jQuery.post(wpcw_js_consts_fe.ajaxurl, data, function (response) {
                $j('#i4_username_availability_status').html(response.icon);

                if (response.status == 200) { //OK response
                    usernameCheck = true;
                }

                if (usernameCheck && emailCheck) {
                    nextButton.prop("disabled", false);
                }
            }, 'json');

        });

    }
});
