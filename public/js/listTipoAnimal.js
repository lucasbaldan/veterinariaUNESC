var dataTableTipoAnimal;
$(document).ready(function () {
  dataTableTipoAnimal = $("#gridDataTable").DataTable({
    language: {
      url: "/veterinariaUNESC/public/languages/datatablePt-BR.json",
    },
    ajax: {
      url: "/veterinariaUNESC/server/tipoAnimal/grid",
      type: "POST",
      dataSrc: function (json) {
        json.draw = json.RETURN.draw;
        json.recordsTotal = json.RETURN.recordsTotal;
        json.recordsFiltered = json.RETURN.recordsFiltered;
        return json.RETURN.data;
      },
    },
    columns: [
      { data: "cd_tipo_animal" },
      { data: "descricao" },
      { data: "fl_ativo" },
    ],
    processing: true,
    serverSide: true,
    createdRow: function (row, data, dataIndex) {
      $("td", row).each(function (index) {
        var cell = $(this);
        var recordId = data.cd_tipo_animal;
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
    initComplete: function () {
      this.api()
        .columns()
        .every(function () {
          var column = this;
          var title = column.footer().textContent;

          // Create input element and add event listener
          $(
            '<input class="form-control form-control-sm" type="text" placeholder="Pesquisar..." />'
          )
            .appendTo($(column.footer()).empty())
            .on("keyup change clear", function () {
              if (column.search() !== this.value) {
                column.search(this.value).draw();
              }
            });
        });
    },
  });
});

function openCadastro($id = null) {
  try {
    Loading.on();

    var ajaxModal = $.ajax({
      url: "/veterinariaUNESC/modais/cadastroTipoAnimal",
      method: "POST",
      data: { id: $id },
    });

    var script = $.getScript("/veterinariaUNESC/public/js/CadastroTipoAnimalModal.js");

    $.when(ajaxModal, script).done(function (respostaAjaxModal) {
        bootbox.dialog({
          title: "Cadastro de Tipo de Animal",
          message: respostaAjaxModal[0],
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
