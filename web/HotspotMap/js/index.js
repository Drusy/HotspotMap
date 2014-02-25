var map;

function addGreenMarker(title, pos) {
    var marker = new google.maps.Marker({
        position: pos,
        map: map,
        title: title,
        icon: 'Hotspotmap/images/green_marker.png',
        zIndex: 999
    });
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

function toggleMap() {
    $("#caract").toggle();
    $("#map-info").toggle();
    $("#save-hotspot").toggle();
    if($("#map").hasClass("view"))
    {
        $("#map").removeClass("view");
        $("#map").addClass("edit");
    }
    else
    {
        $("#map").removeClass("edit");
        $("#map").addClass("view");
    }
    setTimeout(
        function()
        {
            google.maps.event.trigger(map, 'resize');
        }, 1100
    );
}

$("#add-hotspot").click(function() {
    if($("#map").hasClass("view"))
    {
        $(this).text("cancel");
    } else {
        $(this).text("add your hotspot");
    }

    toggleMap();
});

$('#save-hotspot').click(function() {
    $('#add-form').submit();
});

$('#add-form').on('submit', function(event) {
    event.preventDefault();

    $.ajax({
        url: "/places",
        type: "POST",
        dataType: "text",
        data: $(this).serialize(),
        headers: {
            Accept : "application/json"
        }
    })
    .done(function( data ) {
        var json = $.parseJSON(data);

        toggleMap();
        $("#add-hotspot").text("add your hotspot");

        setTimeout(
            function()
            {
                addGreenMarker(json.town, new google.maps.LatLng(json.latitude, json.longitude));
                updateFromJson(json);
            }, 1150
        );
    })
    .fail(function(jqXHR, textStatus, errorThrown) {
        alert( "Request failed: " + textStatus + " " + errorThrown );
    });

    // Prevent form submit
    return false;
});

function updateFromJson(json) {
    map.setCenter(new google.maps.LatLng(json.latitude, json.longitude));

    $( "#addressInput" ).val(json.address);
    $( "#latitudeInput" ).val(json.latitude);
    $( "#longitudeInput" ).val(json.longitude);

    $( "#latitudeAddInput" ).val(json.latitude);
    $( "#longitudeAddInput" ).val(json.longitude);
    $( "#addressAddInput" ).val(json.address);
    $( "#countryAddInput" ).val(json.country);
    $( "#townAddInput" ).val(json.town);
    $( "#nameAddInput" ).val(json.name);
    $( "#websiteAddInput" ).val(json.website);
    $( "#descriptionAddInput" ).val(json.description);
}

function updateFromClientInfo() {
    var request = $.ajax({
        url: "/userInfo",
        type: "GET",
        dataType: "text",
        headers: {
            Accept : "application/json"
        }
    })

    request.done(function( data ) {
        var json = $.parseJSON(data);

        updateFromJson(json);
    })

    request.fail(function(jqXHR, textStatus, errorThrown) {
        alert( "Request failed: " + textStatus + " " + errorThrown );
    });
}

function updateFromPlaceId(placeId) {
    var request = $.ajax({
        url: "/places/" + placeId,
        type: "GET",
        dataType: "text",
        headers: {
            Accept : "application/json"
        }
    })

    request.done(function( data ) {
        var json = $.parseJSON(data);

        updateFromJson(json);
    })

    request.fail(function(jqXHR, textStatus, errorThrown) {
        alert( "Request failed: " + textStatus + " " + errorThrown );
    });
}

$(".clickablePlace").click(function() {
    var placeId = $(this).attr('id');

    if (placeId == 'clientPosition') {
        updateFromClientInfo();
    } else {
        updateFromPlaceId(placeId);
    }
});