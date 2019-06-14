$(document).ready(function(){
    //confirm на удаление
    $('#contacts-offices__remove').click(function(){
        var office_id = customParseInt($(this).attr('data-office-id'));
        var current_status = customParseInt($(this).attr('data-current-status'));
        $('#confirm').modal('show');
        if (current_status) {
            $("#confirm .confirm-content").html("Вы действительно хотите удалить данную должность?");
        } else {
            $("#confirm .confirm-content").html("Вы действительно хотите восстановить данную должность?");
        }
        
        $('#confirm .btn-success').one('click', function(e){
            loader(true);
            $.get("/contacts-offices/remove/",
                {
                    id : office_id ,
                    current_status : current_status,   
                },
                function(data){
                    $('#confirm').modal('hide');
                    $('#contacts-offices__remove').text((current_status) ? 'Восстановить' : 'Удалить').attr('data-current-status', (current_status) ? 0 : 1);
                    loader(false);
                },
                'json'
            );
        });
    });
});