// loader(true);
$(document).ready(function (e) {
    var vlan = $('#vlan');
    var ip = $('#address');
    var mac = $('#mac');
    var swconf = $('#swconf');
    var leases = $('#leases');
    var remlease = $('#removeLease');


    function FormFields(bool) {
        if(bool){
            ip.attr("disabled","disabled");
            mac.attr("disabled","disabled");
            leases.attr("disabled","disabled");
            remlease.attr("disabled","disabled");
            swconf.trigger('reset');
        }else{

        }
    }
    function GetVlans() {
        $.ajax({
            type: "post",
            url: "/tools/swconf/ajax",
            data:{
                'play':'GetVlans'
            },

            dataType:'json',
            // async:false,
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                console.log(textStatus);
            },
            success: function(result){
                vlan.html(result.html);
                loader(false);
                // $("#loader").attr('hidden',true);
                // $('#row_'+net).html("<tr><td>" + result + "</td></td>");
                // $('#vlan').html(result.vlan);
                // $('#addr').html(result.address);
            }
        });
    }
    GetVlans();
    leases.on('change',function () {
        if($(this).val()!=0) {
            remlease.removeAttr("disabled");
            // loader(true);
            /*$.ajax({
                type: "post",
                url: "/tools/swconf/ajax",
                data: {
                    'play': 'GetAddresses',
                    'vlan': $(this).val()
                },

                dataType:'json',
                // async:false,
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    console.log(textStatus);
                },
                success: function (result) {
                    ip.html(result.html).removeAttr('disabled').focus();
                    leases.html(result.leases).removeAttr('disabled');
                    console.log(result.leases);
                    loader(false);
                    // $("#loader").attr('hidden',true);
                    // $('#row_'+net).html("<tr><td>" + result + "</td></td>");
                    // $('#vlan').html(result.vlan);
                    // $('#addr').html(result.address);
                }
            });*/
        }else{
            remlease.attr("disabled","disabled");
            // ip.attr("disabled","disabled");
            // mac.attr("disabled","disabled");
            // swconf.trigger('reset');
        }
    });

    remlease.on('click',function () {
        loader(true);
        $.ajax({
            type: "post",
            url: "/tools/swconf/ajax",
            data: {
                'play': 'RemoveLease',
                'ip': leases.val(),
                'rid': leases.attr('rid')
            },

            // dataType:'json',
            // async:false,
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                console.log(textStatus);
            },
            success: function (result) {
                console.log(result);
                FormFields(true);
                loader(false);
                // $("#loader").attr('hidden',true);
                // $('#row_'+net).html("<tr><td>" + result + "</td></td>");
                // $('#vlan').html(result.vlan);
                // $('#addr').html(result.address);
            }
        });
    });

    vlan.on('change',function () {
        console.log($(this).val());
        if($(this).val()!=0) {
            loader(true);
            $.ajax({
                type: "post",
                url: "/tools/swconf/ajax",
                data: {
                    'play': 'GetAddresses',
                    'vlan_id': $(this).val()
                },

                dataType:'json',
                // async:false,
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    console.log(textStatus);
                    loader(false);
                    $('#address').addClass('alert-danger');
                },
                success: function (result) {
                    ip.html(result.html).removeAttr('disabled').focus();
                    leases.html(result.leases).removeAttr('disabled');
                    console.log(result);
                    $('#address').removeClass('alert-danger');
                    loader(false);
                    // $("#loader").attr('hidden',true);
                    // $('#row_'+net).html("<tr><td>" + result + "</td></td>");
                    // $('#vlan').html(result.vlan);
                    // $('#addr').html(result.address);
                },
                timeout:20000,
            });
        }else{
            FormFields(true);

            // ip.attr("disabled","disabled");
            // mac.attr("disabled","disabled");
            // swconf.trigger('reset');
        }
    });

    ip.on('change',function () {
        if($(this).val()!=0) {
            mac.removeAttr('disabled').focus();
            //http://10.60.248.21/swconfig.php?text=IP:10.60.0.4;A8:F9:4B:2C:B2:80;SRV:sw_vlan_2221;HN:MES2024
        }
    });


    var templength = (mac.val()).length;
    mac.on('keydown',function (event) {
        var length = (mac.val()).length;
        var temp;
       /* if(event.keyCode!=8) {
        // console.log(event);
            if(length<17){
                if (length == 11) {
                    mac.val(mac.val() + ":");
                    templength = (mac.val()).length;
                }
                if (length == 14) {
                    mac.val(mac.val() + ":");
                    templength = (mac.val()).length;
                }
            }
        }else{
            if(length<10){
                event.preventDefault();
            }
        }*/
    });

    swconf.submit(function (e) {
        e.preventDefault();
        loader(true);
        var post = "&play=GetTemplate&" + swconf.serialize();
        console.log(post);
        $.ajax({
            type: "post",
            url: "/tools/swconf/ajax",
            data: post,

            // dataType:'json',
            // async:false,
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                console.log(textStatus);
            },
            success: function (result) {
                $('#result').val(result);
                FormFields(true);
                // ip.attr("disabled","disabled");
                // mac.attr("disabled","disabled");
                // swconf.trigger('reset');
                // $('#address').html(result.html).removeAttr('disabled').focus();
                // console.log(result);
                loader(false);
                // $("#loader").attr('hidden',true);
                // $('#row_'+net).html("<tr><td>" + result + "</td></td>");
                // $('#vlan').html(result.vlan);
                // $('#addr').html(result.address);
            }
        });

    })

});