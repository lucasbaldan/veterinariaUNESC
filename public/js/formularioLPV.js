$(document).ready(function () {

    $('#ExpandFicha').click(function () {
        $('.accordion-collapse').each(function () {
            let collapseInstance = new bootstrap.Collapse(this, {
                toggle: false
            });
            collapseInstance.show();
        });
    });
    
      var selectTipoAnimal = new Select2("#select2tipoAnimal", {
        url: "/veterinariaUNESC/server/tipoAnimal/general",
      });
    
      var selectEspecieAnimal = new Select2("#select2especieAnimal", {
        url: "/veterinariaUNESC/server/especie/general",
      });
    
      var selectRacaAnimal = new Select2("#select2racaAnimal", {
        url: "/veterinariaUNESC/server/raca/general",
      });

      selectTipoAnimal.on("change", function (e) {
        var TipoAnimalSelecionado = $(this).val();
        $("#select2especieAnimal").val(null).trigger("change");
    
        if (TipoAnimalSelecionado) {
          selectEspecieAnimal = new Select2("#select2especieAnimal", {
            url: "/veterinariaUNESC/server/especie/general",
            idTipoAnimal: TipoAnimalSelecionado,
          });
          $("#select2especieAnimal").prop("disabled", false);
        } else {
          $("#select2racaAnimal").val(null).trigger("change");
          $("#select2especieAnimal, #select2racaAnimal").prop("disabled", true);
        }
      });
    
      selectEspecieAnimal.on("change", function (e) {
        var EspecieSelecionado = $(this).val();
        $("#select2racaAnimal").val(null).trigger("change");
    
        if (EspecieSelecionado) {
          selectRacaAnimal = new Select2("#select2racaAnimal", {
            url: "/veterinariaUNESC/server/raca/general",
            idEspecie: EspecieSelecionado,
          });
          $("#select2racaAnimal").prop("disabled", false);
        } else {
          $("#select2racaAnimal").prop("disabled", true);
        }
      });

      if($('#cdVeterinarioRemetente') === ''){
        $("#buscaRapidaVeterinario").prop("disabled", true);
      } else {
        $("#alterarVeterinario, #desvincularVeterinario").prop("disabled", true);
      }
      
      $("#cdVeterinarioRemetente").on("change", function () {
        if ($(this).val()) {
          $("#buscaRapidaVeterinario").prop("disabled", true);
          $("#alterarVeterinario, #desvincularVeterinario").prop("disabled", false);
        } else {
          $("#buscaRapidaVeterinario").prop("disabled", false);
          $("#alterarVeterinario, #desvincularVeterinario").prop("disabled", true);
        }
      });

      new Select2("#select2cdCidadeVeterinario", {
        url: "/veterinariaUNESC/server/municipio/general",
      });

      new Select2("#select2cidadePropriedade", {
        url: "/veterinariaUNESC/server/municipio/general",
      });
      
      $("#nrTelefoneProprietario").inputmask("(99) 99999-9999", { autoUnmask: true });
      $("#nrTelVeterinarioRemetente").inputmask("(99) 99999-9999", { autoUnmask: true });
      
});

$("#alterarAnimalFicha").on("click", function () {
    $("#select2racaAnimal, #animal, #select2tipoAnimal, #select2especieAnimal, #select2racaAnimal, #dsSexo, #idade, #anoNascimento").prop("disabled", false);
    $('#alterouAnimal').val('S');
    if($('#select2TipoAnimal').val() == '') {
        $("#select2especieAnimal").prop("disabled", true);
      }
      if($('#select2especieAnimal').val() == '') {
        $("#select2racaAnimal").prop("disabled", true);
      }

      if ($('#select2tipoAnimal').val() !== '' && $('#select2especieAnimal').val() === '') {
        var TipoAnimalSelecionado = $('#select2tipoAnimal').val();
        selectEspecieAnimal = new Select2("#select2especieAnimal", {
            url: "/veterinariaUNESC/server/especie/general",
            idTipoAnimal: TipoAnimalSelecionado,
          });
          $("#select2especieAnimal").prop("disabled", false);
    }

    if ($('#select2especieAnimal').val() !== '' && $('#select2racaAnimal').val() === '') {
        var EspecieSelecionado = $('#select2especieAnimal').val();
        selectRacaAnimal = new Select2("#select2racaAnimal", {
            url: "/veterinariaUNESC/server/raca/general",
            idEspecie: EspecieSelecionado,
          });
          $("#select2racaAnimal").prop("disabled", false);
    }
});

$("#alterarDonoFicha").on("click", function () {
    $("#donoNaoDeclarado, #nmProprietario, #nrTelefoneProprietario").prop("disabled", false);
    $('#alterouDono').val('S');
});

$("#donoNaoDeclarado").on("change", function () {
    if ($(this).is(":checked")) {
        $("#nmProprietario, #nrTelefoneProprietario").val('');
        $("#nmProprietario, #nrTelefoneProprietario").prop("disabled", true);
    }
  });




  $("#buscaRapidaVeterinario").on("click", function () {
    try {
      Loading.on();

      var ajaxModal = $.ajax({
        url: "/veterinariaUNESC/modais/buscaRapidaPessoa",
        method: "POST",
      });

      var script = $.getScript(
        "/veterinariaUNESC/public/js/buscaRapidaPessoaModal.js"
      );

      $.when(ajaxModal, script)
        .done(function (respostaAjaxModal) {
          bootbox.dialog({
            title: "Busca Rápida - Pessoa",
            size: "extra-large",
            message: respostaAjaxModal[0],
            className: "search-pessoa",
            onShown: function() {constructModalBuscaPessoa();}
          });
        })
        .fail(function (xhr, status, error) {})
        .always(function () {
          Loading.off();
        });
    } catch (e) {
      Loading.off();
    }
  });

  function tableNaoEncontrado(){
    Notificacao.NotificacaoAviso('Nenhum registro encontrado!<br> <b>Campos habilitados para inserir nova Pessoa</b>');
    $("#nmVeterinarioRemetente, #crmvVeterinarioRemetente, #nrTelVeterinarioRemetente, #dsEmailVeterinarioRemetente, #select2cdCidadeVeterinario").prop("disabled", false);
  }

  function selecionarPessoa(id) {
    $("#nmVeterinarioRemetente, #crmvVeterinarioRemetente, #nrTelVeterinarioRemetente, #dsEmailVeterinarioRemetente, #select2cdCidadeVeterinario").prop("disabled", true);
    Loading.on();
    $.ajax({
      url: "/veterinariaUNESC/server/pessoas/selecionarPessoa",
      method: "POST",
      data: {
        cdPessoa: id,
      },
      success: function (response) {
        if (response.RESULT) {
          var pessoa = response.RETURN;
  
          $("#cdVeterinarioRemetente").val(pessoa.cd_pessoa).trigger("change");
          $("#nmVeterinarioRemetente").val(pessoa.nm_pessoa);
          $("#nrTelVeterinarioRemetente").val(pessoa.nr_telefone);
          $("#dsEmailVeterinarioRemetente").val(pessoa.ds_email);
          $("#crmvVeterinarioRemetente").val(pessoa.nr_crmv);
  
          var optionCidade = new Option(
            pessoa.nm_cidade,
            pessoa.cd_cidade,
            true,
            true
          );
          $("#select2cdCidadeVeterinario").append(optionCidade).trigger("change");
          bootbox.hideAll();
        }
      },
      error: function (xhr, status, error) {
        Notificacao.NotificacaoErro(xhr.responseJSON.MESSAGE);
      },
      complete: function () {
        Loading.off();
      },
    });
  }

  $("#alterarVeterinario").on("click", function () {
    $("#nmVeterinarioRemetente, #crmvVeterinarioRemetente, #nrTelVeterinarioRemetente, #dsEmailVeterinarioRemetente, #select2cdCidadeVeterinario").prop("disabled", false);
    $('#alterouVeterinario').val('S');
});

$("#desvincularVeterinario").on("click", function () {
  $("#nmVeterinarioRemetente, #crmvVeterinarioRemetente, #nrTelVeterinarioRemetente, #dsEmailVeterinarioRemetente, #select2cdCidadeVeterinario").prop("disabled", true);
  $("#cdVeterinarioRemetente").val('').trigger("change");
  $("#nmVeterinarioRemetente, #crmvVeterinarioRemetente, #nrTelVeterinarioRemetente, #dsEmailVeterinarioRemetente").val('');
  $("#select2cdCidadeVeterinario").val(null).trigger("change");
  $('#alterouVeterinario').val('N');
});







function salvarCadastroAtendimento() {
  Loading.on();
  var formData = $("#formFichaLPV").serialize();

  $.ajax({
    url: "/veterinariaUNESC/server/atendimentos/controlar",
    method: "POST",
    data: formData,
    success: function (response) {
      if (response.RESULT) {
        sessionStorage.setItem("notificarSucesso", "true");
        window.location.href = "/veterinariaUNESC/paginas/listAtendimentos";
      }
    },
    error: function (xhr, status, error) {
      Notificacao.NotificacaoErro(xhr.responseJSON.MESSAGE);
    },
    complete: function () {
      Loading.off();
    },
  });
}

function excluirCadastroAtendimentos() {
  bootbox.confirm({
    className: "bootbox-delete",
    size: "extra-large",
    title: "Confirmar exclusão?",
    centerVertical: true,
    message:
      "Você tem <b>certeza</b> que deseja excluir esse registro? Esta ação poderá ser desfeita.",
    buttons: {
      cancel: {
        label: '<i class="bi bi-arrow-left"></i> Cancelar',
      },
      confirm: {
        label: '<i class="bi bi-check-lg"></i> Confirmar',
      },
    },
    callback: function (result) {
      if (result) {
        $("#bootbox-delete").modal("hide");
        Loading.on();
        var formData = $("#formFichaLPV").serialize();
        $.ajax({
          url: "/veterinariaUNESC/server/atendimentos/excluir",
          method: "POST",
          data: formData,
          success: function (response) {
            if (response.RESULT) {
              sessionStorage.setItem("notificarSucesso", "true");
              window.location.href = "/veterinariaUNESC/paginas/listAtendimentos";
            }
          },
          error: function (xhr, status, error) {
            Notificacao.NotificacaoErro(xhr.responseJSON.MESSAGE);
          },
          complete: function () {
            Loading.off();
          },
        });
      }
    },
  });
}
  