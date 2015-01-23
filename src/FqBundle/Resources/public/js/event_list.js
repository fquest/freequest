/**
 * Created by sivashchenko on 12/25/2014.
 */
$('[data-role="event-list-reloader"]').click(function() {
    var searchQuery = $('[data-role="search"]').val();
    if (searchQuery) {
        var input = $('<input type="text" name="query" hidden="hidden">');
        input.val(searchQuery);
        $('#list_filter').append(input);
    }
    $('#list_filter').submit();
});
$('[data-role="search"]').keyup(function(event){
    if(event.keyCode == 13){
        var searchQuery = $('[data-role="search"]').val();
        if (searchQuery) {
            var input = $('<input type="text" name="query" hidden="hidden">');
            input.val(searchQuery);
            $('#list_filter').append(input);
        }
        $('#list_filter').submit();
    }
});
var map;

$('[href="#tab2"]').click(function() {
    if (typeof map === 'undefined') {
        //var position = new google.maps.LatLng($(mapCanavs).data('lat'), $(mapCanavs).data('lng'));
        var mapOptions = {
            zoom: 12,
            center: new google.maps.LatLng(50.4501, 30.523400000000038),
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };

        map = new google.maps.Map(document.getElementById("list-map"), mapOptions);
        centerMapByGeolocation();

        $('.event-location-info').each(function(){
            var marker = new google.maps.Marker({
                position: new google.maps.LatLng($(this).data('lat'), $(this).data('lng')),
                map: map,
                title: String($(this).data('title'))
            });
            var url = $(this).data('url');
            google.maps.event.addListener(marker, 'click', function(){document.location = url});
        });
    }
});

function centerMapByGeolocation() {
    if(navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            var pos = new google.maps.LatLng(position.coords.latitude,
                position.coords.longitude);
            map.setCenter(pos);
        }, function() {
            handleNoGeolocation(true);
        });
    } else {
        // Browser doesn't support Geolocation
        handleNoGeolocation(false);
    }
}

function handleNoGeolocation(errorFlag) {
    if (errorFlag) {
        console.log('Error: The Geolocation service failed.');
    } else {
        console.log('Error: Your browser doesn\'t support geolocation.');
    }

    map.setCenter(new google.maps.LatLng(50.4501, 30.523400000000038));
}