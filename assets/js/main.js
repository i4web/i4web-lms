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
        var iframe = $('#unit-video')[0];
        var player = $f(iframe);

        // When the player is ready, add listener for finish event
        player.addEvent('ready', function() {
            player.addEvent('finish', onFinish);
        });

        function onFinish(id) {
            // Programmatically click the "complete" button
            $('.wpcw_fe_progress_box_mark a').click();
        }
    });
});

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
