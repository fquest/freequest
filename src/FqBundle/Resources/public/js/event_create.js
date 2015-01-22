/**
 * Created by sivashchenko on 12/25/2014.
 *
 * Kiev coordinates: 50.4501, 30.523400000000038
 */
function showRouteForm() {
    var locations = $('#fqbundle_event_form_route_locations');
    locations.data('index', locations.find(':input').length);
    addLocationForm();
}

function addLocationForm() {
    var locations = $('#fqbundle_event_form_route_locations');
    var prototype = locations.data('prototype');
    var index = locations.data('index');
    var newForm = prototype.replace('<label class="required">__name__label__</label>', '').replace(/__name__/g, index);
    locations.append(newForm);
    locations.data('index', index + 1);
}
showRouteForm();

var marker;
var startMarker, endMarker;
var geocoder = new google.maps.Geocoder();
var map;
var poly;
var path = new google.maps.MVCArray();
var service = new google.maps.DirectionsService(), shiftPressed = false;

function showAddressOnMap() {
    var address = $(this).val();
    geocoder.geocode(
        {address: address},
        function(results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                map.setCenter(results[0].geometry.location);
                marker.setPosition(results[0].geometry.location);
                console.log(results[0].geometry.location);
            } else {
                console.log('Geocode was not successful for the following reason: ' + status);
            }
        }
    );
}

function fillFormByCoordinates() {
    geocoder.geocode(
        {address: address},
        function(results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                map.setCenter(results[0].geometry.location);
                marker.setPosition(results[0].geometry.location);
                console.log(results[0].geometry.location);
            } else {
                console.log('Geocode was not successful for the following reason: ' + status);
            }
        }
    );
}
function fillAddress(position) {
    $('#fqbundle_event_form_route_locations_0_latitude').val(String(position.lat()));
    $('#fqbundle_event_form_route_locations_0_longitude').val(String(position.lng()));
    geocoder.geocode(
        {location: position},
        function(results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                $('#location_address').val(
                    results[0].address_components[1].short_name + ', ' + results[0].address_components[0].short_name
                );
                $('#fqbundle_event_form_route_locations_0_address').val(
                    results[0].address_components[1].short_name + ', ' + results[0].address_components[0].short_name
                );
                $('#fqbundle_event_form_city').val(results[0].address_components[3].short_name);
            } else {
                console.log('Geocode was not successful for the following reason: ' + status);
            }
        }
    );
}

function showGelocationOnMap() {
    if(navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            var pos = new google.maps.LatLng(position.coords.latitude,
                position.coords.longitude);

            map.setCenter(pos);
            marker = new google.maps.Marker({
                position: pos,
                map: map,
                title: 'Место проведения события'
            });
            fillAddress(pos);
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

function initializeMap() {
    var mapOptions = {
        zoom: 15,
        center: new google.maps.LatLng(50.4501, 30.523400000000038),
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        mapTypeControlOptions: {
            mapTypeIds: [google.maps.MapTypeId.ROADMAP, google.maps.MapTypeId.HYBRID, google.maps.MapTypeId.SATELLITE]
        },
        disableDoubleClickZoom: true
    };

    map = new google.maps.Map(document.getElementById("map"), mapOptions);
    google.maps.event.addListener(map, 'click', mapClickEventHandler);
    poly = new google.maps.Polyline({
        map: map,
        geodesic: true,
        strokeColor: '#4682B4',
        strokeOpacity: 1.0,
        strokeWeight: 5
    });
    showGelocationOnMap();
}

function mapClickEventHandler(args) {
    if ($('#location_type').val() == 'address') {
        marker.setPosition(args.latLng);
        fillAddress(args.latLng);
    } else {
        addPoint(args.latLng);
    }
}
function addPoint(posiotion) {
    if (shiftPressed || path.getLength() === 0) {
        path.push(posiotion);
        if(path.getLength() === 1) {
            poly.setPath(path);
            startMarker = new google.maps.Marker({
                position: posiotion,
                title: 'Старт',
                map: map
            });
        }
    } else {
        service.route(
            { origin: path.getAt(path.getLength() - 1), destination: posiotion, travelMode: google.maps.DirectionsTravelMode.DRIVING },
            function(result, status) {
                if (status == google.maps.DirectionsStatus.OK) {
                    for(var i = 0, len = result.routes[0].overview_path.length; i < len; i++) {
                        path.push(result.routes[0].overview_path[i]);
                    }
                }
            }
        );
    }
    if (path.getLength() !== 0 && !endMarker) {
        endMarker = new google.maps.Marker({
            position: posiotion,
            title: 'Финиш',
            map: map
        });
    } else {
        endMarker.setPosition(posiotion);
    }
}

function handleLocationTypeChange() {
    if ($('#location_type').val() == 'address') {
        marker.setVisible(true);
        if (path.getLength() !== 0) {
            poly.setVisible(false);
            startMarker.setVisible(false);
            endMarker.setVisible(false);
        }
    } else {
        marker.setVisible(false);
        if (path.getLength() !== 0) {
            poly.setVisible(true);
            startMarker.setVisible(true);
            endMarker.setVisible(true);
        }
    }
}

google.maps.event.addDomListener(document, "keydown", function(e) { shiftPressed = e.shiftKey; });
google.maps.event.addDomListener(document, "keyup", function(e) { shiftPressed = e.shiftKey; });

google.maps.event.addDomListener(window, 'load', initializeMap);

$('#location_address').change(showAddressOnMap);
$('#fqbundle_event_form_city').change(showAddressOnMap);
$('#location_type').change(handleLocationTypeChange);

//Route maps

//var routeMap, path = new google.maps.MVCArray(), service = new google.maps.DirectionsService(), shiftPressed = false, poly;
//
//google.maps.event.addDomListener(document, "keydown", function(e) { shiftPressed = e.shiftKey; });
//google.maps.event.addDomListener(document, "keyup", function(e) { shiftPressed = e.shiftKey; });
//
//function initialize() {
//    var myOptions = {
//        zoom: 15,
//        center: new google.maps.LatLng(37.2008385157313, -93.2812106609344),
//        mapTypeId: google.maps.MapTypeId.ROADMAP,
//        mapTypeControlOptions: {
//            mapTypeIds: [google.maps.MapTypeId.ROADMAP, google.maps.MapTypeId.HYBRID, google.maps.MapTypeId.SATELLITE]
//        },
//        disableDoubleClickZoom: true,
//        scrollwheel: false,
//        draggableCursor: "crosshair"
//    };
//
//    routeMap = new google.maps.Map(document.getElementById("route-map"), myOptions);
//    poly = new google.maps.Polyline({
//        map: routeMap,
//        geodesic: true,
//        strokeColor: '#4682B4',
//        strokeOpacity: 1.0,
//        strokeWeight: 5
//    });
//
//    var addPoint = function(evt) {
//        if (shiftPressed || path.getLength() === 0) {
//            path.push(evt.latLng);
//            if(path.getLength() === 1) {
//                poly.setPath(path);
//            }
//        } else {
//            service.route({ origin: path.getAt(path.getLength() - 1), destination: evt.latLng, travelMode: google.maps.DirectionsTravelMode.DRIVING }, function(result, status) {
//                if (status == google.maps.DirectionsStatus.OK) {
//                    for(var i = 0, len = result.routes[0].overview_path.length; i < len; i++) {
//                        path.push(result.routes[0].overview_path[i]);
//                    }
//                }
//            });
//        }
//        var marker = new google.maps.Marker({
//            position: evt.latLng,
//            title: '#' + path.getLength(),
//            map: routeMap
//        });
//    };
//
//    google.maps.event.addListener(routeMap, "click", addPoint);
//}
//$('#add-route').click(function(){
//    $(this).hide();
//    $('#route-div').show();
//    initialize();
//    return false;
//});