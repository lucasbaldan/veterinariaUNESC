$(document).ready(function () {

  if($('#select2TipoAnimal').val() == '') {
    $("#select2especieAnimal").prop("disabled", true);
  }

  if($('#select2especieAnimal').val() == '') {
    $("#select2racaAnimal").prop("disabled", true);
  }

  if ($('#donoNaoDeclarado').is(':checked')) {
    $("#buscaRapidaPessoa").prop("disabled", true);
}

  if($('#cdPessoa').val() == '') {
    $("#desvincularPessoa, #alterarPessoaAtual").prop("disabled", true);
  } else {
    $("#buscaRapidaPessoa").prop("disabled", true);
  }

  bloquearCamposPessoa();

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

  $("#idade").on("blur", function (e) {
    if($("#idade").val() !== ""){
    var anoCalculado = calcularAnoNascimento($("#idade").val());
    $("#anoNascimento").val(anoCalculado);
    }
  });

  $("#anoNascimento").on("blur", function (e) {
    if($("#anoNascimento").val() !== ""){
    var idadeCalculada = calcularIdade($("#anoNascimento").val());
    $("#idade").val(idadeCalculada);
    }
  });

  $("#donoNaoDeclarado").on("change", function () {
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
    url: "/veterinariaUNESC/server/municipio/general",
  });

  new Select2("#select2cdBairro", {
    url: "/veterinariaUNESC/server/bairro/general",
  });

  new Select2("#select2cdLogradouro", {
    url: "/veterinariaUNESC/server/logradouro/general",
  });

  $("#nrTelefone").inputmask("(99) 99999-9999", { autoUnmask: true });
  $("#cpfPessoa").inputmask("999.999.999-99", { autoUnmask: true });

  $("#buscaRapidaPessoa").on("click", function () {
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
    url: "/veterinariaUNESC/server/animais/controlar",
    method: "POST",
    data: formData,
    success: function (response) {
      if (response.RESULT) {
        sessionStorage.setItem("notificarSucesso", "true");
        window.location.href = "/veterinariaUNESC/paginas/listAnimais";
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
          url: "/veterinariaUNESC/server/animais/excluir",
          method: "POST",
          data: formData,
          success: function (response) {
            if (response.RESULT) {
              sessionStorage.setItem("notificarSucesso", "true");
              window.location.href = "/veterinariaUNESC/paginas/listAnimais";
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

function selecionarPessoa(id) {
  Loading.on();
  bloquearCamposPessoa();
  $.ajax({
    url: "/veterinariaUNESC/server/pessoas/selecionarPessoa",
    method: "POST",
    data: {
      cdPessoa: id,
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

function calcularAnoNascimento(idade) {
  var dataAtual = new Date().getFullYear();
  var anoNascimento = dataAtual - idade;
  return anoNascimento;
}

function calcularIdade(anoNascimento) {
  var anoAtual = new Date().getFullYear();
  var idade = anoAtual - anoNascimento;
  return idade;
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

function tableNaoEncontrado(){
  Notificacao.NotificacaoAviso('Nenhum registro encontrado!<br> <b>Campos habilitados para inserir nova Pessoa</b>');
  desvincularPessoa();
  desbloquearCamposPessoa();
  $("#buscaRapidaPessoa, #alterarPessoaAtual").prop("disabled", true);
  $("#desvincularPessoa").prop("disabled", false);
}
