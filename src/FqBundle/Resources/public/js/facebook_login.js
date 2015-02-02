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

$.widget( "freequest.facebookLogin", {
    options: {
        facebookRoot: '#fb-root'
    },
    _create: function() {
        this._on(this.element, {"click": this._loginByFacebook});
    },
    _loginByFacebook: function() {
        var targetUrl = this.element.data('target');
        var url = $(this.options.facebookRoot).data('url');
        FB.getLoginStatus(function (response) {
            if (response.status === 'connected') {
                // connected
                //alert('Already connected, redirect to login page to create token.');
                document.location = url + '?target_path=' + encodeURIComponent(targetUrl);
            } else {
                // not_authorized
                FB.login(function (response) {
                    if (response.authResponse) {
                        document.location = url + '?target_path=' + encodeURIComponent(targetUrl);
                    } else {
                        //alert('Cancelled.');
                    }
                }, {scope: 'email'});
            }
        });
    }
});

$.widget( "freequest.authorizingLink", {
    options: {
        urlDataAttribute: 'url',
        loginButtonSelector: '[data-role="facebook-login"]',
        targetDataAttribute: 'target',
        loginButtonEvent: 'click'
    },
    _create: function() {
        this._on(this.element, {
            "click": this._navigateAuthorized
        });
    },
    _navigateAuthorized: function() {
        var url = this.element.data(this.options.urlDataAttribute);
        var facebookButton = $(this.options.loginButtonSelector);

        if (facebookButton.length) {
            facebookButton.data(this.options.targetDataAttribute, url);
            facebookButton.trigger(this.options.loginButtonEvent);
        } else {
            document.location = url;
        }
        return false;
    }
});
$.widget( "freequest.eventHider", {
    options: {
        urlDataAttribute: 'url',
        loginButtonSelector: '[data-role="facebook-login"]',
        targetDataAttribute: 'target',
        loginButtonEvent: 'click'
    },
    _create: function() {
        this._on(this.element, {
            "click": this._navigateAuthorized
        });
    },
    _navigateAuthorized: function() {
        var url = this.element.data(this.options.urlDataAttribute);
        var facebookButton = $(this.options.loginButtonSelector);

        if (facebookButton.length) {
            facebookButton.data(this.options.targetDataAttribute, url);
            facebookButton.trigger(this.options.loginButtonEvent);
        } else {
            this.element.closest(".list-group-item").animate({opacity:'0.0'},800).slideUp(700);
            $.post(url);
        }
        return false;
    }
});

$('[data-role="facebook-login"]').facebookLogin();
$("[data-role='link-button']").authorizingLink();
$("[data-role='link-button-hide']").eventHider();