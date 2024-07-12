var table;

function constructModalBuscaPessoa() {
  $("#cpfPessoaModal").inputmask("999.999.999-99", { autoUnmask: true });

  table = $("#gridDataTable").DataTable({
    language: {
      url: "/veterinariaUNESC/public/languages/datatablePt-BR.json",
    },
    pageLength: 10,
    lengthChange: false
  });
}

function formatarData(data) {
  const partes = data.split('-');
  return `${partes[2]}-${partes[1]}-${partes[0]}`;
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
                pessoa.CPF,
                formatarData(pessoa.DATA_NASCIMENTO),
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
