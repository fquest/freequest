/**
 * Created by sivashchenko on 12/25/2014.
 */
$('.map').each(function(key, mapCanavs) {
    var latlng = $(mapCanavs).data('latlng').split(',');
    console.log(latlng);
    var map = new GMaps({
        el: mapCanavs,
        lat: latlng[0],
        lng: latlng[1]
    });
    marker = map.addMarker({
        lat: latlng[0],
        lng: latlng[1]
    });
});