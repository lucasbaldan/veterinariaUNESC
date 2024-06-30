var tableBuscaRapida;

function constructModalBuscaAnimal() {

  tableBuscaRapida = $("#gridDataTableBuscaRapidaAnimal").DataTable({
    language: {
      url: "/veterinariaUNESC/public/languages/datatablePt-BR.json",
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
    url: "/veterinariaUNESC/server/animais/retornaPesquisaModal",
    method: "POST",
    data: formData,
    success: function (response) {
      if (response.RESULT) {
        tableBuscaRapida.clear();
        if (response.RETURN) {
          var animais = response.RETURN;
          for (var i = 0; i < animais.length; i++) {
            var animal = animais[i];
            tableBuscaRapida.row
              .add([
                animal.nm_animal,
                animal.nm_tipo_animal,
                animal.ano_nascimento,
                animal.nm_pessoa,
                '<button class="btn btn-primary" onclick="iniciarAtendimento(' + animal.cd_animal + ')">Iniciar Atendimento <i class="bi bi-clipboard2-pulse-fill"></i></button>',
              ])
              .draw();
          }
        } else {
          Notificacao.NotificacaoAviso('Nenhum registro encontrado!');
        }
        tableBuscaRapida.draw();
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
