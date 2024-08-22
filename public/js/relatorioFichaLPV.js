$(document).ready(function () {

  new Select2('#cdCidade', {
    url: '/veterinaria/server/municipio/general',
  })

  new Select2('#cdVetRemetente', {
    url: '/veterinaria/server/pessoas/general',
  })

  new Select2('#cdAnimal', {
    url: '/veterinaria/server/tipoAnimal/general',
  })

});



function filtrarFichasLPV() {
  Loading.on();
  var formData = $("#formRelatorioFichaLPV").serialize();

  console.log(formData);

  $.ajax({
    url: "/veterinaria/paginas/relatorioFichaLPV",
    method: "post",
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
      // if (response.RESULT) {
        // sessionStorage.setItem('notificarSucesso', 'true');
        // window.location.href = '/veterinaria/paginas/relatorioFichaLPV';
      // }
    },
    error: function (xhr, status, error) {
      // Notificacao.NotificacaoErro(xhr.responseJSON.MESSAGE);
    },
    complete: function () {
      Loading.off();
    },
  });
}

function LimparCamposFiltroFichaLPV() {
  $('.select2-selection__clear').trigger('click');
  $('#formRelatorioFichaLPV').trigger('reset');
}

function gerarPDF() {
  var html = $('#formCadastroPessoas').html();
  var nmPdf = $('#nmPessoa').val();

  // console.log(html);
  // return;

  $.ajax({
    url: "/veterinaria/server/pdf/geraPdf",
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
      var blob = new Blob([data], { type: 'application/pdf' });
      var url = window.URL.createObjectURL(blob);
      var a = document.createElement('a');
      a.href = url;
      a.download = 'ficha_'+ nmPdf + '.pdf';
      document.body.appendChild(a); // Necessário para o Firefox
      a.click();
      window.URL.revokeObjectURL(url);
    },
    error: function (xhr, status, error) {
      // Notificacao.NotificacaoErro(xhr.responseJSON.MESSAGE);
    },
    complete: function () {
      Loading.off();
    },
  });
}
