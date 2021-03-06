/**
 * Created by sivashchenko on 12/25/2014.
 *
 * Kiev coordinates: 50.4501, 30.523400000000038
 */
//Outputing base addresses
$('#firstLocation').val($('#fqbundle_event_form_location_address').val());
$('#fqbundle_event_form_location_address').change(function () {
    $('#firstLocation').empty().append($(this).val());
    $('#arrowLocation').hide();
    $('#secondLocation').hide();
});
$('#fqbundle_event_form_route_startLocation_address').change(function () {
    $('#firstLocation').empty().append($(this).val());
});
$('#fqbundle_event_form_route_endLocation_address').change(function () {
    $('#arrowLocation').show();
    $('#secondLocation').empty().append($(this).val()).show();
});
//var defaultPosition = new google.maps.LatLng(50.4501, 30.523400000000038);
//var formMarkerLat = $('#fqbundle_event_form_location_latitude').val();
//var formMarkerLng = $('#fqbundle_event_form_location_longitude').val();
//var formStartMarkerLat = $('#fqbundle_event_form_location_longitude').val();
//var formStartMarkerLng = $('#fqbundle_event_form_location_longitude').val();
//var formEndMarkerLat = $('#fqbundle_event_form_location_longitude').val();
//var formEndMarkerLng = $('#fqbundle_event_form_location_longitude').val();
//var formPath = $('#fqbundle_event_form_location_longitude').val();

var marker = new google.maps.Marker({title: 'Место проведения'});
var startMarker  = new google.maps.Marker({title: 'Старт'});
var endMarker = new google.maps.Marker({title: 'Финиш'});
var geocoder = new google.maps.Geocoder();
var map;
var poly;
var path = new google.maps.MVCArray();
var service = new google.maps.DirectionsService(), shiftPressed = false;

function processGeocodeResult(results, status) {
    if (status != google.maps.GeocoderStatus.OK) {
        console.log('Geocode was not successful for the following reason: ' + status);
        return;
    }
    $('#fqbundle_event_form_location_latitude').val(String(results[0].geometry.location.lat()));
    $('#fqbundle_event_form_location_longitude').val(String(results[0].geometry.location.lng()));
    var address = results[0].address_components[3].short_name
        + ', ' + results[0].address_components[2].short_name
        + ', ' + results[0].address_components[1].short_name
        + ', ' + results[0].address_components[0].short_name;
    $('#fqbundle_event_form_location_address').val(address).trigger('change');
    $('#fqbundle_event_form_city').val(results[0].address_components[3].short_name);
}

function showAddressOnMap() {
    var address = $('#address_field').val();
    geocoder.geocode(
        {address: address},
        function(results, status) {
            processGeocodeResult(results, status);
            map.setCenter(results[0].geometry.location);
            marker.setPosition(results[0].geometry.location);
        }
    );
}

function showGelocationOnMap() {
    if(navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            var pos = new google.maps.LatLng(position.coords.latitude,
                position.coords.longitude);

            map.setCenter(pos);
            marker.setMap(map);
            marker.setPosition(pos);
            geocoder.geocode({location: pos}, processGeocodeResult);
        }, function() {
            console.log('Error: The Geolocation service failed.');
        });
    } else {
        console.log('Error: Your browser doesn\'t support geolocation.');
    }
}

function initializeMap() {
    var mapOptions = {
        zoom: 15,
        center: new google.maps.LatLng(50.4501, 30.523400000000038),
        mapTypeId: google.maps.MapTypeId.ROADMAP
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
    if ($('#fqbundle_event_form_route_route').val()) {
        showRouteOnMap();
    } else if ($('#fqbundle_event_form_location_address').val()) {
        showLocationOnMap();
    } else {
        showGelocationOnMap();
    }
}

function showRouteOnMap() {
    $('#location_type').val('route');
    var startPosition = new google.maps.LatLng(
        $('#fqbundle_event_form_route_startLocation_latitude').val(),
        $('#fqbundle_event_form_route_startLocation_longitude').val()
    );
    var endPosition = new google.maps.LatLng(
        $('#fqbundle_event_form_route_endLocation_latitude').val(),
        $('#fqbundle_event_form_route_endLocation_longitude').val()
    );
    var position = new google.maps.LatLng(
        $('#fqbundle_event_form_location_latitude').val(),
        $('#fqbundle_event_form_location_longitude').val()
    );
    marker.setPosition(position);
    marker.setMap(map);
    marker.setVisible(false);

    startMarker.setPosition(startPosition);
    startMarker.setMap(map);
    map.setCenter(startPosition);
    endMarker.setPosition(endPosition);
    endMarker.setMap(map);

    setTimeout(function(){
        path = new google.maps.MVCArray(
            google.maps.geometry.encoding.decodePath($('#fqbundle_event_form_route_route').val())
        );
        poly.setPath(path)
    }, 3000);
}

function showLocationOnMap() {
    var position = new google.maps.LatLng(
        $('#fqbundle_event_form_location_latitude').val(),
        $('#fqbundle_event_form_location_longitude').val()
    );

    map.setCener(position);
    marker.setPosition(position);
    marker.setMap(map);
}

function mapClickEventHandler(args) {
    if ($('#location_type').val() == 'address') {
        marker.setPosition(args.latLng);
        geocoder.geocode({location: args.latLng}, processGeocodeResult);
    } else {
        addPoint(args.latLng);
    }
}

// Route map

function fillStartPointFields() {
    $('#fqbundle_event_form_route_startLocation_latitude').val(startMarker.position.lat());
    $('#fqbundle_event_form_route_startLocation_longitude').val(startMarker.position.lng());
    geocoder.geocode(
        {location: startMarker.position},
        function(results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                $('#fqbundle_event_form_route_startLocation_address').val(
                    results[0].address_components[3].short_name
                    + ', ' + results[0].address_components[2].short_name
                    + ', ' + results[0].address_components[1].short_name
                    + ', ' + results[0].address_components[0].short_name
                ).trigger('change');
            } else {
                console.log('Geocode was not successful for the following reason: ' + status);
            }
        }
    );
}

function fillEndPointFields() {
    $('#fqbundle_event_form_route_endLocation_longitude').val(endMarker.position.lng());
    $('#fqbundle_event_form_route_endLocation_latitude').val(endMarker.position.lat());
    geocoder.geocode(
        {location: endMarker.position},
        function(results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                var address = results[0].address_components[3].short_name
                    + ', ' + results[0].address_components[2].short_name
                    + ', ' + results[0].address_components[1].short_name
                    + ', ' + results[0].address_components[0].short_name;
                $('#fqbundle_event_form_route_endLocation_address').val(address).trigger('change');
            } else {
                console.log('Geocode was not successful for the following reason: ' + status);
            }
        }
    );
}

function addPoint(posiotion) {
    if (shiftPressed || path.getLength() === 0) {
        path.push(posiotion);
        if(path.getLength() === 1) {
            poly.setPath(path);
            startMarker.setMap(map);
            startMarker.setPosition(posiotion);
            geocoder.geocode({location: posiotion}, processGeocodeResult);
            fillStartPointFields();
        }
    } else {
        $('#fqbundle_event_form_save').attr("disabled", true);
        service.route(
            { origin: path.getAt(path.getLength() - 1), destination: posiotion, travelMode: google.maps.DirectionsTravelMode.DRIVING },
            function(result, status) {
                if (status == google.maps.DirectionsStatus.OK) {
                    for(var i = 0, len = result.routes[0].overview_path.length; i < len; i++) {
                        path.push(result.routes[0].overview_path[i]);
                    }
                    $('#fqbundle_event_form_route_route').val(google.maps.geometry.encoding.encodePath(path));
                    $('#fqbundle_event_form_save').attr("disabled", false);
                }
            }
        );
    }
    if (path.getLength() !== 0 && !endMarker.getMap()) {
        endMarker.setMap(map);
    }
    endMarker.setPosition(posiotion);
    fillEndPointFields();
}

function handleLocationTypeChange() {
    if ($('#location_type').val() == 'address') {
        clearRoute();
        //$('#firstLocation').empty();
        //$('#secondLocation').empty();
        //$('#arrowLocation').hide();
        $('.address-control').show();
        $('.route-control').hide();
        marker.setVisible(true);
        if (path.getLength() !== 0) {
            poly.setVisible(false);
            startMarker.setVisible(false);
            endMarker.setVisible(false);
        }
    } else {
        //$('#firstLocation').empty();
        $('.address-control').hide();
        $('.route-control').show();
        marker.setVisible(false);
        if (path.getLength() !== 0) {
            poly.setVisible(true);
            startMarker.setVisible(true);
            endMarker.setVisible(true);
        }
    }
}

function clearRoute()
{
    path.clear();
    startMarker.setMap(null);
    endMarker.setMap(null);
    //$('#firstLocation').empty();
    //$('#secondLocation').empty();
    //$('#arrowLocation').hide();
    return false;
}

$('#fqbundle_event_form_location_address').change(function () {
    $('#firstLocation').empty().append($(this).val());
    $('#arrowLocation').hide();
    $('#secondLocation').hide();
});
$('#fqbundle_event_form_route_startLocation_address').change(function () {
    $('#firstLocation').empty().append($(this).val());
});
$('#fqbundle_event_form_route_endLocation_address').change(function () {
    $('#arrowLocation').show();
    $('#secondLocation').empty().append($(this).val()).show();
});

google.maps.event.addDomListener(document, "keydown", function(e) { shiftPressed = e.shiftKey; });
google.maps.event.addDomListener(document, "keyup", function(e) { shiftPressed = e.shiftKey; });

google.maps.event.addDomListener(window, 'load', initializeMap);

$('#search_address').click(showAddressOnMap);
$('#location_type').change(handleLocationTypeChange);

$('#clear_route').click(clearRoute);