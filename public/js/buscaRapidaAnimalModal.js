var tableBuscaRapida;

function constructModalBuscaAnimal() {

  new Select2('#especieModal', {
    url: '/veterinaria/server/especie/general',
    dropdownParent: '.search-animal'
  })

  new Select2('#racaModal', {
    url: '/veterinaria/server/raca/general',
    dropdownParent: '.search-animal'
  })

  tableBuscaRapida = $("#gridDataTableBuscaRapidaAnimal").DataTable({
    language: {
      url: "/veterinaria/public/languages/datatablePt-BR.json",
    },
    pageLength: 10,
    lengthChange: false,
    scrollX: true,
  });

  tableBuscaRapida.columns.adjust().draw();
}

function BuscarRapidaAnimal() {
  Loading.on();
  var formData = $("#formBuscaRapidaAnimal").serialize();

  $.ajax({
    url: "/veterinaria/server/animais/retornaPesquisaModal",
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
        tableBuscaRapida.clear();
        if (response.RETURN) {
          var animais = response.RETURN;
          for (var i = 0; i < animais.length; i++) {
            var animal = animais[i];
            tableBuscaRapida.row
              .add([
                animal.NM_ANIMAL,
                animal.ESPECIE,
                animal.RACA,
                animal.NM_PESSOA,
                '<button class="btn btn-primary" onclick="iniciarAtendimento(' + animal.CD_ANIMAL + ')">Iniciar Atendimento <i class="bi bi-clipboard2-pulse-fill"></i></button>',
              ])
              .draw();
          }
        } else {
          Notificacao.NotificacaoAviso('Nenhum registro encontrado!');
        }
        tableBuscaRapida.draw();
      }
      Loading.off();
    },
    error: function (xhr, status, error) {
      Notificacao.NotificacaoErro(xhr.responseJSON.MESSAGE);
      Loading.off();
    },
  });
}
