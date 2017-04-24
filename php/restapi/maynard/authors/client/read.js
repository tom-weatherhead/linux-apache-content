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

function receiveAuthorData_callback(author) {
    $("#authorData").empty();
    $("#authorData").append("<tr><th>Key</th><th>Value</th></tr>");
    $("#authorData").append("<tr><td>First Name</td><td>" + author.first_name + "</td></tr>");
    $("#authorData").append("<tr><td>Last Name</td><td>" + author.last_name + "</td></tr>");
    $("#authorData").append("<tr><td>Birth Year</td><td>" + author.birth_year + "</td></tr>");
    $("#authorData").append("<tr><td>Death Year</td><td>" + author.death_year + "</td></tr>");
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
