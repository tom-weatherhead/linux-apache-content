function receiveAllAuthors_callback(authors) {
    $("#allAuthors").empty();
    $("#allAuthors").append("<tr><th>ID</th><th>Last Name</th><th>First Name</th><th>Read</th><th>Update</th><th>Delete</th></tr>");

    $.each(authors, function(i, author) {
        $("#allAuthors").append("<tr><td>" + author.id + "</td><td>" + author.last_name + "</td><td>" + author.first_name + "</td>"
            + '<td><a href="read.html?id=' + author.id + '">Read</a></td>'
            + '<td><a href="update.html?id=' + author.id + '">Update</a></td>'
            + '<td><input type="button" value="Delete" onclick="btnDelete_onClick(' + author.id + ')" /></td></tr>');
    });
}

$(document).ready(function() {
    $.ajax({
        //type: "GET", // This is not needed, since "GET" is the default type.
        url: "../service/",
        dataType: "json", // We expect to receive JSON from the service
        success: receiveAllAuthors_callback,
        error: function(e) {
            alert("Read failed: " + e.status + ": " + e.statusText + ": " + e.responseJSON);
        }
    });
});

function btnDelete_onClick(id) {

    if (!confirm("Delete record " + id + "?")) {
        return;
    }

    $.ajax({
        type: 'DELETE',
        url: '../service/' + id,
        dataType: "json", // We expect to receive JSON from the service
        success: function (data) {
            alert("Delete succeeded: " + data);

            // The "true" parameter means: Reload the page from the server, not the browser's cache.
            // See http://stackoverflow.com/questions/5404839/how-can-i-refresh-a-page-with-jquery
            location.reload(true);
        },
        error: function(e) {
            //alert("Delete failed: " + e);
            alert("Delete failed: " + e.status + ": " + e.statusText + ": " + e.responseJSON);
        }
    });
}
