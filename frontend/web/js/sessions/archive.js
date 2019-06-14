'use strict';
String.prototype.replaceAll = function(search, replace){
    return this.split(search).join(replace);
};
function ConvertMAC(mac){   // to abcd.efgh.klmn  - format
    function MakeRightFormat(tmp) {
        return tmp.substring(0, 4) + "." + tmp.substring(4,8) + "." + tmp.substring(8,12);
    }
    if(mac.length<12){
        return mac;
    }

    if(mac.indexOf('.')+1) {
        var MAC = mac;
    }else{
        if (mac.indexOf(':')+1){
            MAC = MakeRightFormat(mac.replaceAll(':',''));

        } else {
            if (mac.indexOf('-')+1) {
                MAC = MakeRightFormat(mac.replaceAll('-',''));
                // MAC = mac;
            }else{
                MAC = MakeRightFormat(mac);
                // MAC = implode(":", explode("-", mac));
            }

            // MAC = implode(":", explode(".", mac));
            // MAC = substr(MAC, 0, 2).":"
            //     .substr(MAC, 2, 5).":"
            //     .substr(MAC, 7, 5).":"
            //     .substr(MAC, 12, 2);
        }
    }
    return MAC;
}

function MacWODots(mac) {
    return mac.replaceAll('.','');
}

$(document).on('pjax:success', function() {
    console.log('pjax');
});
$(document).ready(function (e) {


    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });

    var radiusmain_begin = $("#radiusarchsearch-begin");
    var radiusmain_end = $("#radiusarchsearch-end");
    radiusmain_begin.datetimepicker({
        ignoreReadonly : true,
        showClose : true,
        useCurrent: false,
        format : "YYYY-MM-DD HH:mm",
        maxDate: Date.now()
    });

    radiusmain_end.datetimepicker({
        ignoreReadonly : true,
        showClose : true,
        useCurrent: false,
        format : "YYYY-MM-DD HH:mm",
        maxDate: Date.now(),
        defaultDate:Date.now()
    });


    radiusmain_begin.on("dp.change",function (e) {
        radiusmain_end.data("DateTimePicker").minDate(e.date);
    });
    radiusmain_end.on("dp.change",function (e) {
        radiusmain_begin.data("DateTimePicker").maxDate(e.date);
    });

});
