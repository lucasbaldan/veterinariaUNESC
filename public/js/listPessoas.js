var dataTablePessoas;
$(document).ready(function () {
  dataTablePessoas = $("#gridDataTable").DataTable({
    scrollX: true,
    orderCellsTop: true,
    fixedHeader: true,
    language: {
      url: "/veterinariaUNESC/public/languages/datatablePt-BR.json",
    },
    ajax: {
      url: "/veterinariaUNESC/server/pessoas/grid",
      type: "POST",
      data: function (d) {
        d.columns[0].search.value = $('#pesquisaCodigoPessoa').val();
        d.columns[1].search.value = $('#pesquisaNomePessoa').val();
        d.columns[2].search.value = $('#pesquisaAtivoPessoa').val();
      },
      dataSrc: function (json) {
        json.draw = json.RETURN.draw;
        json.recordsTotal = json.RETURN.recordsTotal;
        json.recordsFiltered = json.RETURN.recordsFiltered;
        return json.RETURN.data;
      },
    },
    columns: [
      { data: "CD_PESSOA" },
      { data: "NM_PESSOA" },
      { data: "fl_ativo" },
    ],
    processing: true,
    serverSide: true,
    createdRow: function (row, data, dataIndex) {
      $("td", row).each(function (index) {
        var cell = $(this);
        var recordId = data.CD_PESSOA;
        var cellText = cell.text();
        cell.html(
          '<span class="dataTable-item" onclick="redirectToCadastro(' +
          recordId +
          ')">' +
          cellText +
          "</span>"
        );
      });
    },
  });

    $('#pesquisaCodigoPessoa, #pesquisaNomePessoa, #pesquisaAtivoPessoa').on('keyup clear input', function() {
      if (this.timer) clearTimeout(this.timer);
      this.timer = setTimeout(function() {
        dataTablePessoas.draw();
      }, 500);
    });

    // initComplete: function () {
    //   this.api()
    //     .columns()
    //     .every(function () {
    //       var column = this;
    //       var title = column.footer().textContent;

    //       // Create input element and add event listener
    //       $(
    //         '<input class="form-control form-control-sm" type="text" placeholder="Pesquisar..." />'
    //       )
    //         .appendTo($(column.footer()).empty())
    //         .on("keyup change clear", function () {
    //           if (column.search() !== this.value) {
    //             column.search(this.value).draw();
    //           }
    //         });
    //     });
    // },
});

function redirectToCadastro(cdPessoa) {
  const form = document.createElement('form');
  form.method = 'POST';
  form.action = '/veterinariaUNESC/paginas/cadastroPessoas';

  const input = document.createElement('input');
  input.type = 'hidden';
  input.name = 'id';
  input.value = cdPessoa;
  
  form.appendChild(input);
  document.body.appendChild(form);
  form.submit();
}


// function openCadastro(cdPessoa = null) {
//   $.ajax({
//     url: '/veterinariaUNESC/paginas/cadastroPessoas',
//     type: 'POST',
//     dataType: 'json',
//     data: { id: cdPessoa },
//     beforeSend: function () {
//       Loading.on();
//     },
//     success: function (response) {
//       console.log('Requisição bem-sucedida', response);
//       // Aqui você pode adicionar código para atualizar a interface com a resposta recebida
//     },
//     error: function (xhr, status, error) {
//       console.error('Erro na requisição AJAX:', error);
//       console.error('Detalhes do erro:', xhr.responseText);
//     },
//     complete: function () {
//       Loading.off();
//     }
//   });
// }
