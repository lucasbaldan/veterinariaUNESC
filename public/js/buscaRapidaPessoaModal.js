var table;

function constructModalBuscaPessoa() {
  $("#cpfPessoaModal").inputmask("999.999.999-99", { autoUnmask: true });

  table = $("#gridDataTable").DataTable();
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
                pessoa.nm_pessoa,
                pessoa.cpf,
                pessoa.data_nascimento,
                '<button class="btn btn-primary" data-id="' +
                  pessoa.id +
                  '">Selecionar</button>',
              ])
              .draw();
          }
        } else {
            table.draw();
            Notificacao.NotificacaoAviso('Nenhum registro encontrado!');
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
