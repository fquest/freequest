/**
 * Created by sivashchenko on 12/27/2014.
 */
window.fbAsyncInit = function() {
    // init the FB JS SDK
    FB.init({
        appId      : '347707842068035',         // App ID from the app dashboard
        channelUrl : '//freequest.com.ua',      // Channel file for x-domain comms
        status     : true,                      // Check Facebook Login status
        xfbml      : true                       // Look for social plugins on the page
    });
};

// Load the SDK asynchronously
(function(d, s, id){
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) {return;}
    js = d.createElement(s); js.id = id;
    js.src = "//connect.facebook.net/en_US/all.js";
    fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));


$('[data-role="facebook-login"]').click(function() {
    var targetUrl = $(this).data('target');
    var url = $('#fb-root').data('url');
    FB.getLoginStatus(function(response) {
        if (response.status === 'connected') {
            // connected
            //alert('Already connected, redirect to login page to create token.');
            document.location = url + '?target_path=' + encodeURIComponent(targetUrl);
        } else {
            // not_authorized
            FB.login(function(response) {
                if (response.authResponse) {
                    document.location = url + '?target_path=' + encodeURIComponent(targetUrl);
                } else {
                    //alert('Cancelled.');
                }
            }, {scope: 'email'});
        }
    });
});

$("[data-role='link-button']").click(function () {
    var url = $(this).data('url');
    var facebookButton = $('[data-role="facebook-login"]');

    if (facebookButton.length) {
        facebookButton.data('target', url);
        facebookButton.trigger('click');
    } else {
        document.location = url;
    }
    return false;
});