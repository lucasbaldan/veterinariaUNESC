$(document).ready(function () {
  $("#gridDataTable").DataTable({
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
    initComplete: function () {
      this.api()
        .columns()
        .every(function () {
          var column = this;
          var title = column.footer().textContent;

          // Create input element and add event listener
          $('<input class="form-control form-control-sm" type="text" placeholder="Pesquisar..." />')
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
