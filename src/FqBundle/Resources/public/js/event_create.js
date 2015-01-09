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
$('[data-role="create_category"]').click(function() {
    $.ajax({
        type: 'post',
        url: $(this).data('url'),
        data: { name: $('#category_name').val() },
        success: function(data) {
            category = JSON.parse(data);
            var option = new Option(category.name, category.id);
            $('#form_category').append($(option));
            $('#form_category').val(category.id);
            $('#createCategoryPopup').modal('hide');
        }
    });
});