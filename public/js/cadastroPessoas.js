$(document).ready(function () {

  new Select2('#select2cdCidade', {
    url: '/veterinaria/server/municipio/general',
  })

  new Select2('#select2cdBairro', {
    url: '/veterinaria/server/bairro/general',
  })

  new Select2('#select2cdLogradouro', {
    url: '/veterinaria/server/logradouro/general',
  })

  $('#nrTelefone').inputmask("(99) 99999-9999", { autoUnmask: true });
  $('#cpfPessoa').inputmask("999.999.999-99", { autoUnmask: true });

});



function salvarCadastroPessoas() {
  Loading.on();
  var formData = $("#formCadastroPessoas").serialize();

  $.ajax({
    url: "/veterinaria/server/pessoas/controlar",
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
        sessionStorage.setItem('notificarSucesso', 'true');
        window.location.href = '/veterinaria/paginas/listPessoas';
      }
    },
    error: function (xhr, status, error) {
      Loading.off();
      Notificacao.NotificacaoErro(xhr.responseJSON.MESSAGE);
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
        Loading.on();
        $("#bootbox-delete").modal("hide");
        var formData = $("#formCadastroPessoas").serialize();
        $.ajax({
          url: "/veterinaria/server/pessoas/excluiPessoa",
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
              sessionStorage.setItem('notificarSucesso', 'true');
              window.location.href = '/veterinaria/paginas/listPessoas';
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

function gerarPDF() {
  var html = $('#formCadastroPessoas').html();
  var nmPdf = $('#nmPessoa').val();

  // console.log(html);
  // return;

  $.ajax({
    url: "/veterinaria/server/relatorios/fichaLPV",
    method: "POST",
    data: {
      html: html,
      nmArquivo: nmPdf,
      orientacao: 'portrait'
    },
    complete: function(xhr, textStatus) {
      if (xhr.status === 302) {
          var redirectUrl = xhr.getResponseHeader('Location');
          if (redirectUrl) {
              window.location.href = redirectUrl;
          }
      }
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
