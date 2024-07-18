    $(document).ready(function () {

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
            url: '/veterinariaUNESC/server/usuarios/efetuarLogin',
            type: 'POST',
            dataType: 'json',
            data: dadosForm,
            beforeSend: function(){
            Loading.on();
            },
            success: function(response) {
                window.location.href = '/veterinariaUNESC/paginas/inicial';
            },
            error: function(xhr, status, error) {
                Notificacao.NotificacaoErro(xhr.responseJSON.MESSAGE);
                Loading.off();
            },
        });
        });
    });