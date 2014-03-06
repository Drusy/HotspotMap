var map;
var currentPos;
var currentPlaceId;
var markers = [];

$("#address-tooltip").hover(
    function() {
        $(this).tooltip('show')
    }
);

function popupError() {
    $.pnotify({
        title: "Can't retrieve the place",
        text: 'An error occurred while processing your request',
        addclass: 'pnotify-error',
        delay: 2000,
        opacity: 0.9,
        icon: 'glyphicon glyphicon-exclamation-sign'
    });
}

function addPopupUpdateSuccess() {
    $.pnotify({
        title: 'Place updated',
        text: 'The place have been updated successfully',
        addclass: 'pnotify-success',
        delay: 2000,
        opacity: 0.9,
        icon: 'glyphicon glyphicon-ok'
    });
}

function addPopupAddSuccess() {
    $.pnotify({
        title: 'Place added',
        text: 'The place have to be moderate by an administrator first.',
        addclass: 'pnotify-success',
        delay: 2000,
        opacity: 0.9,
        icon: 'glyphicon glyphicon-ok'
    });
}

function addGreenMarker(id, title, pos) {
    var marker = new google.maps.Marker({
            position: pos,
            map: map,
            title: title,
            icon: 'Hotspotmap/images/green_marker.png',
            zIndex: 999
    });
    addMarkerListener(marker);
    marker.id = id;
    markers[marker.id] = marker;
}

function addBlueMarker(title, pos) {
    var marker = new google.maps.Marker({
        position: pos,
        map: map,
        title: title,
        icon: 'Hotspotmap/images/blue_marker.png',
        zIndex: 999
    });
}

function addMarkerListener(marker) {
    google.maps.event.addListener(marker, 'click', function() {
        currentPos = marker.getPosition();

        allowUpdateForId(marker.id);
    });
}

function allowUpdateForId(id) {
    if (updateInputFromURI("/places/" + id))
    {
        if(isMapViewMode())
        {
            toggleMap("edit");
            $("#add-hotspot").text("cancel");
            currentPlaceId = id;
            setTimeout(
                function()
                {
                    map.setCenter(currentPos);
                }, 1150
            );
        }
        $("#update-hotspot").show();
        $("#save-hotspot").hide();
    }
}

function isMapViewMode() {
    return $("#map").hasClass("view");
}

function toggleMap(type) {
    $("#caract").toggle();
    $("#map-info").toggle();
    if(isMapViewMode())
    {
        $("#map").removeClass("view");
        $("#map").addClass(type);
    }
    else
    {
        $("#map").removeClass(type);
        $("#map").addClass("view");
    }
    setTimeout(
        function()
        {
            google.maps.event.trigger(map, 'resize');
        }, 1100
    );
}

$('#search-form').on('submit', function(event) {
    event.preventDefault();

    var request = $.ajax({
        url: "/places/find",
        type: "POST",
        dataType: "text",
        data: $('#search-form').serialize(),
        headers: {
            Accept : "application/json"
        }
    })

    request.done(function( data ) {
        var json = $.parseJSON(data);

        $( "#addressInput" ).val(json.address);
        $( "#latitudeInput" ).val(json.latitude);
        $( "#longitudeInput" ).val(json.longitude);

        currentPos = new google.maps.LatLng(json.latitude, json.longitude);
        map.setCenter(currentPos);
    })

    request.fail(function(jqXHR, textStatus, errorThrown) {
        popupError();
    });

    // Prevent form submit
    return false;
});

$("#add-hotspot").click(function() {
    if(isMapViewMode())
    {
        $("#save-hotspot").show();
        $("#update-hotspot").hide();
        clearAddUpdateForm();
        $(this).text("cancel");
    } else {
        $("#save-hotspot").hide();
        $("#update-hotspot").hide();
        currentPlaceId = null;
        $(this).text("add your hotspot");
    }

    toggleMap("edit");
});

$('#update-hotspot').click(function() {
    $('#add-form').submit();
});

$('#save-hotspot').click(function() {
    $('#add-form').submit();
});

$('#view-hotspot').click(function() {
    toggleMap("add");
    toggleComment();
});

$('#add-form').on('submit', function(event) {
    event.preventDefault();

    if (currentPlaceId == null) {
        saveHotspot();
    } else {
        updateHotspot();
    }

    // Prevent form submit
    return false;
});

function updateHotspot() {
    var request = $.ajax({
        url: "/places/" + currentPlaceId,
        type: "PUT",
        dataType: "text",
        data: $('#add-form').serialize(),
        headers: {
            Accept : "application/json"
        }
    })

    request.done(function( data ) {
        var json = $.parseJSON(data);
        currentPos = new google.maps.LatLng(json.latitude, json.longitude);

        markers[json.id].setPosition(currentPos);
        updateFromJson(json);
        addPopupUpdateSuccess();
    })

    request.fail(function(jqXHR, textStatus, errorThrown) {
        popupError();
    });
}

function saveHotspot() {
    var request = $.ajax({
        url: "/places",
        type: "POST",
        dataType: "text",
        data: $('#add-form').serialize(),
        headers: {
            Accept : "application/json"
        }
    })

    request.done(function( data ) {
        var json = $.parseJSON(data);

        toggleMap("edit");
        $("#add-hotspot").text("add your hotspot");

        setTimeout(
            function()
            {
                addGreenMarker(json.id, json.name, new google.maps.LatLng(json.latitude, json.longitude));
                updateFromJson(json);
                addPopupAddSuccess();
            }, 1150
        );
    })

    request.fail(function(jqXHR, textStatus, errorThrown) {
        popupError();
    });
}

function clearAddUpdateForm() {
    $( "#latitudeAddInput" ).val("");
    $( "#longitudeAddInput" ).val("");
    $( "#addressAddInput" ).val("");
    $( "#countryAddInput" ).val("");
    $( "#townAddInput" ).val("");
    $( "#nameAddInput" ).val("");
    $( "#websiteAddInput" ).val("");
    $( "#descriptionAddInput" ).val("");
}

function updateFromJson(json) {
    currentPos = new google.maps.LatLng(json.latitude, json.longitude);
    map.setCenter(currentPos);

    $( "#addressInput" ).val(json.address);
    //$( "#latitudeInput" ).val(json.latitude);
    //$( "#longitudeInput" ).val(json.longitude);

    $( "#latitudeAddInput" ).val(json.latitude);
    $( "#longitudeAddInput" ).val(json.longitude);
    $( "#addressAddInput" ).val(json.address);
    $( "#countryAddInput" ).val(json.country);
    $( "#townAddInput" ).val(json.town);
    $( "#nameAddInput" ).val(json.name);
    $( "#websiteAddInput" ).val(json.website);
    $( "#descriptionAddInput" ).val(json.description);
}

function updateInputFromURI(uri) {
    var result = true;

    var request = $.ajax({
        url: uri,
        type: "GET",
        dataType: "text",
        headers: {
            Accept : "application/json"
        },
        async: false
    })

    request.done(function( data ) {
        var json = $.parseJSON(data);

        updateFromJson(json);
    })

    request.fail(function(jqXHR, textStatus, errorThrown) {
        popupError();
        result = false;
    });

    return result;
}

function toggleComment(){

    if($("#map-info").hasClass("col-md-12"))
    {
        $("#map-info").removeClass("col-md-12");
        $("#map-info").addClass("col-md-6");
    }
    else
    {
        $("#map-info").removeClass("col-md-6");
        $("#map-info").addClass("col-md-12");
    }

    $("#comments-all").toggle();
    $("#twitter-mess").toggle();
}

$(".clickablePlace").click(function() {
    var placeId = $(this).attr('id');

    if (placeId == 'clientPosition') {
        currentPlaceId = null;
        if (!isMapViewMode()) {
            $("#update-hotspot").hide();
            $("#save-hotspot").show();
        }
        updateInputFromURI("/userInfo");
    } else {
        allowUpdateForId(placeId);
    }
});