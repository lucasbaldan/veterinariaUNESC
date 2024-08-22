function selectTipoAnimal(){
  new Select2('#select2cdTipoAnimal', {
    url: '/veterinaria/server/tipoAnimal/general',
    dropdownParent: '.cad-tipo-animal'
  })
}

function salvarCadastroEspecie() {
  Loading.on();
  var formData = $("#formCadastroEspecie").serialize();

  $.ajax({
    url: "/veterinaria/server/especie/controlar",
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

function excluirCadastroEspecie() {
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
        var formData = $("#formCadastroEspecie").serialize();

        $.ajax({
          url: "/veterinaria/server/especie/excluir",
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
