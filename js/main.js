//Useful links:
// http://code.google.com/apis/maps/documentation/javascript/reference.html#Marker
// http://code.google.com/apis/maps/documentation/javascript/services.html#Geocoding
// http://jqueryui.com/demos/autocomplete/#remote-with-cache

var geocoder;
var map;
var marker;
var markersArray = [];
var check = [];
var img;

function initialize(iniLat, iniLng) {

    if (iniLat !== undefined)
    {
        iniLat = parseFloat(iniLat);
        iniLng = parseFloat(iniLng);
        var latlng = new google.maps.LatLng(iniLat, iniLng);
        check = [];
        // condition for checking that which of the checkboxes are selected
        if ($("#transport").attr("checked"))
        {
            check.push('airport', 'bus_station', 'train_station', 'taxi_stand', 'travel_agency');
        }
        if ($("#bars").attr("checked"))
        {
            check.push('bar', 'restaurant');
        }
        if ($("#tourist").attr("checked"))
        {
            check.push('art_gallery', 'campground', 'museum', 'aquarium', 'zoo', 'library', 'amusement_park', 'park');
        }
        if ($("#shopping").attr("checked"))
        {
            check.push('shopping_mall', 'shoe_store', 'jewelry_store', 'grocery_or_supermarket', 'furniture_store', 'electronics_store', 'clothing_store', 'book_store');
        }


        //alert(map.getZoom());
        //map.setCenter(map.getCenter());
        //map.setZoom(map.getZoom());

        var bounds = new google.maps.LatLngBounds();
        map.fitBounds(bounds);

        var request = {
            location: latlng,
            radius: '50000',
            types: check
        };

        service = new google.maps.places.PlacesService(map);
        service.nearbySearch(request, callback);

    }
    else
    {
//MAP

        var latlng = new google.maps.LatLng($("#latitude").val(), $("#longitude").val());



    }

    var image = new google.maps.MarkerImage("http://google.com/mapfiles/kml/paddle/red-circle.png", new google.maps.Size(32, 37), new google.maps.Point(0, 0), new google.maps.Point(16, 37), new google.maps.Size(32, 37));
    var options = {
        zoom: 10,
        center: latlng,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };

    map = new google.maps.Map(document.getElementById("map_canvas"), options);

    //GEOCODER
    geocoder = new google.maps.Geocoder();

    marker = new google.maps.Marker({
        map: map,
        draggable: true,
        icon: image

    });

    marker.setPosition(latlng); // setting position of marker on load

    google.maps.event.addListener(map, "click", function(event)
    {
        // place a marker


        // display the lat/lng in your form's lat/lng fields
        document.getElementById("latitude").value = event.latLng.lat();
        document.getElementById("longitude").value = event.latLng.lng();

        placeMarker(event.latLng);

        var lats = event.latLng.lat();
        var longs = event.latLng.lng();


        geocoder.geocode({'latLng': event.latLng}, function(results, status) {

            if (status == google.maps.GeocoderStatus.OK)
            {

                $("#address").val(results[0]['formatted_address']);


            } else {
                alert("Geocode was not successful for the following reason: " + status);
            }



            initialize(lats, longs);

        });


    });










}   //initialize function ends



function placeMarker(location) {
    // first remove all markers if there are any
    //deleteOverlays();
    marker.setPosition(location);

    //markersArray.push(marker);

}


function callback(results, status)
{

    if ($("input:checked").length > 0)  //condition fpr checking whether checkboxes are checked(if checked then show the results)
    {



        if (status == google.maps.places.PlacesServiceStatus.OK)
        {
            for (var i = 0; i < results.length; i++)
            {

                $("#expo").val($("#expo").val() + "<br>" + results[i]['icon']);
                var tmp = new google.maps.LatLng(results[i]['geometry']['location'].lat(), results[i]['geometry']['location'].lng());
                img = new google.maps.MarkerImage(results[i]['icon'], new google.maps.Size(32, 37), new google.maps.Point(0, 0), new google.maps.Point(16, 37), new google.maps.Size(32, 37));
                createMarker(tmp);
            }
        }


    }  // condition for checking whether checkboxes are checked or not

}



//function for creating markers
function createMarker(latlng)
{

    var marker = new google.maps.Marker({
        map: map,
        icon: img
    });

    marker.setPosition(latlng);

    //deleteOverlays();
    //placeMarker(latlng);


    markersArray.push(marker);
    //  showOverlays();		  


}


//delete the earlier markers
function deleteOverlays()
{
    if (markersArray)
    {
        for (i in markersArray)
        {
            markersArray[i].setMap(null);
        }
        markersArray.length = 0;
    }
}

// Shows any overlays currently in the array
function showOverlays() {


    if (markersArray)
    {
        for (i in markersArray)
        {
            markersArray[i].setMap(map);

        }

    }
}


$(document).ready(function() {

    initialize();

    $(function() {

        $("#address").autocomplete({
            //This bit uses the geocoder to fetch address values
            source: function(request, response) {
                geocoder.geocode({'address': request.term}, function(results, status) {
                    response($.map(results, function(item) {
                        return {
                            label: item.formatted_address,
                            value: item.formatted_address,
                            latitude: item.geometry.location.lat(),
                            longitude: item.geometry.location.lng()
                        }
                    }));
                })
            },
            //This bit is executed upon selection of an address
            select: function(event, ui) {
                $("#latitude").val(ui.item.latitude);
                $("#longitude").val(ui.item.longitude);
                var location = new google.maps.LatLng(ui.item.latitude, ui.item.longitude);
                marker.setPosition(location);
                map.setCenter(location);
            }
        });
    });

    if (document.getElementById('latitude').value != "")
    {
        var latitude = document.getElementById('latitude').value;
        var longitude = document.getElementById('longitude').value;
        var location = new google.maps.LatLng(latitude, longitude);
        marker.setPosition(location);
        map.setCenter(location);
    }

    //Add listener to marker for reverse geocoding
    google.maps.event.addListener(marker, 'drag', function() {

        geocoder.geocode({'latLng': marker.getPosition()}, function(results, status) {

            if (status == google.maps.GeocoderStatus.OK) {
                if (results[0]) {
                    $('#address').val(results[0].formatted_address);
                    $('#latitude').val(marker.getPosition().lat());
                    $('#longitude').val(marker.getPosition().lng());
                }
            }
        });
    });


    //checkbox function

    $('input:checkbox').change(function() {

        var lat = $("#latitude").val();
        var lng = $("#longitude").val();
        initialize(lat, lng);



    });




});



