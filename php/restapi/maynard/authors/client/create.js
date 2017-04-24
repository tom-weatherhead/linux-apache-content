function btnCreate_onClick() {
    $.ajax({
        type: "POST",
        url: "../service/",
        dataType: "json",                // We expect to receive JSON from the service
        contentType: "application/json", // We are sending JSON to the service
        data: JSON.stringify({
            first_name: $("#first_name2").val(),
            last_name:  $("#last_name2").val(),
            birth_year: $("#birth_year2").val(),
            death_year: $("#death_year2").val()
        }),
        success: function (data) {
            alert("Create succeeded: " + data);
            window.location.href = "index.html";
        },
        error: function(e) {
            //alert("Create failed: " + e);
            alert("Create failed: " + e.status + ": " + e.statusText + ": " + e.responseJSON);
        }
    });
}
