$(document).ready(function () {

  new Select2('#select2cdCidade', {
    url: '/veterinariaUNESC/server/municipio/general',
  })

  new Select2('#select2cdBairro', {
    url: '/veterinariaUNESC/server/bairro/general',
  })

  new Select2('#select2cdLogradouro', {
    url: '/veterinariaUNESC/server/logradouro/general',
  })

  $('#nrTelefone').inputmask("(99) 99999-9999", { autoUnmask: true });
  $('#cpfPessoa').inputmask("999.999.999-99", { autoUnmask: true });

});



function salvarCadastroPessoas() {
  Loading.on();
  var formData = $("#formCadastroPessoas").serialize();

  $.ajax({
    url: "/veterinariaUNESC/server/pessoas/controlar",
    method: "POST",
    data: formData,
    success: function (response) {
      if (response.RESULT) {
        sessionStorage.setItem('notificarSucesso', 'true');
        window.location.href = '/veterinariaUNESC/paginas/listPessoas';
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

function excluirCadastroPessoas() {
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
        var formData = $("#formCadastroPessoas").serialize();
        $.ajax({
          url: "/veterinariaUNESC/server/pessoas/excluiPessoa",
          method: "POST",
          data: formData,
          success: function (response) {
            if (response.RESULT) {
              sessionStorage.setItem('notificarSucesso', 'true');
              window.location.href = '/veterinariaUNESC/paginas/listPessoas';
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

function gerarPDF() {
  var html = $('#formCadastroPessoas').html();
  var nmPdf = $('#nmPessoa').val();

  // console.log(html);
  // return;

  $.ajax({
    url: "/veterinariaUNESC/server/relatorios/fichaLPV",
    method: "POST",
    data: {
      html: html,
      nmArquivo: nmPdf,
      orientacao: 'portrait'
    },
    success: function (data) {
    },
    error: function (xhr, status, error) {
      // Notificacao.NotificacaoErro(xhr.responseJSON.MESSAGE);
    },
    complete: function () {
      Loading.off();
    },
  });
}
