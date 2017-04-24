// publisher.js - March 17, 2012

function ddlPublisherNames_onChange() {
    var url = "publisher.php";
    var authorID = $("#ddlPublisherNames").val();

    if (authorID != "00000000-0000-0000-0000-000000000000") {
        url = url + "?id=" + authorID;
    }

    window.location = url;
}

// **** End of File ****