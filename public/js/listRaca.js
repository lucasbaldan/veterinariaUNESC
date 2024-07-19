var dataTable;
$(document).ready(function () {
  dataTable = $("#gridDataTable").DataTable({
    scrollX: true,
    orderCellsTop: true,
    fixedHeader: true, 
    language: {
      url: "/veterinariaUNESC/public/languages/datatablePt-BR.json",
    },
    ajax: {
      url: "/veterinariaUNESC/server/raca/grid",
      type: "POST",
      data: function (d) {
        d.columns[0].search.value = $('#pesquisaCodigoRaca').val();
        d.columns[1].search.value = $('#pesquisaDescricaoRaca').val();
        d.columns[2].search.value = $('#pesquisaEspecie').val();
        d.columns[3].search.value = $('#pesquisaAtivoRaca').val();
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
      { data: "CD_RACA" },
      { data: "DESCRICAO" },
      { data: "ESPECIE_DESCRICAO" },
      { data: "FL_ATIVO" },
    ],
    processing: true,
    serverSide: true,
    createdRow: function (row, data, dataIndex) {
      $("td", row).each(function (index) {
        var cell = $(this);
        var recordId = data.CD_RACA;
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


  $('#pesquisaCodigoRaca, #pesquisaDescricaoRaca, #pesquisaAtivoRaca, #pesquisaEspecie').on('keyup clear input', function() {
    if (this.timer) clearTimeout(this.timer);
    this.timer = setTimeout(function() {
      dataTable.draw();
    }, 2000);
  });
});

function openCadastro($id = null) {
  try {
    Loading.on();

    var ajaxModal = $.ajax({
      url: "/veterinariaUNESC/modais/cadastroRaca",
      method: "POST",
      data: { id: $id },
    });

    var script = $.getScript("/veterinariaUNESC/public/js/CadastroRacaModal.js?v=" + window.scriptVersao);

    $.when(ajaxModal, script).done(function (respostaAjaxModal) {
        bootbox.dialog({
          title: "Cadastro de Raça",
          centerVertical: true,
          message: respostaAjaxModal[0],
          className: 'cad-raca',
          onShown: function(){
            selectTipoAnimal();
          }
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
