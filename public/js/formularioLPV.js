$(document).ready(function () {

  if (sessionStorage.getItem("notificarSucesso") === "true") {
    Notificacao.NotificacaoSucesso();
    sessionStorage.removeItem("notificarSucesso");
  }

  if ($("#donoNaoDeclarado").is(":checked")) {
    $("#alterarDonoFicha").prop(
      "disabled",
      true
    );
  }

  $.fn.filepond.registerPlugin(FilePondPluginImagePreview, FilePondPluginFileValidateType);
  $('.my-pond').filepond({
    allowMultiple: true,
    labelIdle: 'Arraste e solte os arquivos ou <span class="filepond--label-action">clique aqui</span>', // Texto personalizado para o label
    //maxFileSize: '2MB', // Tamanho máximo do arquivo
    imagePreviewHeight: 150,
    imagePreviewWidth: 200,
    acceptedFileTypes: ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'], // Tipos de arquivo aceitos

    instantUpload: true,
    server: {
      process: {
        url: '/veterinaria/server/atendimentos/uploadGaleria',
        method: 'POST',
        withCredentials: false,
        headers: {},
        onerror: function (response) {
          console.log('Erro no envio:', response);
        },
        ondata: function (formData) {
          formData.append('cdAtendimento', $('#cdFichaLPV').val());
          return formData;
        }
      }
    },
  });

  $('.my-pond').on('FilePond:processfile', function (e, file) {
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

  var selectEspecieAnimal = new Select2("#select2especieAnimal", {
    url: "/veterinaria/server/especie/general",
  });

  var selectRacaAnimal = new Select2("#select2racaAnimal", {
    url: "/veterinaria/server/raca/general",
  });

  selectEspecieAnimal.on("change", function (e) {
    var EspecieSelecionado = $(this).val();
    $("#select2racaAnimal").val(null).trigger("change");

    if (EspecieSelecionado) {
      selectRacaAnimal = new Select2("#select2racaAnimal", {
        url: "/veterinaria/server/raca/general",
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
    url: "/veterinaria/server/municipio/general",
  });

  new Select2("#select2cidadePropriedade", {
    url: "/veterinaria/server/municipio/general",
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
    "#select2racaAnimal, #animal, #select2especieAnimal, #select2racaAnimal, #dsSexo, #flCastrado, #idade, #anoNascimento"
  ).prop("disabled", false);
  $("#alterouAnimal").val("S");

  if ($("#select2especieAnimal").val() == "") {
    $("#select2racaAnimal").prop("disabled", true);
  }

  if (
    $("#select2especieAnimal").val() !== "" &&
    $("#select2racaAnimal").val() === ""
  ) {
    var EspecieSelecionado = $("#select2especieAnimal").val();
    selectRacaAnimal = new Select2("#select2racaAnimal", {
      url: "/veterinaria/server/raca/general",
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
      url: "/veterinaria/modais/buscaRapidaPessoa",
      method: "POST",
    });

    var script = $.getScript(
      "/veterinaria/public/js/buscaRapidaPessoaModal.js?v=" + window.scriptVersao
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
      .fail(function (xhr, status, error) { })
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
    url: "/veterinaria/server/pessoas/selecionarPessoa",
    method: "POST",
    data: {
      cdPessoa: id,
    },
    complete: function (xhr, textStatus) {
      if (xhr.status === 302) {
        var redirectUrl = xhr.getResponseHeader('Location');
        if (redirectUrl) {
          window.location.href = redirectUrl;
        }
      }
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

function salvarCadastroAtendimentos(atualizarPage = false) {
  Loading.on();

  var formData = $('#formFichaLPV').serialize();

  $.ajax({
    url: "/veterinaria/server/atendimentos/controlar",
    method: "POST",
    data: formData,
    complete: function (xhr, textStatus) {
      if (xhr.status === 302) {
        var redirectUrl = xhr.getResponseHeader('Location');
        if (redirectUrl) {
          window.location.href = redirectUrl;
        }
      }
    },
    success: function (response) {
      if (response.RESULT) {
        if (atualizarPage) {
          var idFicha = response.RETURN;
          var form = $('<form>', {
            action: '/veterinaria/paginas/fichaLPV',
            method: 'POST'
          });
          $('<input>').attr({
            type: 'hidden',
            name: 'idFicha',
            value: idFicha
          }).appendTo(form);
          form.appendTo('body').submit();
          sessionStorage.setItem("notificarSucesso", "true");
        } else {
          sessionStorage.setItem("notificarSucesso", "true");
          window.location.href = "/veterinaria/paginas/listAtendimentos";
        }
      }
    },
    error: function (xhr, status, error) {
      Notificacao.NotificacaoErro(xhr.responseJSON.MESSAGE);
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
      "Você tem <b>certeza</b> que deseja excluir esse registro? Esta acarretará também na exclusão das <b>imagens anexadas</b>.",
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
          url: "/veterinaria/server/atendimentos/excluir",
          method: "POST",
          data: formData,
          complete: function (xhr, textStatus) {
            if (xhr.status === 302) {
              var redirectUrl = xhr.getResponseHeader('Location');
              if (redirectUrl) {
                window.location.href = redirectUrl;
              }
            }
          },
          success: function (response) {
            if (response.RESULT) {
              sessionStorage.setItem("notificarSucesso", "true");
              window.location.href =
                "/veterinaria/paginas/listAtendimentos";
            }
          },
          error: function (xhr, status, error) {
            Notificacao.NotificacaoErro(xhr.responseJSON.MESSAGE);
            Loading.off();
          },
          complete: function () {
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

  $.post('/veterinaria/modais/recarregarGaleria', dados, function (response) {
    $('#galeriaDIV').html(response);
  });
}

$('#galeriaDIV').on('click', '.btn-excluir-galeria', function (e) {
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
          url: "/veterinaria/server/atendimentos/excluirImagem",
          method: "POST",
          data: { idImagem: idImagem },
          complete: function (xhr, textStatus) {
            if (xhr.status === 302) {
              var redirectUrl = xhr.getResponseHeader('Location');
              if (redirectUrl) {
                window.location.href = redirectUrl;
              }
            }
          },
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


$("#printFichaLPV").on("click", function () {
  // Mostra o Bootbox para o usuário escolher o formato
  bootbox.dialog({
    title: "Emitir Relatório LPV",
    message: '<button type="button" class="btn btn-primary" onclick="gerarRelPDF()">PDF</button> <button type="button" class="btn btn-primary" onclick="gerarRelWord()">Word</button>',
  });
});

function gerarRelWord() {
  Loading.on();
  
  $.ajax({
    url: "/veterinaria/server/relatorios/fichaLPV", // URL correta para gerar o DOCX
    method: "POST",
    data: {
      cdFichaLPV: $('#cdFichaLPV').val(),
    },
    xhrFields: {
      responseType: 'blob' // Define o tipo de resposta como blob (binário)
    },
    success: function (blob) {
      Loading.off();

      // Cria um URL para o blob
      const url = window.URL.createObjectURL(blob);

      // Cria um link temporário
      const a = document.createElement('a');
      a.href = url;
      a.download = "relatorio.docx"; // Nome do arquivo

      // Adiciona o link ao documento e clica nele
      document.body.appendChild(a);
      a.click();

      // Remove o link temporário
      document.body.removeChild(a);
      window.URL.revokeObjectURL(url); // Libera a memória associada ao blob
    },
    error: function (xhr, status, error) {
      Notificacao.NotificacaoErro(xhr.responseJSON.MESSAGE);
      Loading.off();
    }
  });
}

function gerarRelPDF() {
  Loading.on();
  
  $.ajax({
    url: "/veterinaria/server/relatorios/fichaLPV", // URL correta para gerar o DOCX
    method: "POST",
    data: {
      cdFichaLPV: $('#cdFichaLPV').val(),
    },
    xhrFields: {
      responseType: 'blob' // Define o tipo de resposta como blob (binário)
    },
    success: function (blob) {
      Loading.off();

      // Cria um URL para o blob
      const url = window.URL.createObjectURL(blob);

      // Cria um link temporário
      const a = document.createElement('a');
      a.href = url;
      a.download = "relatorio.pdf"; // Nome do arquivo

      // Adiciona o link ao documento e clica nele
      document.body.appendChild(a);
      a.click();

      // Remove o link temporário
      document.body.removeChild(a);
      window.URL.revokeObjectURL(url); // Libera a memória associada ao blob
    },
    error: function (xhr, status, error) {
      Notificacao.NotificacaoErro(xhr.responseJSON.MESSAGE);
      Loading.off();
    }
  });
}
