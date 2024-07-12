var table;

function constructModalBuscaPessoa() {
  $("#telefoneCelularModal").inputmask("(99) 99999-9999", { autoUnmask: true });

  new Select2('#select2cdCidade', {
    url: '/veterinariaUNESC/server/municipio/general',
  })

  table = $("#gridDataTable").DataTable({
    language: {
      url: "/veterinariaUNESC/public/languages/datatablePt-BR.json",
    },
    pageLength: 10,
    lengthChange: false
  });
}

function BuscarRapidoPessoa() {
  Loading.on();
  var formData = $("#formBuscaRapidaPessoa").serialize();

  $.ajax({
    url: "/veterinariaUNESC/server/pessoas/retornaPesquisaModal",
    method: "POST",
    data: formData,
    success: function (response) {
      if (response.RESULT) {
        table.clear();
        if (response.RETURN) {
          var pessoas = response.RETURN;
          for (var i = 0; i < pessoas.length; i++) {
            var pessoa = pessoas[i];
            table.row
              .add([
                pessoa.NM_PESSOA,
                pessoa.CIDADE,
                pessoa.NR_TELEFONE,
                '<button class="btn btn-primary" onclick="selecionarPessoa(' + pessoa.CD_PESSOA + ')">Selecionar</button>',
              ])
              .draw();
          }
        } else {
            table.draw();
            tableNaoEncontrado();
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
