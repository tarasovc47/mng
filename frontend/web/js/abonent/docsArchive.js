(function($){    
    $(document).ready(function(){
        $('#docsarchive-client_id').on('change', function(){
            var client_id = $(this).val();
            loader(true);
            $.get("/abonent/docs-archive/get-extra-data-for-client/",
                {
                    client_id : client_id    
                },
                function(data){
                    $('#docsarchive-loki_basic_service_ids').html(data.user_ids_html).trigger('chosen:updated');
                    $('#docsarchive-billing_contract_id').html(data.contracts_html).click();
                    loader(false);
                },
                'json'
            );
        });

        // Подключение датапикеров
        $('#docsarchive-opened_at').datetimepicker({
            ignoreReadonly : true,
            showClose : true,
            useCurrent: false,
            format : "D-MM-YYYY",
        });

        // Подключение чузенов
        $('#docsarchive-loki_basic_service_ids, #docsarchive-service_types, #docsarchive-conn_techs').chosen({
                                                    'no_results_text': 'Нет совпадений',
                                                    'placeholder_text_multiple': ' ',
                                                    'width' : '100%',
                                                });

        $('#docsarchive-load_new_file').click(function(){
            $(this).toggleClass('hidden');
            $('#docsarchive-file').toggleClass('hidden').siblings('label').toggleClass('hidden');
        });

        $('#docsarchive-billing_contract_id').click(function(){
            var contract_id = $(this).val();

            if (contract_id != '') {
                loader(true);
                $.get("/abonent/docs-archive/get-one-contract/",
                    {
                        contract_id : contract_id    
                    },
                    function(data){
                        $('#docsarchive-billing_contract_name').val(data.number);
                        $('#docsarchive-billing_contract_type').val(data.type);
                        $('#docsarchive-billing_contract_date').val(data.date);

                        $('#docsarchive-label').val(data.number);
                        $('#docsarchive-opened_at').val(data.opened_at);
                        loader(false);
                    },
                    'json'
                );
            } else {
                $('#docsarchive-billing_contract_name').val('');
                $('#docsarchive-billing_contract_type').val('');
                $('#docsarchive-billing_contract_date').val('');
            }
        });

        $('#docsarchive-service_types').on('change', function(){
            var service_types = $(this).val();
            if (service_types !== null ) {
                var conn_techs = $('#docsarchive-conn_techs').val();
                loader(true);
                $.get("/abonent/docs-archive/get-conn-techs/",
                    {
                        service_types : service_types,
                        conn_techs : conn_techs,
                    },
                    function(data){
                        $('#docsarchive-conn_techs').html(data).prop('disabled', false).trigger('chosen:updated');
                        loader(false);
                    },
                    'json'
                );
            } else {
                $('#docsarchive-conn_techs').html('<option value=""></option>').prop('disabled', true).trigger('chosen:updated');
            }
        });

        //confirm на удаление
        $('#view__remove-button').click(function(){
            var doc_id = customParseInt($(this).attr('data-doc-id'));
            var current_status = customParseInt($(this).attr('data-current-status'));
            $('#confirm').modal('show');
            if (current_status) {
                $("#confirm .confirm-content").html("Вы действительно хотите удалить данный документ?");
            } else {
                $("#confirm .confirm-content").html("Вы действительно хотите восстановить данный документ?");
            }
            
            $('#confirm .btn-success').one('click', function(e){
                loader(true);
                $.get("/abonent/docs-archive/remove/",
                    {
                        id : doc_id ,
                        current_status : current_status,   
                    },
                    function(data){
                        $('#confirm').modal('hide');
                        $('#view__remove-button').text((current_status) ? 'Восстановить' : 'Удалить').attr('data-current-status', (current_status) ? 0 : 1);
                        loader(false);
                    },
                    'json'
                );
            });
        });
    });
}(jQuery));