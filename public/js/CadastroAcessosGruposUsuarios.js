const notificacao = new Notificacao({
  duration: 10000,
  position: {
    x: 'end',
    y: 'top',
  },
});

function salvarCadastroAcessosGruposUsuarios() {
  // Loading.on();
  var formData = $("#formCadastroAcessosGruposUsuarios").serialize();
  $.ajax({
    url: "/veterinariaUNESC/server/gruposUsuarios/acessos",
    method: "POST",
    data: formData,
    beforeSend: function () {
      Loading.on();
    },
    success: function (response) {
      if (response.RESULT) {
        // sessionStorage.setItem('notificarSucesso', 'true');
        notificacao.push('Permissões salvas com Sucesso!', 'success');
        // window.location.href = '/veterinariaUNESC/paginas/listPessoas';
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
