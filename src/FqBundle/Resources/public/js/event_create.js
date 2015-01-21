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

var startMarker;
var geocoder = new google.maps.Geocoder();
var map;

function showAddressOnMap() {
    var address = $(this).val();
    geocoder.geocode(
        {address: address},
        function(results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                map.setCenter(results[0].geometry.location);
                startMarker.setPosition(results[0].geometry.location);
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
                startMarker.setPosition(results[0].geometry.location);
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
            startMarker = new google.maps.Marker({
                position: pos,
                map: map,
                title: 'Старт'
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

    var options = {
        map: map,
        position: new google.maps.LatLng(50.4501, 30.523400000000038)
    };

    map.setCenter(options.position);
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
    showGelocationOnMap();
}

function mapClickEventHandler(args) {
    startMarker.setPosition(args.latLng);
    fillAddress(args.latLng);
}

function handleLocationTypeChange() {

}

google.maps.event.addDomListener(window, 'load', initializeMap);

$('#location_address').change(showAddressOnMap);
$('#fqbundle_event_form_city').change(showAddressOnMap);
$('#location_type').change(hadleLocationTypeChange);

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