/**
 * Created by sivashchenko on 12/25/2014.
 */
var marker;
function fillAddress(latitude, logitude) {
    GMaps.geocode({
        latLng: {
            lat: latitude,
            lng: logitude
        },
        callback: function (results, status) {
            if (status == 'OK') {
                $('#form_address').val(results[0].address_components[1].short_name + ', ' + results[0].address_components[0].short_name);
                $('#form_city').val(results[0].address_components[3].short_name);
            }
        }
    })
}
var map = new GMaps({
    el: '#map',
    lat: -12.043333,
    lng: -77.028333,
    click: function(args) {
        if (marker) {
            marker.setPosition({
                lat: args.latLng.lat(),
                lng: args.latLng.lng()
            });
        } else {
            marker = map.addMarker({
                lat: args.latLng.lat(),
                lng: args.latLng.lng()
            });
        }
        $('#form_position').val(String(args.latLng.lat()) + ',' + (String(args.latLng.lng())));
        fillAddress(args.latLng.lat(), args.latLng.lng());
    },
});
GMaps.geolocate({
    success: function(position) {
        map.setCenter(position.coords.latitude, position.coords.longitude);
        $('#form_position').val(String(position.coords.latitude) + ',' + (String(position.coords.longitude)));
        fillAddress(position.coords.latitude, position.coords.longitude);
        marker = map.addMarker({
            lat: position.coords.latitude,
            lng: position.coords.longitude
        });
    },
    error: function(error) {
        //map.setCenter(-12.043333, -77.028333);
    },
    not_supported: function() {
        //alert("Your browser does not support geolocation");
    },
    always: function() {
        //alert("Done!");
    }
});
$('#form_address').change(function() {
    GMaps.geocode({
        address: $('#form_address').val(),
        callback: function(results, status) {
            if (status == 'OK') {
                var latLng = results[0].geometry.location;
                map.setCenter(latLng.lat(), latLng.lng());
                $('#form_position').val(String(latLng.lat()) + ',' + (String(latLng.lng())));
                //fillAddress(latLng.lat(), latLng.lng());
                if (marker) {
                    marker.setPosition({
                        lat: latLng.lat(),
                        lng: latLng.lng()
                    });
                } else {
                    marker = map.addMarker({
                        lat: latLng.lat(),
                        lng: latLng.lng()
                    });
                }
            }
        }
    });
});

//Route maps

var routeMap, path = new google.maps.MVCArray(), service = new google.maps.DirectionsService(), shiftPressed = false, poly;

google.maps.event.addDomListener(document, "keydown", function(e) { shiftPressed = e.shiftKey; });
google.maps.event.addDomListener(document, "keyup", function(e) { shiftPressed = e.shiftKey; });

function initialize() {
    var myOptions = {
        zoom: 15,
        center: new google.maps.LatLng(37.2008385157313, -93.2812106609344),
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        mapTypeControlOptions: {
            mapTypeIds: [google.maps.MapTypeId.ROADMAP, google.maps.MapTypeId.HYBRID, google.maps.MapTypeId.SATELLITE]
        },
        disableDoubleClickZoom: true,
        scrollwheel: false,
        draggableCursor: "crosshair"
    };

    routeMap = new google.maps.Map(document.getElementById("route-map"), myOptions);
    poly = new google.maps.Polyline({
        map: routeMap,
        geodesic: true,
        strokeColor: '#4682B4',
        strokeOpacity: 1.0,
        strokeWeight: 5
    });

    var addPoint = function(evt) {
        if (shiftPressed || path.getLength() === 0) {
            path.push(evt.latLng);
            if(path.getLength() === 1) {
                poly.setPath(path);
            }
        } else {
            service.route({ origin: path.getAt(path.getLength() - 1), destination: evt.latLng, travelMode: google.maps.DirectionsTravelMode.DRIVING }, function(result, status) {
                if (status == google.maps.DirectionsStatus.OK) {
                    for(var i = 0, len = result.routes[0].overview_path.length; i < len; i++) {
                        path.push(result.routes[0].overview_path[i]);
                    }
                }
            });
        }
        var marker = new google.maps.Marker({
            position: evt.latLng,
            title: '#' + path.getLength(),
            map: routeMap
        });
    };

    google.maps.event.addListener(routeMap, "click", addPoint);
}
$('#add-route').click(function(){
    $(this).hide();
    $('#route-div').show();
    initialize();
    return false;
});