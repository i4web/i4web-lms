jQuery(document).ready(function ($) {
    $(function() {
        $(document).confirmWithReveal({
            modal_class: 'reveal-confirm-modal medium',
            ok: 'Delete',
            ok_class: 'button blue confirm-button tiny',
            body: 'This action is permanent and cannot be undone',
            cancel_class: 'button primary cancel-button tiny',
            title_class: 'confirm-heading',
            footer_class: 'confirm-buttons'
        });

        var managePatientsTable = $('#manage-patients-table');
        $(managePatientsTable).tablesorter({
            // Initial sort is patient name in ascending order
            sortList: [[0,0]],
            widgets: ['filter'],
            headers: {
                2: { filter: false },
                3: { filter: false }
            },
            cancelSelection: true,
            ignoreCase: true,
            sortInitialOrder: "asc"
        });

        pagerOptions = {
            container: $(".pager"),
            output: '{startRow} - {endRow} / {filteredRows}',
            fixedHeight: false,
            removeRows: false,

            cssGoto: '.gotoPage',
            cssNext: '.next',
            cssPrev: '.prev',
            cssFirst: '.first',
            cssLast: '.last'
        };
        managePatientsTable.tablesorterPager(pagerOptions);

        //set the emailCheck and usernameCheck to false before we do anything
        var emailCheck = false;
        var usernameCheck = false;
        var currentPatient = {};

        $('#available-courses, #user-courses').sortable({
            connectWith: ".connectedSortable",
            revert: true
        }).disableSelection();

        $('#add-patients').on('click', function() {
            showEditPatientModal(true);
        });

        var patientsList = $('#patients-list');
        // When clicking the modify courses button
        $(patientsList).on('click', '.fa-list', function() {
            var patient = {
                id: $(this).closest('tr').attr('id'),
                name: $(this).closest('td').siblings('.patient-name').text()
            };
            showModifyCoursesModal(patient);
        });

        // When clicking the edit patient button
        $(patientsList).on('click', '.fa-pencil', function() {
            var patientId = $(this).closest('tr').attr('id');
            showEditPatientModal(false, patientId);
        });

        // When clicking the remove patient button
        $(patientsList).on('click', '.fa-times', function() {
            var patientName = $(this).closest('td').siblings('.patient-name').text();
            $(this).closest('a').attr('data-confirm', '{"title": "Are you sure you want to remove <i><strong>' + patientName + '</strong></i>?"}');
        });

        // Handle the event where the user has confirmed the deletion of the patient
        $(patientsList).on('confirm.reveal', '.remove-patient', function() {
            var patientId = $(this).closest('tr').attr('id');
            var data = {
                action: 'i4_lms_remove_patient',
                patientId: patientId
            };

            var modal = $('.reveal-confirm-modal');
            var spinner = document.createElement('div');
            $(spinner).addClass('spinner').appendTo(modal);

            var confirmButtonsDiv = $('div.confirm-buttons');
            var cancelButton = $(confirmButtonsDiv).find('a.cancel-button');
            var doneButton = $(confirmButtonsDiv).find('a.confirm-button');
            var confirmHeader = $(modal).find('h2');
            var confirmBody = $(modal).find('p');

            // Dim the confirm dialog and show the spinner
            confirmHeader.fadeTo(500, .2);
            confirmBody.fadeTo(500, .2);
            confirmButtonsDiv.fadeTo(500, .2);
            $(spinner).show();
            cancelButton.prop('disabled', true);
            doneButton.prop('disabled', true);

            $.post(wpcw_js_consts_fe.ajaxurl, data, function() {
                // Remove the confirm modal
                modal.remove();
                $('.reveal-modal-bg').hide();

                // Remove the patient
                $('#' + patientId).remove();
                $(managePatientsTable).trigger("update", [ true ]);
            });
        });

        $('#update-patient-courses-submit').on('click', function (e) {
            e.preventDefault();

            var userCourses = $("#user-courses");
            var courseIds = userCourses.sortable("toArray");
            var patientId = $("#coursesPatientId").val();

            var data = {
                action: 'i4_lms_handle_update_patient_courses',
                patientId: patientId,
                courses: courseIds
            };

            // Dim the form and show the spinner
            $('#modify-courses-form').fadeTo(500, .2);
            $('#modify-courses-spinner').show();
            $('#update-patient-courses-submit').prop('disabled', true);

            $.post(wpcw_js_consts_fe.ajaxurl, data, function() {
                var patientCourses = $('#' + patientId).find('.patient-courses');

                // Sort the assigned courses by name
                var courses = $(userCourses).find('li');
                courses.sort(function(a, b) {
                    return a.textContent.toLowerCase().localeCompare(b.textContent.toLowerCase());
                });

                // Generate the HTML for the assigned courses
                var coursesText = "";
                var numCourses = courses.size();
                for (var i = 0; i < numCourses; i++) {
                    coursesText += courses[i].textContent;
                    if (i < numCourses - 1) {
                        coursesText += "<br/>";
                    }
                }
                $(patientCourses).html(coursesText);

                $('#modify-courses-modal').foundation('reveal', 'close');
                clearModifyCoursesModal();
            });
        });

        // The new user submit button.
        $('#edit-patient-submit').on('click', function (e) {
            e.preventDefault();

            var i4_patient_email = $('#patient_email').val();
            var i4_patient_firstname = $('#patient_fname').val();
            var i4_patient_lastname = $('#patient_lname').val();

            var patientId = $('#patientId').val();
            var data = {
                patient_email: i4_patient_email,
                patient_fname: i4_patient_firstname,
                patient_lname: i4_patient_lastname
            };

            if (patientId) {
                // Editing an existing patient
                data.action = 'i4_lms_update_patient';
                data.patient_id = patientId;
            }
            else {
                // Adding a new patient
                var i4_patient_username = $('#patient_username').val();
                data.action = 'i4_lms_handle_add_new_patient';
                data.security = wpcw_js_consts_fe.new_patient_nonce;
                data.patient_username = i4_patient_username;
            }

            // Dim the form and show the spinner
            var submitButton = $('#edit-patient-submit');
            $('#edit-patient-form').fadeTo(500, .2);
            $('#edit-patient-spinner').show();
            submitButton.prop('disabled', true);

            $.post(wpcw_js_consts_fe.ajaxurl, data, function (response) {
                if (response.status == 200) {
                    var patient = {
                        id: response.patient_id,
                        name: i4_patient_firstname + " " + i4_patient_lastname,
                        email: i4_patient_email
                    };

                    if (!patientId) {
                        $.when(showModifyCoursesModal(patient)).done(function () {
                            insertPatient(patient);
                            // Hide the new user modal
                            $('#edit-patient-modal').foundation('reveal', 'close');
                            clearEditPatientModal();
                        });
                    }
                    else {
                        updatePatient(patient);
                        // Hide the new user modal
                        $('#edit-patient-modal').foundation('reveal', 'close');
                        clearEditPatientModal();
                    }
                    // Remove the current patient since we no longer need the data
                    currentPatient = {};
                }

                // Always hide the spinner
                $('#edit-patient-form').fadeTo(500, 1);
                $('#edit-patient-spinner').hide();
                submitButton.prop('disabled', false);
            }, 'json');
        });

        function clearEditPatientModal() {
            // Unset the patient ID and name in the modal
            $('#patientId').removeAttr('value');
            $('#patient_email').removeAttr('value');
            $('#patient_fname').removeAttr('value');
            $('#patient_lname').removeAttr('value');
            $('#i4_email_availability_status').empty();
            $('#i4_username_availability_status').empty();

            var submitButton = $('#edit-patient-submit');
            submitButton.text('Next');
            submitButton.attr('disabled', true);

            var username = $('#patient_username');
            $(username).removeAttr('value');
            $(username).removeAttr('disabled');

            $('#modalTitle').text("Add New Patient");
        }

        function clearModifyCoursesModal() {
            // Hide the spinner and enable the submit button again
            $('#modify-courses-form').fadeTo(500, 1);
            $('#modify-courses-spinner').hide();
            $('#update-patient-courses-submit').prop('disabled', false);

            // Unset the patient ID and name in the modal
            $('#coursesPatientId').removeAttr('value');
            $('#modifyCoursesTitle').children('i').empty();

            // Unset the courses in the sortable
            $('#available-courses').empty();
            $('#user-courses').empty();
        }

        function showEditPatientModal(isNewPatient, patientId) {
            clearEditPatientModal();

            var editPatientModal = $('#edit-patient-modal');
            if (!isNewPatient) {
                var data = {
                    action: 'i4_lms_get_patient_info',
                    patient_id: patientId
                };

                $.when($.get(wpcw_js_consts_fe.ajaxurl, data, function (response) {
                    currentPatient = {
                        id: patientId,
                        email: response.email,
                        username: response.user_login,
                        fname: response.first_name,
                        lname: response.last_name
                    };
                }, 'json').done(function () {
                    $(editPatientModal).find('#patientId').val(currentPatient.id);

                    $('#modalTitle').text("Edit Patient");

                    var email = $(editPatientModal).find('#patient_email');
                    $(email).val(currentPatient.email);

                    var username = $(editPatientModal).find('#patient_username');
                    $(username).attr('disabled', true);
                    $(username).val(currentPatient.username);

                    var fname = $(editPatientModal).find('#patient_fname');
                    $(fname).val(currentPatient.fname);

                    var lname = $(editPatientModal).find('#patient_lname');
                    $(lname).val(currentPatient.lname);

                    var submitButton = $('#edit-patient-submit');
                    submitButton.text('Done');
                    submitButton.attr('disabled', false);

                    $(editPatientModal).foundation('reveal', 'open');
                }));
            }
            else {
                $(editPatientModal).foundation('reveal', 'open');
            }
        }

        function showModifyCoursesModal(patient) {
            clearModifyCoursesModal();

            var data = {
                action: 'i4_lms_get_user_courses',
                patientId: patient.id
            };
            $.get(wpcw_js_consts_fe.ajaxurl, data, function (response) {
                // Set the patient ID and name in the modal
                $('#coursesPatientId').val(patient.id);
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

        function updatePatient(patient) {
            var patientRow = $('#' + patient.id);
            $('.patient-name', patientRow).text(patient.name);
            $('.patient-email', patientRow).text(patient.email);
        }

        function insertPatient(patient) {
            var patientRow = createRow(patient);
            var $patientRow = $(patientRow);
            var resort = true;
            $(managePatientsTable).find('tbody').append($patientRow).trigger('addRows', [$patientRow, resort]);
            return false;
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
            var removePatientAction = createAction('Remove Patient', 'fa-times', 'remove-patient');

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

        function createAction(title, iconClass, aClass) {
            var span = document.createElement('span');
            $(span).addClass('manage-patient-action');

            var a = document.createElement('a');
            $(a).attr({
                href: '#',
                title: title
            });
            if (aClass) {
                $(a).addClass(aClass);
            }

            var i = document.createElement('i');
            $(i).addClass('fa ' + iconClass);

            a.appendChild(i);
            span.appendChild(a);

            return span;
        }

        //the patient email field changes
        $("#patient_email").change(function (e) {
            var usernameField = $('#patient_username');
            usernameCheck = usernameCheck || usernameField.attr('disabled'); // Use the value we already have or true if editing a user

            var i4_patient_email = $(this).val(); //retrieve the patients email
            emailCheck = false; //assume the email is false every time we begin this
            if (currentPatient.email) {
                emailCheck = currentPatient.email === i4_patient_email;
            }
            var nextButton = $('#edit-patient-submit'); //store the nextButton element
            nextButton.prop("disabled", true); //disable the button in case it was enabled previously

            var data = {
                action: 'i4_lms_handle_check_email',
                security: wpcw_js_consts_fe.new_patient_nonce,
                patient_email: i4_patient_email
            };

            $.post(wpcw_js_consts_fe.ajaxurl, data, function (response) {
                var emailStatus = $('#i4_email_availability_status');
                // If we've reset the data to the patient's existing email, we should show a success
                if (emailCheck) {
                    emailStatus.html('<i class="fa fa-check"></i>')
                }
                // If the emailCheck is false (new patient or emails don't match) then use the icon from the response
                else {
                    emailStatus.html(response.icon);
                }

                if (response.status == 200) { //OK response
                    emailCheck = true;
                }

                if (usernameCheck && emailCheck) {
                    nextButton.prop("disabled", false);
                }
            }, 'json');

        }); //end patient email field changes

        //the patient username field changes
        $("#patient_username").change(function (e) {
            usernameCheck = false; // assume the username is false every time this field is changed
            var nextButton = $('#edit-patient-submit'); //store the nextButton element
            nextButton.prop("disabled", true); //disable the button in case it was enabled previously

            var i4_patient_username = $(this).val(); //retrieve the patients email

            var data = {
                action: 'i4_lms_handle_check_username',
                security: wpcw_js_consts_fe.new_patient_nonce,
                patient_username: i4_patient_username
            };

            $.post(wpcw_js_consts_fe.ajaxurl, data, function (response) {
                $('#i4_username_availability_status').html(response.icon);

                if (response.status == 200) { //OK response
                    usernameCheck = true;
                }

                if (usernameCheck && emailCheck) {
                    nextButton.prop("disabled", false);
                }
            }, 'json');

        });
    });
});
