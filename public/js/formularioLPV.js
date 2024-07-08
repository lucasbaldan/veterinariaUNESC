$(document).ready(function () {
  
  $.fn.filepond.registerPlugin(FilePondPluginImagePreview, FilePondPluginFileValidateType);
  $('.my-pond').filepond({
    allowMultiple: true,
    labelIdle: 'Arraste e solte os arquivos ou <span class="filepond--label-action">clique aqui</span>', // Texto personalizado para o label
    maxFileSize: '2MB', // Tamanho máximo do arquivo
    imagePreviewHeight: 150,
    imagePreviewWidth: 200,
    acceptedFileTypes: ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'], // Tipos de arquivo aceitos
  
    instantUpload: true,
    server: {
      process: {
        url: '/veterinariaUNESC/server/atendimentos/uploadGaleria',
        method: 'POST',
        withCredentials: false,
        headers: {},
        onerror: function(response) {
          console.log('Erro no envio:', response);
        },
        ondata: function(formData) {
          formData.append('cdAtendimento', $('#cdFichaLPV').val());
          return formData;
        }
      }
    },
  });

  $('.my-pond').on('FilePond:processfile', function(e, file) {
    console.log('Arquivo enviado com sucesso:');
    atualizarGaleria();
  });

  Fancybox.bind('[data-fancybox]', {
    // Custom options for all galleries
  });  

  $("#ExpandFicha").click(function () {
    $(".accordion-collapse").each(function () {
      let collapseInstance = new bootstrap.Collapse(this, {
        toggle: false,
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

  if ($("#cdVeterinarioRemetente").val() === "") {
    $("#alterarVeterinario, #desvincularVeterinario").prop("disabled", true);
  } else {
    $("#buscaRapidaVeterinario").prop("disabled", true);
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

  $("#nrTelefoneProprietario").inputmask("(99) 99999-9999", {
    autoUnmask: true,
  });
  $("#nrTelVeterinarioRemetente").inputmask("(99) 99999-9999", {
    autoUnmask: true,
  });
});

$("#alterarAnimalFicha").on("click", function () {
  $(
    "#select2racaAnimal, #animal, #select2tipoAnimal, #select2especieAnimal, #select2racaAnimal, #dsSexo, #idade, #anoNascimento"
  ).prop("disabled", false);
  $("#alterouAnimal").val("S");
  if ($("#select2TipoAnimal").val() == "") {
    $("#select2especieAnimal").prop("disabled", true);
  }
  if ($("#select2especieAnimal").val() == "") {
    $("#select2racaAnimal").prop("disabled", true);
  }

  if (
    $("#select2tipoAnimal").val() !== "" &&
    $("#select2especieAnimal").val() === ""
  ) {
    var TipoAnimalSelecionado = $("#select2tipoAnimal").val();
    selectEspecieAnimal = new Select2("#select2especieAnimal", {
      url: "/veterinariaUNESC/server/especie/general",
      idTipoAnimal: TipoAnimalSelecionado,
    });
    $("#select2especieAnimal").prop("disabled", false);
  }

  if (
    $("#select2especieAnimal").val() !== "" &&
    $("#select2racaAnimal").val() === ""
  ) {
    var EspecieSelecionado = $("#select2especieAnimal").val();
    selectRacaAnimal = new Select2("#select2racaAnimal", {
      url: "/veterinariaUNESC/server/raca/general",
      idEspecie: EspecieSelecionado,
    });
    $("#select2racaAnimal").prop("disabled", false);
  }
});

$("#alterarDonoFicha").on("click", function () {
  $("#donoNaoDeclarado, #nmProprietario, #nrTelefoneProprietario").prop(
    "disabled",
    false
  );
  $("#alterouDono").val("S");
});

$("#donoNaoDeclarado").on("change", function () {
  if ($(this).is(":checked")) {
    $("#nmProprietario, #nrTelefoneProprietario").val("");
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
      "/veterinariaUNESC/public/js/buscaRapidaPessoaModal.js?v=" + window.scriptVersao
    );

    $.when(ajaxModal, script)
      .done(function (respostaAjaxModal) {
        bootbox.dialog({
          title: "Busca Rápida - Pessoa",
          size: "extra-large",
          message: respostaAjaxModal[0],
          className: "search-pessoa",
          onShown: function () {
            constructModalBuscaPessoa();
          },
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

function tableNaoEncontrado() {
  Notificacao.NotificacaoAviso(
    "Nenhum registro encontrado!<br> <b>Campos habilitados para inserir nova Pessoa</b>"
  );
  $(
    "#nmVeterinarioRemetente, #crmvVeterinarioRemetente, #nrTelVeterinarioRemetente, #dsEmailVeterinarioRemetente, #select2cdCidadeVeterinario"
  ).prop("disabled", false);
}

function selecionarPessoa(id) {
  $(
    "#nmVeterinarioRemetente, #crmvVeterinarioRemetente, #nrTelVeterinarioRemetente, #dsEmailVeterinarioRemetente, #select2cdCidadeVeterinario"
  ).prop("disabled", true);
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
  $(
    "#nmVeterinarioRemetente, #crmvVeterinarioRemetente, #nrTelVeterinarioRemetente, #dsEmailVeterinarioRemetente, #select2cdCidadeVeterinario"
  ).prop("disabled", false);
  $("#alterouVeterinario").val("S");
});

$("#desvincularVeterinario").on("click", function () {
  $(
    "#nmVeterinarioRemetente, #crmvVeterinarioRemetente, #nrTelVeterinarioRemetente, #dsEmailVeterinarioRemetente, #select2cdCidadeVeterinario"
  ).prop("disabled", true);
  $("#cdVeterinarioRemetente").val("").trigger("change");
  $(
    "#nmVeterinarioRemetente, #crmvVeterinarioRemetente, #nrTelVeterinarioRemetente, #dsEmailVeterinarioRemetente"
  ).val("");
  $("#select2cdCidadeVeterinario").val(null).trigger("change");
  $("#alterouVeterinario").val("N");
});

function salvarCadastroAtendimentos() {
  Loading.on();
  
  var form = $('#formFichaLPV')[0]; // You need to use standard javascript object here
  var formData = new FormData(form);

  $.ajax({
      url: "/veterinariaUNESC/server/atendimentos/controlar",
      method: "POST",
      data: formData,
      contentType: false,
      processData: false,
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
              window.location.href =
                "/veterinariaUNESC/paginas/listAtendimentos";
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

function atualizarGaleria() {
  var dados = {
      cdAtendimento: $('#cdFichaLPV').val()
  };

  $.post('/veterinariaUNESC/modais/recarregarGaleria', dados, function(response) {
      $('#galeriaDIV').html(response);

      // $('[data-fancybox="gallery-a"]').fancybox({
      //     // Configurações do Fancybox, se houver
      // });
  });
}

$('#galeriaDIV').on('click', '.btn-excluir-galeria', function(e) {
  e.preventDefault();
  
  // Obtém o nome ou identificador da imagem a ser excluída do atributo data-imagem
  var idImagem = $(this).attr('id');

  bootbox.confirm({
    className: "bootbox-delete",
    size: "extra-large",
    title: "Confirmar exclusão?",
    centerVertical: true,
    message:
      "Você tem <b>certeza</b> que deseja excluir esse registro? Esta ação não poderá ser desfeita.",
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
        $.ajax({
          url: "/veterinariaUNESC/server/atendimentos/excluirImagem",
          method: "POST",
          data: {idImagem : idImagem},
          success: function (response) {
            if (response.RESULT) {
              Notificacao.NotificacaoSucesso();
              atualizarGaleria();
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
});
