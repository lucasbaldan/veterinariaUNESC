$(document).ready(function() {
            $('#ExpandFicha').click(function() {
                $('.accordion-collapse').each(function() {
                    let collapseInstance = new bootstrap.Collapse(this, {
                        toggle: false
                    });
                    collapseInstance.show(); 
                });
            });


    $('#btnSalvarFormularioLPV').on('click', function (event) {

        event.preventDefault();
        // var formData = $(this).serialize();

        // console.log('FORMULARIO: ', formData);
        // return;

        $.ajax({
            type: "POST",
            data: {
                cdFichaLPV: $('#cdFichaLPV').val(),
                dtFicha: $('#dtFicha').val(),
                animal: $('#animal').val(),
                nmVeterinarioRemetente: $('#nmVeterinarioRemetente').val(),
                crmvVeterinarioRemetente: $('#crmvVeterinarioRemetente').val(),
                nrTelVeterinarioRemetente: $('#nrTelVeterinarioRemetente').val(),
                dsEmailVeterinarioRemetente: $('#dsEmailVeterinarioRemetente').val(),
                nmCidadeVeterinarioRemetente: $('#nmCidadeVeterinarioRemetente').val(),
                // cdUsuarioPlantonista : $('#cdFichaLPV'),
                nmProprietario: $('#nmProprietario').val(),
                nrTelefoneProprietario: $('#nrTelefoneProprietario').val(),
                cidadePropriedade: $('#cidadePropriedade').val(),
                dsEspecie: $('#dsEspecie').val(),
                dsRaca: $('#dsRaca').val(),
                dsSexo: $('#dsSexo').val(),
                idade: $('#idade').val(),
                totalAnimais: $('#totalAnimais').val(),
                qtdAnimaisDoentes: $('#qtdAnimaisDoentes').val(),
                qtdAnimaisMortos: $('#qtdAnimaisMortos').val(),
                dsMaterialRecebido: $('#dsMaterialRecebido').val(),
                dsDiagnosticoPresuntivo: $('#dsDiagnosticoPresuntivo'),
                flAvaliacaoTumoralComMargem: $('#flAvaliacaoTumoralComMargem').val(),
                dsNomeAnimal: $('#dsNomeAnimal').val(),
                dsEpidemiologiaHistoriaClinica: $('#dsEpidemiologiaHistoriaClinica').val(),
                dsLesoesMacroscopicas: $('#dsLesoesMacroscopicas').val(),
                dsLesoesHistologicas: $('#dsLesoesHistologicas').val(),
                dsDiagnostico: $('#dsDiagnostico').val(),
                dsRelatorio: $('#dsRelatorio').val(),
            },
            url: window.HOME + 'Controllers/formulario-lpv/salvar',
            beforeSend: function () { },
            complete: function () { },
            success: function (data) {
                $("#ResultDados").html(data);
            }
        });

        event.preventDefault();
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
        //this.submit();
        notificacao.push("<b>Usuário ou senha inválidos</b><br><br> Por favor verifique os dados de acesso e tente novamente.", 'warning');
    });
});