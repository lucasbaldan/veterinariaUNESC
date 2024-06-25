$(document).ready(function () {
  $("#select2especieAnimal, #select2racaAnimal").prop("disabled", true);

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
    var anoCalculado = calcularAnoNascimento($("#idade").val());
    $("#anoNascimento").val(anoCalculado);
  });

  $("#anoNascimento").on("blur", function (e) {
    var idadeCalculada = calcularIdade($("#anoNascimento").val());
    $("#idade").val(idadeCalculada);
  });

  $("#donoNaoDeclarado").on("change", function () {
    if ($(this).is(":checked")) {
      $(
        "#cdPessoa, #nmPessoa, #nrTelefone, #dsEmail, #nrCRMV, #select2cdCidade, #select2cdBairro, #select2cdLogradouro, #cpfPessoa, #dataNascimento, #buscaRapidaPessoa"
      ).prop("disabled", true);
    } else {
      $(
        "#cdPessoa, #nmPessoa, #nrTelefone, #dsEmail, #nrCRMV, #select2cdCidade, #select2cdBairro, #select2cdLogradouro, #cpfPessoa, #dataNascimento, #buscaRapidaPessoa"
      ).prop("disabled", false);
    }
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
  
      var script = $.getScript("/veterinariaUNESC/public/js/buscaRapidaPessoaModal.js");
  
      $.when(ajaxModal, script).done(function (respostaAjaxModal) {
          bootbox.dialog({
            title: "Busca Rápida - Pessoa",
            size: 'extra-large',
            message: respostaAjaxModal[0],
            className: 'search-pessoa',
          });
          constructModalBuscaPessoa();
        })
        .fail(function (xhr, status, error) {
        })
        .always(function () {
          Loading.off();
        });
    } catch (e) {
      Loading.off();
    }
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
