    $(document).ready(function () {

        const notificacao = new Notificacao({
            duration: 10000,
            position: {
                x: 'center',
                y: 'bottom',
            },
        });

        $('#formLogin').submit(function (event) {
            event.preventDefault();

            var dadosForm = $(this).serialize();

            var validarTela = false;
            if ($('#usuario').val() === '') {
                $('#usuario').addClass('is-invalid');
                validarTela = true;
            }
            else {
                $('#usuario').removeClass('is-invalid');
            }
            if ($('#senha').val() === '') {
                $('#senha').addClass('is-invalid');
                validarTela = true;
            } else {
                $('#senha').removeClass('is-invalid');
            }

            if (validarTela) {
                return;
            }

            $.ajax({
            url: '/veterinariaUNESC/server/login',
            type: 'POST',
            dataType: 'json',
            data: dadosForm,
            beforeSend: function(){
            Loading.on();
            },
            success: function(response) {
                alert(response);
            },
            error: function(xhr, status, error) {
                notificacao.push(xhr.responseJSON.MESSAGE, 'warning');
            },
            complete: function(){
                Loading.off();
            }
        });
        });
    });