function salvarCadastroGrupoUsuarios() {
  botaoAnimado = new AnimarBotaoLoading("btnSalvar");
  botaoAnimadoExcluir = new AnimarBotaoLoading("btnExcluir");
  botaoAnimado.animar();
  botaoAnimadoExcluir.animar();
  Loading.on();
  var formData = $("#formCadastroGruposUsuarios").serialize();

  // console.log("FORM: ", formData);

  $.ajax({
    url: "/veterinariaUNESC/server/gruposUsuarios/salvaGrupoUsuarios",
    method: "POST",
    data: formData,
    success: function (response) {
      if (response.RESULT) {
        Notificacao.NotificacaoSucesso();
        bootbox.hideAll();
        if (typeof dataTableGruposUsuarios !== "undefined") {
          dataTableGruposUsuarios.ajax.reload();
        }
      }
    },
    error: function (xhr, status, error) {
      Notificacao.NotificacaoErro(xhr.responseJSON.MESSAGE);
    },
    complete: function () {
      Loading.off();
      botaoAnimado.restaurar();
      botaoAnimadoExcluir.restaurar();
    },
  });
}

function excluirCadastroGrupoUsuarios() {
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
        btnSalvaGrupoUsuarios = new AnimarBotaoLoading("btnSalvar");
        btnExcluiGruposUsuarios = new AnimarBotaoLoading("btnExcluir");
        btnSalvaGrupoUsuarios.animar();
        btnExcluiGruposUsuarios.animar();

        $("#bootbox-delete").modal("hide");
        Loading.on();
        var formData = $("#formCadastroGruposUsuarios").serialize();
        $.ajax({
          url: "/veterinariaUNESC/server/gruposUsuarios/excluiGruposUsuarios",
          method: "POST",
          data: formData,
          success: function (response) {
            if (response.RESULT) {
              Notificacao.NotificacaoSucesso();
              bootbox.hideAll();
              if (typeof dataTableGruposUsuarios !== "undefined") {
                dataTableGruposUsuarios.ajax.reload();
              }
            }
          },
          error: function (xhr, status, error) {
            Notificacao.NotificacaoErro(xhr.responseJSON.MESSAGE);
          },
          complete: function () {
            Loading.off();
            btnSalvaGrupoUsuarios.restaurar();
            btnExcluiGruposUsuarios.restaurar();
          },
        });
      }
    },
  });
}
