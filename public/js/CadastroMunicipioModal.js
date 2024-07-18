function selectEstado(){
  new Select2('#select2cdEstado', {
    url: '/veterinariaUNESC/server/estado/general',
    dropdownParent: '.cad-municipio'
  })
}

function salvarCadastroMunicipio() {
  Loading.on();
  var formData = $("#formCadastroMunicipio").serialize();

  $.ajax({
    url: "/veterinariaUNESC/server/municipio/controlar",
    method: "POST",
    data: formData,
    success: function (response) {
      if (response.RESULT) {
        Notificacao.NotificacaoSucesso();
        bootbox.hideAll();
        if (typeof dataTable !== "undefined") {
          dataTable.ajax.reload();
        }
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

function excluirCadastroMunicipio() {
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
        Loading.on();
        $("#bootbox-delete").modal("hide");
        var formData = $("#formCadastroMunicipio").serialize();
        $.ajax({
          url: "/veterinariaUNESC/server/municipio/excluir",
          method: "POST",
          data: formData,
          success: function (response) {
            if (response.RESULT) {
              Notificacao.NotificacaoSucesso();
              bootbox.hideAll();
              if (typeof dataTable !== "undefined") {
                dataTable.ajax.reload();
              }
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
