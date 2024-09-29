$(document).ready(function () {

  if (sessionStorage.getItem("notificarSucesso") === "true") {
    Notificacao.NotificacaoSucesso();
    sessionStorage.removeItem("notificarSucesso");
  }

  $.fn.filepond.registerPlugin(FilePondPluginImagePreview, FilePondPluginFileValidateType);
  $('.my-pond').filepond({
    allowMultiple: true,
    labelIdle: 'Arraste e solte o arquivo ou <span class="filepond--label-action">clique aqui</span>', // Texto personalizado para o label
    //maxFileSize: '2MB', // Tamanho máximo do arquivo
    imagePreviewHeight: 150,
    imagePreviewWidth: 200,
    acceptedFileTypes: ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'], // Tipos de arquivo aceitos

    instantUpload: true,
    server: {
      process: {
        url: '/veterinaria/server/laboratorios/uploadLogo',
        method: 'POST',
        withCredentials: false,
        headers: {},
        onerror: function (response) {
          console.log('Erro no envio:', response);
        },
        ondata: function (formData) {
          formData.append('cdLaboratorio', $('#cdLaboratorio').val());
          return formData;
        }
      }
    },
  });

  $('.my-pond').on('FilePond:processfile', function (e, file) {
    // console.log('Arquivo enviado com sucesso:');
    atualizarGaleria();
  });

  Fancybox.bind('[data-fancybox]', {
    // Custom options for all galleries
  });
});

function salvarCadastroLaboratorios() {
  Loading.on();
  var formData = $("#formCadastroLaboratorios").serialize();

  $.ajax({
    url: "/veterinaria/server/laboratorios/controlar",
    method: "POST",
    data: formData,
    complete: function (xhr, textStatus) {
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
        window.location.href = '/veterinaria/paginas/listLaboratorios';
      }
    },
    error: function (xhr, status, error) {
      Loading.off();
      Notificacao.NotificacaoErro(xhr.responseJSON.MESSAGE);
    },
  });
}

function excluirCadastroLaboratorios() {
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
        var formData = $("#formCadastroLaboratorios").serialize();
        $.ajax({
          url: "/veterinaria/server/laboratorios/excluir",
          method: "POST",
          data: formData,
          complete: function (xhr, textStatus) {
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
              window.location.href = '/veterinaria/paginas/listLaboratorios';
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


function atualizarGaleria() {
  var dados = {
    cdAtendimento: $('#cdLaboratorio').val()
  };

  $.post('/veterinaria/modais/recarregarGaleria', dados, function (response) {
    $('#galeriaDIV').html(response);
  });
}

$('#galeriaDIV').on('click', '.btn-excluir-galeria', function (e) {
  e.preventDefault();

  // Obtém o nome ou identificador da imagem a ser excluída do atributo data-imagem
  var idImagem = $(this).attr('id');

  bootbox.confirm({
    className: "bootbox-delete",
    size: "extra-large",
    title: "Confirmar exclusão?",
    centerVertical: true,
    message:
      "Você tem <b>certeza</b> que deseja excluir esse registro? Esta ação não poderá ser desfeita.",
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
        $.ajax({
          url: "/veterinaria/server/laboratorios/excluirLogo",
          method: "POST",
          data: { idImagem: idImagem },
          complete: function (xhr, textStatus) {
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
              atualizarGaleria();
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
});