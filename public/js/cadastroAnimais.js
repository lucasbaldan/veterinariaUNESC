$(document).ready(function () {

  if ($("#tutorNaoDeclarado").is(":checked")) {
    $("#buscaRapidaPessoa, #desvincularPessoa, #alterarPessoaAtual").prop(
      "disabled",
      true
    );
  }

  if ($('#select2especieAnimal').val() == '') {
    $("#select2racaAnimal").prop("disabled", true);
  }

  if ($('#donoNaoDeclarado').is(':checked')) {
    $("#buscaRapidaPessoa").prop("disabled", true);
  }

  if ($('#cdPessoa').val() == '') {
    $("#desvincularPessoa, #alterarPessoaAtual").prop("disabled", true);
  } else {
    $("#buscaRapidaPessoa").prop("disabled", true);
  }

  bloquearCamposPessoa();

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

  $("#tutorNaoDeclarado").on("change", function () {
    if ($(this).is(":checked")) {
      desvincularPessoa();
      $("#buscaRapidaPessoa, #desvincularPessoa, #alterarPessoaAtual").prop(
        "disabled",
        true
      );
    } else {
      $("#buscaRapidaPessoa").prop("disabled", false);
    }
  });

  $("#cdPessoa").on("change", function () {
    if ($(this).val()) {
      $("#buscaRapidaPessoa").prop("disabled", true);
      $("#desvincularPessoa, #alterarPessoaAtual").prop("disabled", false);
    } else {
      $("#buscaRapidaPessoa").prop("disabled", false);
      $("#desvincularPessoa, #alterarPessoaAtual").prop("disabled", true);
    }
  });

  new Select2("#select2cdCidade", {
    url: "/veterinaria/server/municipio/general",
  });

  new Select2("#select2cdBairro", {
    url: "/veterinaria/server/bairro/general",
  });

  new Select2("#select2cdLogradouro", {
    url: "/veterinaria/server/logradouro/general",
  });

  $("#nrTelefone").inputmask("(99) 99999-9999", { autoUnmask: true });
  $("#cpfPessoa").inputmask("999.999.999-99", { autoUnmask: true });

  $("#buscaRapidaPessoa").on("click", function () {
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
            onShown: function () { constructModalBuscaPessoa(); }
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

  $("#desvincularPessoa").on("click", function () {
    desvincularPessoa();
    $('#alterouPessoa').val('N');
  });

  $("#alterarPessoaAtual").on("click", function () {
    $('#alterouPessoa').val('S');
    desbloquearCamposPessoa();
  });
});

function salvarCadastroAnimais() {
  Loading.on();
  var formData = $("#formCadastroAnimais").serialize();

  $.ajax({
    url: "/veterinaria/server/animais/controlar",
    method: "POST",
    data: formData,
    complete: function(xhr, textStatus) {
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
        window.location.href = "/veterinaria/paginas/listAnimais";
      }
    },
    error: function (xhr, status, error) {
      Notificacao.NotificacaoErro(xhr.responseJSON.MESSAGE);
      Loading.off();
    },
  });
}

function excluirCadastroAnimais() {
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
        var formData = $("#formCadastroAnimais").serialize();
        $.ajax({
          url: "/veterinaria/server/animais/excluir",
          method: "POST",
          data: formData,
          complete: function(xhr, textStatus) {
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
              window.location.href = "/veterinaria/paginas/listAnimais";
            }
          },
          error: function (xhr, status, error) {
            Notificacao.NotificacaoErro(xhr.responseJSON.MESSAGE);
            Loading.off();
          },
        });
      }
    },
  });
}

function selecionarPessoa(id) {
  Loading.on();
  bloquearCamposPessoa();
  $.ajax({
    url: "/veterinaria/server/pessoas/selecionarPessoa",
    method: "POST",
    data: {
      cdPessoa: id,
    },
    complete: function(xhr, textStatus) {
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

        $("#cdPessoa").val(pessoa.cd_pessoa).trigger("change");
        $("#nmPessoa").val(pessoa.nm_pessoa);
        $("#cpfPessoa").val(pessoa.cpf);
        $("#dataNascimento").val(pessoa.data_nascimento);
        $("#nrTelefone").val(pessoa.nr_telefone);
        $("#dsEmail").val(pessoa.ds_email);
        $("#nrCRMV").val(pessoa.nr_crmv);

        var optionCidade = new Option(
          pessoa.nm_cidade,
          pessoa.cd_cidade,
          true,
          true
        );
        $("#select2cdCidade").append(optionCidade).trigger("change");

        var optionBairro = new Option(
          pessoa.nm_bairro,
          pessoa.cd_bairro,
          true,
          true
        );
        $("#select2cdBairro").append(optionBairro).trigger("change");

        var optionLogradouro = new Option(
          pessoa.nm_logradouro,
          pessoa.cd_logradouro,
          true,
          true
        );
        $("#select2cdLogradouro").append(optionLogradouro).trigger("change");

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

function desvincularPessoa() {
  $("#cdPessoa").val("").trigger("change");
  $("#nmPessoa").val("");
  $("#cpfPessoa").val("");
  $("#dataNascimento").val("");
  $("#nrTelefone").val("");
  $("#dsEmail").val("");
  $("#nrCRMV").val("");
  $("#select2cdCidade").val(null).trigger("change");
  $("#select2cdBairro").val(null).trigger("change");
  $("#select2cdLogradouro").val(null).trigger("change");

  bloquearCamposPessoa();
}

function tableNaoEncontrado() {
  Notificacao.NotificacaoAviso('Nenhum registro encontrado!<br> <b>Campos habilitados para inserir nova Pessoa</b>');
  desvincularPessoa();
  desbloquearCamposPessoa();
  $("#buscaRapidaPessoa, #alterarPessoaAtual").prop("disabled", true);
  $("#desvincularPessoa").prop("disabled", false);
}
