function salvarCadastroTipoAnimal() {
  botaoAnimado = new AnimarBotaoLoading("btnSalvar");
  botaoAnimado.animar();
  Loading.on();
  var formData = $("#formCadastroTipoAnimal").serialize();

  $.ajax({
    url: "/veterinariaUNESC/server/tipoAnimal/controlar",
    method: "POST",
    data: formData,
    success: function (response) {
      if (response.RESULT) {
        Notificacao.NotificacaoSucesso();
        bootbox.hideAll();
        if (typeof dataTableTipoAnimal !== "undefined") {
          dataTableTipoAnimal.ajax.reload();
        }
      }
    },
    error: function (xhr, status, error) {
      Notificacao.NotificacaoErro(xhr.responseJSON.MESSAGE);
    },
    complete: function () {
      Loading.off();
      botaoAnimado.restaurar();
    },
  });
}

function excluirCadastroTipoAnimal() {
  bootbox.confirm({
    className: "bootbox-delete",
    size: "extra-large",
    title: "Confirmar exclusão?",
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
        botaoAnimaSalvar = new AnimarBotaoLoading("btnSalvar");
        botaoAnimaExcluir = new AnimarBotaoLoading("btnExcluir");
        botaoAnimaSalvar.animar();
        botaoAnimaExcluir.animar();

        $("#bootbox-delete").modal("hide");
        Loading.on();
        var formData = $("#formCadastroTipoAnimal").serialize();
        $.ajax({
          url: "/veterinariaUNESC/server/tipoAnimal/excluir",
          method: "POST",
          data: formData,
          success: function (response) {
            if (response.RESULT) {
              Notificacao.NotificacaoSucesso();
              bootbox.hideAll();
              if (typeof dataTableTipoAnimal !== "undefined") {
                dataTableTipoAnimal.ajax.reload();
              }
            }
          },
          error: function (xhr, status, error) {
            Notificacao.NotificacaoErro(xhr.responseJSON.MESSAGE);
          },
          complete: function () {
            Loading.off();
            botaoAnimaSalvar.restaurar();
            botaoAnimaExcluir.restaurar();
          },
        });
      }
    },
  });
}
