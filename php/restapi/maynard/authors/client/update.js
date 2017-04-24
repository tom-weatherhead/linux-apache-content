// getID() is duplicated in update.js; TODO: Consolidate down to one copy of this code.

function getID() {
    var indexOfQuestionMark = window.location.href.indexOf('?');

    if (indexOfQuestionMark < 0) {
        return 0; // Error
    }

    var hashes = window.location.href.slice(indexOfQuestionMark + 1).split('&');

    for (var i = 0; i < hashes.length; i++) {
        hash = hashes[i].split('=');

        if (hash[0] == "id") {
            return unescape(hash[1]);
        }
    }

    return 0; // Error
}

function receiveAuthorData_callback(author, status) {

    if (status != "success") {
        alert("receiveAuthorData_callback failed: " + status);
        return;
    }

    $("#first_name").val(author.first_name);
    $("#last_name").val(author.last_name);
    $("#birth_year").val(author.birth_year);
    $("#death_year").val(author.death_year);
}

$(document).ready(function() {
    id = getID();

    if (id == 0) {
        alert("Missing or invalid id parameter");
        return;
    }

    $.ajax({
        //type: "GET", // This is not needed, since "GET" is the default type.
        url: "../service/" + id,
        dataType: "json", // We expect to receive JSON from the service
        success: receiveAuthorData_callback,
        error: function(e) {
            alert("Read failed: " + e.status + ": " + e.statusText + ": " + e.responseJSON);
        }
    });
});

function btnUpdate_onClick() {
    id = getID();

    if (id == 0) {
        alert("Missing or invalid id parameter");
        return;
    }

    $.ajax({
        type: "PUT",
        url: "../service/" + id,
        dataType: "json",                // We expect to receive JSON from the service
        contentType: "application/json", // We are sending JSON to the service
        data: JSON.stringify({
            first_name: $("#first_name").val(),
            last_name:  $("#last_name").val(),
            birth_year: $("#birth_year").val(),
            death_year: $("#death_year").val()
        }),
        success: function (data) {
            alert("Update succeeded: " + data);
            window.location.href = "index.html";
        },
        error: function(e) {
            //alert("Update failed: " + e);
            alert("Update failed: " + e.status + ": " + e.statusText + ": " + e.responseJSON);
        }
    });
}
