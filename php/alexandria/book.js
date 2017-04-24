// book.js - March 17, 2012

function ddlBookTitles_onChange() {
    var url = "book.php";
    var authorID = $("#ddlBookTitles").val();

    if (authorID != "00000000-0000-0000-0000-000000000000") {
        url = url + "?id=" + authorID;
    }

    window.location = url;
}

// **** End of File ****