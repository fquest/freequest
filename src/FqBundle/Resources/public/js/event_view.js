/**
 * Created by sivashchenko on 12/25/2014.
 */
$('.map').each(function(key, mapCanavs) {
    var position = new google.maps.LatLng($(mapCanavs).data('lat'), $(mapCanavs).data('lng'));
    var mapOptions = {
        zoom: 15,
        center: position,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };

    var map = new google.maps.Map(mapCanavs, mapOptions);
    var marker = new google.maps.Marker({
        position: position,
        map: map,
        title: 'Место проведения'
    });
});

$('.route-map').each(function(key, mapCanavs) {
    var startPosition = new google.maps.LatLng($(mapCanavs).data('start-lat'), $(mapCanavs).data('start-lng'));
    var endPosition = new google.maps.LatLng($(mapCanavs).data('end-lat'), $(mapCanavs).data('end-lng'));
    var mapOptions = {
        zoom: 15,
        center: startPosition,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };

    var map = new google.maps.Map(mapCanavs, mapOptions);
    var startMarker = new google.maps.Marker({
        position: startPosition,
        map: map,
        title: 'Старт'
    });
    var endMarker = new google.maps.Marker({
        position: endPosition,
        map: map,
        title: 'Финиш'
    });

    var poly = new google.maps.Polyline({
        map: map,
        geodesic: true,
        strokeColor: '#4682B4',
        strokeOpacity: 1.0,
        strokeWeight: 5
    });
    setTimeout(function(){
        var path = google.maps.geometry.encoding.decodePath($(mapCanavs).data('route'));
        poly.setPath(path)
    }, 3000);
});
