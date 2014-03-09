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
            icon: 'HotspotMap/images/green_marker.png',
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
        icon: 'HotspotMap/images/blue_marker.png',
        zIndex: 999
    });
}

function addMarkerListener(marker) {
    google.maps.event.addListener(marker, 'click', function() {
        currentPos = marker.getPosition();

        allowUpdateForId(marker.id);
        setToEditMode();
    });
}

function allowUpdateForId(id) {
    var timeout = 1150;

    if (viewModeIs("edit"))
        timeout = 0;

    if (updateInputFromURI("/places/" + id))
    {
        currentPlaceId = id;
        resizeAndCenterMap(timeout);
        getComments();
    }
}

function resizeAndCenterMap(timeout) {
    setTimeout(
        function()
        {
            google.maps.event.trigger(map, 'resize');
            map.panTo(currentPos);
        }, timeout
    );
}

function viewModeIs(mode) {
    return $("#map").hasClass(mode);
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
        var timeout = 0;

        if (!viewModeIs("view"))
            timeout = 1150;

        $( "#addressInput" ).val(json.address);
        $( "#latitudeInput" ).val(json.latitude);
        $( "#longitudeInput" ).val(json.longitude);

        currentPos = new google.maps.LatLng(json.latitude, json.longitude);


        resizeAndCenterMap(timeout);
        setToViewMode()
    })

    request.fail(function(jqXHR, textStatus, errorThrown) {
        popupError();
    });

    // Prevent form submit
    return false;
});

$('#comment-form').on('submit', function(event) {
    event.preventDefault();
    $(".mfp-close").first().trigger("click");

    var request = $.ajax({
        url: "/places/"+currentPlaceId+"/comment",
        type: "POST",
        dataType: "text",
        data: $('#comment-form').serialize(),
        headers: {
            Accept : "application/json"
        }
    })

    request.done(function( data ) {
        addPopupAddSuccess();
        //TODO : CLEAR FORM
    })

    request.fail(function(jqXHR, textStatus, errorThrown) {
        popupError();
    });

    // Prevent form submit
    return false;
});

$("#add-hotspot").click(function() {
    setToAddMode();
    clearAddUpdateForm();
    resizeAndCenterMap(1150);
    currentPlaceId = null;
});

$('#update-hotspot').click(function() {
    $('#add-form').submit();
});

$('#save-hotspot').click(function() {
    $('#add-form').submit();
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

function fillComment(content, author, date, avatar)
{
    var articleComment = '<article class="comment">\
             <a class="comment-img" href="#non">\
             <img src="##AVATAR" alt="" width="50" height="50">\
             </a>\
             <div class="comment-body">\
             <div class="text">\
             <p>##COMMENT</p>\
             </div>\
             ##BY\
             </div>\
             </article>';

    var by = '<p class="attribution">by <a href="#non">##AUTHOR</a> at ##DATE</p>';

    articleComment = articleComment.replace("##COMMENT", content);

    if(!avatar)
        avatar="HotspotMap/images/avatar_hsm.png";

    articleComment = articleComment.replace("##AVATAR", avatar);

    if(author && date)
    {
        by = by.replace("##AUTHOR", author);
        by = by.replace("##DATE", date);
    }
    else
        by = "";

    articleComment = articleComment.replace("##BY", by);

    return articleComment;
}

function getComments() {
    var request = $.ajax({
        url: "/places/" +currentPlaceId+"/comments",
        type: "GET",
        dataType: "text",
        headers: {
            Accept : "application/json"
        }
    })

    request.done(function( data ) {
        var json = $.parseJSON(data);

        $("section.comments").empty();

        if( json.length == 0 )
        {
            $("section.comments").append(fillComment("No comment for this place. Be the first to add one.", '', '', ''));
        }
        else
            $.each(json, function(){
                $("section.comments").append(fillComment(this.content, this.author, this.creation_date, this.avatar));
            });

    });

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

        setTimeout(
            function()
            {
                addGreenMarker(json.id, json.name, new google.maps.LatLng(json.latitude, json.longitude));
                updateFromJson(json);
                addPopupAddSuccess();
                currentPlaceId = json.id;
                setToEditMode();
                resizeAndCenterMap(1150);
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
    //map.panTo(currentPos);

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

function setToViewMode() {
    $("#map").removeClass("add");
    $("#map").removeClass("edit");
    $("#map").addClass("view");

    $("#update-hotspot").hide();
    $("#save-hotspot").hide();
    $("#cancel-hotspot").hide();
    $("#comments-all").hide();
    $("#twitter-mess").hide();
    $("#caract").hide();
    $("#map-info").hide();

    $("#add-hotspot").show();
}

function setToEditMode() {
    $("#map").removeClass("add");
    $("#map").removeClass("view");
    $("#map").addClass("edit");

    $("#save-hotspot").hide();
    $("#add-hotspot").hide();

    $("#update-hotspot").show();
    $("#cancel-hotspot").show();
    $("#comments-all").show();
    $("#twitter-mess").show();
    $("#caract").show();
    $("#map-info").show();
}

function setToAddMode() {
    $("#map").removeClass("edit");
    $("#map").removeClass("view");
    $("#map").addClass("add");

    $("#update-hotspot").hide();
    $("#comments-all").hide();
    $("#twitter-mess").hide();
    $("#add-hotspot").hide();

    $("#caract").show();
    $("#map-info").show();
    $("#save-hotspot").show();
    $("#cancel-hotspot").show();
}

$("#cancel-hotspot").click(function() {
    setToViewMode();
    resizeAndCenterMap(1150);
});

$(".clickablePlace").click(function() {
    var placeId = $(this).attr('id');

    if (placeId == 'clientPosition') {
        currentPlaceId = null;
        updateInputFromURI("/userInfo");
        resizeAndCenterMap(1150);
    } else {
        allowUpdateForId(placeId);
    }

    setToEditMode();
});

$("#addressInput").on('focus', function() {
    $( "#addressInput" ).val("");
    $( "#latitudeInput" ).val("");
    $( "#longitudeInput" ).val("");
});

$('.popup-with-form').magnificPopup({
    type: 'inline',
    preloader: false,
    focus: '#name',

    // When elemened is focused, some mobile browsers in some cases zoom in
    // It looks not nice, so we disable it:
    callbacks: {
        beforeOpen: function() {
            if($(window).width() < 700) {
                this.st.focus = false;
            } else {
                this.st.focus = '#name';
            }
        }
    }
});