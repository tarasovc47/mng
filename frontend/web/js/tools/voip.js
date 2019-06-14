var getUrlParameter = function getUrlParameter(sParam) {
    var sPageURL = decodeURIComponent(window.location.search.substring(1)),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : sParameterName[1];
        }
    }
};

function ClickDiv(varsa) {
    console.log(varsa);
}


var perc_of_digits = 0.3;
function randomInRange(minVal,maxVal)
{
    var randVal = minVal+(Math.random()*(maxVal-minVal));
    return Math.round(randVal)
}

function getCharInRange(from,till){
    ret = String.fromCharCode( randomInRange(from,till) );
    return ret
}

function genPasswd(passwd_len){
    var ret = '';
    var first_iter = 1;
    while( passwd_len > 0 ){

        if(!first_iter && Math.random() < perc_of_digits ){
            ret+= getCharInRange(48,57)
        }
        else{
            if( Math.random() > 0.5 ){
                // upper
                ret+= getCharInRange(65,90)
            }
            else{
                // lower
                ret+= getCharInRange(97,122)
            }
        }

        first_iter = 0;
        passwd_len--;
    }
    return ret
}

function setPasswd(obj_id){
    var set_obj = document.getElementById(obj_id);
    set_obj.value = genPasswd(20)
}

function Changed(id){
        $('#changed' + $('input[data-port-id=' + id + ']').attr('data-port-id')).val('1');
}

function TrigContext(id) {
    if($('#state' + id).prop('checked')) {
        $('#context' + id).removeAttr("disabled");
    }else{
        // $('#context' + id).attr("disabled","disabled");
    }

}
function Deleting(id) {
    console.log(id);
    if($('#del' + id).prop('checked')) {
        $('#tr' + id).removeClass("success").addClass('danger');
    }else{
        $('#tr' + id).removeClass("danger").addClass('success');
    }

}

$(document).ready(function (e) {

    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    });
    $('#l' + getUrlParameter('mac')).click();


    $('#voipgates').on('shown.bs.collapse', function () {
        loader(true);
        var mac = $('.collapse.in').attr('id');
        var devtype = $('.collapse.in').attr('data-devtype');
        var dbID = $('.collapse.in').attr('data-db-id');
        var ip = $('.collapse.in').attr('data-ip');
        console.log(mac);

        $.ajax({
            type: "post",
            url: "/tools/voip/device",
            data:{
                'mac':mac,
                'devtype':devtype,
                'deviceId':dbID,
                'ip':ip,
            },
            // dataType:'json',
            // async:false,
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                console.log(textStatus);
            },
            success: function(result){
                $("#body" + mac).html(result);
                loader(false);
            }
        });

        // GetArps(net,rid);
    }).on('show.bs.collapse', function () {
        $('.list-group').html('<li class="list-group-item text-center" ><i class="fa fa-spin fa-spinner fa-pulse fa-fw fa-2x text-info"></i></li>');
    });


    // $('#voipgates').on('hidden.bs.collapse', function () {
    //     console.log($('.collapse .in').attr('id'));
    //
    // })


});