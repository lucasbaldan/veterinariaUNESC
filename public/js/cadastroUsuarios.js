$(document).ready(function () {
  new Select2('#select2cdPessoa', {
    url: '/veterinariaUNESC/server/pessoas/general',
  })

  new Select2('#cdGrupoUsuarios', {
    url: '/veterinariaUNESC/server/gruposUsuarios/general',
  })

  const notificacao = new Notificacao({
    duration: 10000,
    position: {
      x: 'center',
      y: 'bottom',
    },
  });

  $('#formCadastroUsuarios').submit(function (event) {
    event.preventDefault();

    var dadosForm = $(this).serialize();
    var codigo = $('#cdUsuario').val();

    // console.log('Formulário:: ', dadosForm);

    $.ajax({
      url: '/veterinariaUNESC/server/usuarios/salvaUsuario',
      type: 'POST',
      dataType: 'json',
      data: dadosForm,
      beforeSend: function () {
        Loading.on();
      },
      success: function (response) {
        // alert(response);
        // console.log('Retorno: ', response.RETURN);
        if (codigo == '') {
          notificacao.push('Usuário salvo com Sucesso!', 'success');
        } else {
          notificacao.push('Usuário atualizado com Sucesso!', 'success');
        }

        if (codigo == '') {
          $('#cdUsuario').val(response.RETURN);
        }
      },
      error: function (xhr, status, error) {
        // console.log('DADOS::: ', xhr.responseJSON);
        // notificacao.push(xhr.responseJSON.MESSAGE, 'warning');
        Notificacao.NotificacaoErro(xhr.responseJSON.MESSAGE);
      },
      complete: function () {
        Loading.off();
      }
    });
  });
});

// $('#btnExcluiUsuario').on('click', () => {
function excluirCadastroUsuario() {
  // console.log('WWWWWW');
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
        var formData = $("#formCadastroUsuarios").serialize();
        $.ajax({
          url: "/veterinariaUNESC/server/usuarios/excluiUsuario",
          method: "POST",
          data: formData,
          success: function (response) {
            if (response.RESULT) {
              Notificacao.NotificacaoSucesso();
              bootbox.hideAll();
              window.location.href = '/veterinariaUNESC/paginas/listUsuarios';
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
// })