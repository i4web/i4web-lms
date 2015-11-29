jQuery(document).ready(function ($) {
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
    player.addEvent('ready', function () {
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
        var courseid = $(this).attr('id');
        var data = {
            action: 'i4_lms_handle_unit_track_progress',
            id: courseid,
            progress_nonce: wpcw_js_consts_fe.progress_nonce
        };

        $(this).hide();
        $(this).parent().find('.wpcw_loader').show();

        // Hide any navigation boxes
        $('.wpcw_fe_navigation_box').hide();

        $.post(wpcw_js_consts_fe.ajaxurl, data, function (response) {
            $('#wpcw_fe_' + courseid).hide().html(response).fadeIn();
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
            bannerLinkSpan.innerHTML = "Move on to the";

            var bannerLink = document.createElement('a');
            bannerLink.setAttribute('href', nextUnitLink);
            bannerLink.setAttribute('class', 'button tiny next-unit-button');

            bannerLink.innerHTML = "Next Unit";

            bannerLinkSpan.appendChild(bannerLink);
        }

        // Show the success banner
        banner.show();

        return false;
    }

    function setCurrentLinkActive() {
        var path = window.location.pathname;
        path = path.replace(/\/$/, "");
        path = decodeURIComponent(path);

        jQuery(".top-bar-menu a").each(function () {
            var href = jQuery(this).attr('href');

            if (path == href) {
                jQuery(this).closest('li').addClass('active');
            }
            if (path == '' && href == 'http://celebrationhealtheducation.com') { // for the home page since the path is empty
                jQuery(this).closest('li').addClass('active');
            }

        });
    }
});
