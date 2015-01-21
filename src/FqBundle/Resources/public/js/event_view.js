/**
 * Created by sivashchenko on 12/25/2014.
 */
$('.map').each(function(key, mapCanavs) {
    var position = new google.maps.LatLng($(mapCanavs).data('lat'), $(mapCanavs).data('lng'))
    var mapOptions = {
        zoom: 15,
        center: position,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        mapTypeControlOptions: {
            mapTypeIds: [google.maps.MapTypeId.ROADMAP, google.maps.MapTypeId.HYBRID, google.maps.MapTypeId.SATELLITE]
        },
        disableDoubleClickZoom: true
    };

    map = new google.maps.Map(mapCanavs, mapOptions);
    startMarker = new google.maps.Marker({
        position: position,
        map: map,
        title: 'Старт'
    });
});
