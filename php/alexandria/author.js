// author.js - March 16, 2012

function ddlAuthorNames_onChange() {
    var url = "author.php";
    var authorID = $("#ddlAuthorNames").val();

    if (authorID != "00000000-0000-0000-0000-000000000000") {
        url = url + "?id=" + authorID;
    }

    window.location = url;
}

// **** End of File ****