var dataTable;
$(document).ready(function () {
  dataTable = $("#gridDataTable").DataTable({
    scrollX: true,
    orderCellsTop: true,
    fixedHeader: true, 
    language: {
      url: "/veterinaria/public/languages/datatablePt-BR.json",
    },
    ajax: {
      url: "/veterinaria/server/logradouro/grid",
      type: "POST",
      data: function (d) {
        d.columns[0].search.value = $('#pesquisaCodigoLogradouro').val();
        d.columns[1].search.value = $('#pesquisaDescricaoLogradouro').val();
      },
      dataSrc: function (json) {
        json.draw = json.RETURN.draw;
        json.recordsTotal = json.RETURN.recordsTotal;
        json.recordsFiltered = json.RETURN.recordsFiltered;
        return json.RETURN.data;
      },
      complete: function(xhr, textStatus) {
        if (xhr.status === 302) {
            var redirectUrl = xhr.getResponseHeader('Location');
            if (redirectUrl) {
                window.location.href = redirectUrl;
            }
        }
    },
    },
    columns: [
      { data: "CD_LOGRADOURO" },
      { data: "NOME" },
    ],
    processing: true,
    serverSide: true,
    createdRow: function (row, data, dataIndex) {
      $("td", row).each(function (index) {
        var cell = $(this);
        var recordId = data.CD_LOGRADOURO;
        var cellText = cell.text();
        cell.html(
          '<span class="dataTable-item" onclick="openCadastro(' +
            recordId +
            ')">' +
            cellText +
            "</span>"
        );
      });
    },
  });


  $('#pesquisaCodigoLogradouro, #pesquisaDescricaoLogradouro').on('keyup clear input', function() {
    if (this.timer) clearTimeout(this.timer);
    this.timer = setTimeout(function() {
      dataTable.draw();
    }, 1000);
  });
});

function openCadastro($id = null) {
  try {
    Loading.on();

    var ajaxModal = $.ajax({
      url: "/veterinaria/modais/cadastroLogradouro",
      method: "POST",
      data: { id: $id },
    });

    var script = $.getScript("/veterinaria/public/js/CadastroLogradouroModal.js?v=" + window.scriptVersao);

    $.when(ajaxModal, script).done(function (respostaAjaxModal) {
        bootbox.dialog({
          title: "Cadastro de Logradouro",
          centerVertical: true,
          message: respostaAjaxModal[0],
          className: 'cad-logradouro',
        });
      })
      .fail(function (xhr, status, error) {
      })
      .always(function () {
        Loading.off();
      });
  } catch (e) {
    Loading.off();
  }
}
