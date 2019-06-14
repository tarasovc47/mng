$(document).ready(function (e) {
    $("#converter").submit(function (e) {

        e.preventDefault();
        console.log($(this).serialize());
        $.ajax({
            type: "post",
            url: "/tools/converter/ajax",
            data: {
                'type': $("#type").val(),
                "ZyxelConfigText": $("#zyxel").val()
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                console.log(textStatus);
            },
            success: function (result) {
                console.log(result);
                $('#result').html(result)
            }
        });
    });
});