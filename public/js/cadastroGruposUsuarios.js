$(document).ready(function () {
    const notificacao = new Notificacao({
        duration: 10000,
        position: {
            x: 'center',
            y: 'bottom',
        },
    });

    $('#formCadastroPessoas').submit(function (event) {
        event.preventDefault();

        var dadosForm = $(this).serialize();
        var codigo = $('#cdPessoa').val();

        // console.log('Formulário:: ', dadosForm);

        $.ajax({
            url: '/veterinariaUNESC/server/pessoas/salvaPessoa',
            type: 'POST',
            dataType: 'json',
            data: dadosForm,
            beforeSend: function () {
                Loading.on();
            },
            success: function (response) {
                // alert(response);
                console.log('Retorno: ', response.RETURN);

                if (codigo == '') {
                    $('#cdPessoa').val(response.RETURN);
                }
            },
            error: function (xhr, status, error) {
                // console.log('DADOS::: ', xhr.responseJSON);
                notificacao.push(xhr.responseJSON.MESSAGE, 'warning');
            },
            complete: function () {
                Loading.off();
                if (codigo == '') {
                    notificacao.push('Pessoa salva com Sucesso!', 'success');
                } else {
                    notificacao.push('Pessoa atualizada com Sucesso!', 'success');
                }
            }
        });
    });

    $('#bntExcluirPessoa').on('click', function () {
        var cdPessoa = $('#cdPessoa').val();
        var acao = 'excluir';

        $.ajax({
            url: '/veterinariaUNESC/server/pessoas/atualizaExclusaoPessoa',
            type: 'POST',
            dataType: 'json',
            data: {
                cdPessoa,
                acao
            },
            beforeSend: function () {
                Loading.on();
            },
            success: function (response) {
            },
            error: function (xhr, status, error) {
                // console.log('DADOS::: ', xhr.responseJSON);
                notificacao.push(xhr.responseJSON.MESSAGE, 'warning');
            },
            complete: function () {
                Loading.off();
                notificacao.push('Pessoa excluida com Sucesso!', 'success');
            }
        });

    })


    // $('#btnSalvarFormularioLPV').on('click', function (event) {

    //     event.preventDefault();
    //     // var formData = $(this).serialize();

    //     // console.log('FORMULARIO: ', formData);
    //     // return;

    //     $.ajax({
    //         type: "POST",
    //         data: {
    //             cdFichaLPV: $('#cdFichaLPV').val(),
    //             dtFicha: $('#dtFicha').val(),
    //             animal: $('#animal').val(),
    //             nmVeterinarioRemetente: $('#nmVeterinarioRemetente').val(),
    //             crmvVeterinarioRemetente: $('#crmvVeterinarioRemetente').val(),
    //             nrTelVeterinarioRemetente: $('#nrTelVeterinarioRemetente').val(),
    //             dsEmailVeterinarioRemetente: $('#dsEmailVeterinarioRemetente').val(),
    //             nmCidadeVeterinarioRemetente: $('#nmCidadeVeterinarioRemetente').val(),
    //             // cdUsuarioPlantonista : $('#cdFichaLPV'),
    //             nmProprietario: $('#nmProprietario').val(),
    //             nrTelefoneProprietario: $('#nrTelefoneProprietario').val(),
    //             cidadePropriedade: $('#cidadePropriedade').val(),
    //             dsEspecie: $('#dsEspecie').val(),
    //             dsRaca: $('#dsRaca').val(),
    //             dsSexo: $('#dsSexo').val(),
    //             idade: $('#idade').val(),
    //             totalAnimais: $('#totalAnimais').val(),
    //             qtdAnimaisDoentes: $('#qtdAnimaisDoentes').val(),
    //             qtdAnimaisMortos: $('#qtdAnimaisMortos').val(),
    //             dsMaterialRecebido: $('#dsMaterialRecebido').val(),
    //             dsDiagnosticoPresuntivo: $('#dsDiagnosticoPresuntivo'),
    //             flAvaliacaoTumoralComMargem: $('#flAvaliacaoTumoralComMargem').val(),
    //             dsNomeAnimal: $('#dsNomeAnimal').val(),
    //             dsEpidemiologiaHistoriaClinica: $('#dsEpidemiologiaHistoriaClinica').val(),
    //             dsLesoesMacroscopicas: $('#dsLesoesMacroscopicas').val(),
    //             dsLesoesHistologicas: $('#dsLesoesHistologicas').val(),
    //             dsDiagnostico: $('#dsDiagnostico').val(),
    //             dsRelatorio: $('#dsRelatorio').val(),
    //         },
    //         url: 'verinariaUNESC/fichaLPV/salvar',
    //         beforeSend: function () { },
    //         complete: function () { },
    //         success: function (data) {
    //             $("#ResultDados").html(data);
    //         }
    //     });

    //     event.preventDefault();
    //     var validarTela = false;
    //     if ($('#usuario').val() === '') {
    //         $('#usuario').addClass('is-invalid');
    //         validarTela = true;
    //     }
    //     else {
    //         $('#usuario').removeClass('is-invalid');
    //     }
    //     if ($('#senha').val() === '') {
    //         $('#senha').addClass('is-invalid');
    //         validarTela = true;
    //     } else {
    //         $('#senha').removeClass('is-invalid');
    //     }

    //     if (validarTela) {
    //         return;
    //     }
    //     //this.submit();
    //     notificacao.push("<b>Usuário ou senha inválidos</b><br><br> Por favor verifique os dados de acesso e tente novamente.", 'warning');
    // });
});