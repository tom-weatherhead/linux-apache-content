// category.js - March 17, 2012

function ddlCategoryNames_onChange() {
    var url = "category.php";
    var authorID = $("#ddlCategoryNames").val();

    if (authorID != "00000000-0000-0000-0000-000000000000") {
        url = url + "?id=" + authorID;
    }

    window.location = url;
}

// **** End of File ****