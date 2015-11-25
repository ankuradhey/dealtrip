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
var gMap;
var LAT = [];
var LONG = [];
var latlng;
var Content = [];
var latlng_flag = [];
var ppty = [];
var image;
function initialize()
{

    var init = $("#gField").val().split("|||");
//    alert(init);

if(init!="")
    {
    init = init[0].split(",");


    latlng = new google.maps.LatLng(init[0], init[1]);
    }
    else
        {
            latlng = new google.maps.LatLng(43, 0);
        }

//MAP

    //************ CORE CODE STARTS************//
    //*****************************************//
    image = new google.maps.MarkerImage(SITEURL + "images/map_marker.png", new google.maps.Size(32, 37), new google.maps.Point(0, 0), new google.maps.Point(16, 37), new google.maps.Size(32, 37));
    var options = {
        zoom: 6,
        center: latlng,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };

    map = new google.maps.Map(document.getElementById("map_canvas"), options);

    /*	marker = new google.maps.Marker({
     map: map,
     draggable: true,
     icon:image
     });
     */


    //GEOCODER
    geocoder = new google.maps.Geocoder();
    //************ CORE CODE ENDS ************//
    //*****************************************//	


    gMap = $("#gField").val();

    var gMapArr = gMap.split("|||");

    for (var i = 0; i < gMapArr.length; i++)
    {
        var tempo = gMapArr[i].split(",");


        LAT[i] = tempo[0];
        LONG[i] = tempo[1];




        latlng = new google.maps.LatLng(LAT[i], LONG[i]);
        var ppty = tempo[2];
        //event listener ends



        createMarker(latlng, ppty);





    }
}   //initialize function ends


//function for creating markers
function createMarker(latlng, ppty)
{
    marker = new google.maps.Marker({
        map: map,
        draggable: false,
        icon: image
    });

    var content = "";

    $.ajax({
        url: SITEURL + "search/getpptydetails/ppty/" + ppty,
        success: function(data)
        {
            data = $.trim(data);
            content = data;
        }

    });


    var myOptions = {
        content: content
                , disableAutoPan: false
                , maxWidth: 0
                , pixelOffset: new google.maps.Size(-140, 0)
                , zIndex: null
                , boxStyle: {
            width: "199px",
            position: 'relative',
            left: '-50%',
            top: '-35px',
            color: '#ffffff',
            padding: '2px',
            borderRadius: '5px',
            background: '#7d7e7d',
            background: 'url(data:image/svg+xml"base64,PD94bWwgdmVyc2lvbj0iMS4wIiA/Pgo8c3ZnIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgdmlld0JveD0iMCAwIDEgMSIgcHJlc2VydmVBc3BlY3RSYXRpbz0ibm9uZSI+CiAgPGxpbmVhckdyYWRpZW50IGlkPSJncmFkLXVjZ2ctZ2VuZXJhdGVkIiBncmFkaWVudFVuaXRzPSJ1c2VyU3BhY2VPblVzZSIgeDE9IjAlIiB5MT0iMCUiIHgyPSIwJSIgeTI9IjEwMCUiPgogICAgPHN0b3Agb2Zmc2V0PSIwJSIgc3RvcC1jb2xvcj0iIzdkN2U3ZCIgc3RvcC1vcGFjaXR5PSIxIi8+CiAgICA8c3RvcCBvZmZzZXQ9IjEwMCUiIHN0b3AtY29sb3I9IiMwZTBlMGUiIHN0b3Atb3BhY2l0eT0iMSIvPgogIDwvbGluZWFyR3JhZGllbnQ+CiAgPHJlY3QgeD0iMCIgeT0iMCIgd2lkdGg9IjEiIGhlaWdodD0iMSIgZmlsbD0idXJsKCNncmFkLXVjZ2ctZ2VuZXJhdGVkKSIgLz4KPC9zdmc+)',
                    backgroundImage: '-moz-linear-gradient(top,  #7d7e7d 0%, #0e0e0e 100%)',
            backgroundImage: '-webkit-gradient(linear, left top, left bottom, color-stop(0%,#7d7e7d), color-stop(100%,#0e0e0e))',
                    /*background-image: -webkit-gradient(linear, left top, left bottom, from(#9e9e9e), to(#454545));
                     background-image: -webkit-linear-gradient(top, #9e9e9e, #454545);
                     */
                    background: '-webkit-linear-gradient(top,  #7d7e7d 0%,#0e0e0e 100%)',
                    background: '-o-linear-gradient(top,  #7d7e7d 0%,#0e0e0e 100%)',
                    background: '-ms-linear-gradient(top,  #7d7e7d 0%,#0e0e0e 100%)',
                    background: 'linear-gradient(to bottom,  #7d7e7d 0%,#0e0e0e 100%)',
                    filter: 'progid:DXImageTransform.Microsoft.gradient( startColorstr="#7d7e7d", endColorstr="#0e0e0e",GradientType=0 )'

        }
        , closeBoxMargin: "10px 2px 2px 2px"
                , closeBoxURL: "http://www.google.com/intl/en_us/mapfiles/close.gif"
                , infoBoxClearance: new google.maps.Size(1, 1)
                , isHidden: false
                , pane: "floatPane"
                , enableEventPropagation: false
    };




    google.maps.event.addListener(marker, 'click', function() {

        var ib = new InfoBox(myOptions);



        //infowindow = new google.maps.InfoWindow();
        ib.setContent(content); // contentString can be html as far as i  know whose style you can override
        ib.setPosition(latlng);
        //infowindow.open(map);
        ib.open(map);
    });



    marker.setPosition(latlng);

    markersArray.push(marker);
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


    gMap = $("#gField").val();

    var gMapArr = gMap.split("|||");


});



// JavaScript Document