/* Main JS Script for front end functions */
jQuery( document ).ready( function( $ ) {
    // Binding to trigger checkPasswordStrength
    $( 'body' ).on( 'keyup', 'input[name=patient_password], input[name=patient_password_retyped]',
        function( event ) {
            checkPasswordStrength(
                $('input[name=patient_password]'),         // First password field
                $('input[name=patient_password_retyped]'), // Second password field
                $('#password-strength'),           // Strength meter
                $('button[type=submit]'),           // Submit button
                ['black', 'listed', 'word']        // Blacklisted words
            );
        }
    );

    $(function() {
        setCurrentLinkActive();

        var iframe = $('#unit-video')[0];
        var player = $f(iframe);
        var duration = undefined;
        var minViewPct = i4_site_settings.min_viewing_pct;
        var userCompletedUnit = i4_site_settings.unit_status;
        var viewedMinPct = false || userCompletedUnit;

        // Get the Mark as Completed button and disable it until the user completes the unit
        var markCompleteButton = $('.wpcw_fe_progress_box_mark a');
        markCompleteButton.addClass('disabled');

        // When the player is ready, add listener for finish event
        player.addEvent('ready', function() {
            if (userCompletedUnit) {
                enableMarkCompleteButton();
                showCompletionBanner();
            }
            player.addEvent('finish', onFinish);
            player.addEvent('playProgress', onPlayProgress);
        });

        function onPlayProgress(data, id) {
            // Only check view percentage if we haven't already viewed the minimum amount of the video
            if (!viewedMinPct) {
                if (!duration) {
                    duration = data.duration
                }

                var currTime = data.seconds;
                var currPct = currTime / duration;
                if (currPct >= minViewPct) {
                    enableMarkCompleteButton();
                    viewedMinPct = true;
                }
            }
        }

        function onFinish(id) {
            // Programmatically enable and click the "complete" button
            enableMarkCompleteButton();
            markCompleteButton.click();
        }

        function enableMarkCompleteButton() {
            markCompleteButton.click(markUnitComplete);
            markCompleteButton.removeClass('disabled');
        }

        function markUnitComplete() {
            var courseid = $j(this).attr('id');
            var data = {
                action:         'i4_lms_handle_unit_track_progress',
                id:             courseid,
                progress_nonce: wpcw_js_consts_fe.progress_nonce
            };

            $j(this).hide();
            $j(this).parent().find('.wpcw_loader').show();

            // Hide any navigation boxes
            $j('.wpcw_fe_navigation_box').hide();

            $j.post(wpcw_js_consts_fe.ajaxurl, data, function(response) {
                $j('#wpcw_fe_' + courseid).hide().html(response).fadeIn();
            });

            showCompletionBanner();

            return false;
        }

        function showCompletionBanner() {
            var nextUnit = jQuery('#next-unit').get(0);
            var banner = jQuery('.banner-wrapper');
            if (nextUnit) {
                var nextUnitLink = nextUnit.href;
                var bannerLinkSpan = jQuery('#completed-next-link').get(0);
                bannerLinkSpan.innerHTML = "Move on to the ";

                var bannerLink = document.createElement('a');
                bannerLink.setAttribute('href', nextUnitLink);
                bannerLink.innerHTML = "next unit.";

                bannerLinkSpan.appendChild(bannerLink);
            }

            // Show the success banner
            banner.show();

            return false;
        }
    });
});

function setCurrentLinkActive() {
    var path = window.location.pathname;
    path = path.replace(/\/$/, "");
    path = decodeURIComponent(path);

    jQuery(".top-bar-menu a").each(function () {
        var href = jQuery(this).attr('href');

        if (path == href) {
            jQuery(this).closest('li').addClass('active');
        }
        if (path == '' && href == 'http://celebrationhealtheducation.com'){ // for the home page since the path is empty
          jQuery(this).closest('li').addClass('active');

        }

    });
}

function checkPasswordStrength( $pass1,
                                $pass2,
                                $strengthResult,
                                $submitButton,
                                blacklistArray ) {
    var pass1 = $pass1.val();
    var pass2 = $pass2.val();

    // Reset the form & meter
    $submitButton.attr( 'disabled', 'disabled' );
        $strengthResult.removeClass( 'alert-box alert warning success radius' );

    // Extend our blacklist array with those from the inputs & site data
    blacklistArray = blacklistArray.concat( wp.passwordStrength.userInputBlacklist() )

    // Get the password strength
    var strength = wp.passwordStrength.meter( pass1, blacklistArray, pass2 );

    // Add the strength meter results
    switch ( strength ) {

        case 2:
            $strengthResult.addClass( 'alert-box alert radius' ).html( pwsL10n.bad );
            break;

        case 3:
            $strengthResult.addClass( 'alert-box success radius' ).html( pwsL10n.good );
            break;

        case 4:
            $strengthResult.addClass( 'alert-box success radius' ).html( pwsL10n.strong );
            break;

        case 5:
            $strengthResult.addClass( 'alert-box warning radius' ).html( pwsL10n.mismatch );
            break;

        default:
            $strengthResult.addClass( 'alert-box alert radius' ).html( pwsL10n.short );

    }

    // The meter function returns a result even if pass2 is empty,
    // enable only the submit button if the password is strong and
    // both passwords are filled up
    if ( (3 === strength && '' !== pass2.trim() ) || (4 === strength && '' !== pass2.trim() ) ) {
        $submitButton.removeAttr( 'disabled' );
    }

    return strength;
}
