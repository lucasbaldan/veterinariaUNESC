$(document).ready(function () {
  var dataTable = $("#gridDataTable").DataTable({
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

  try{

    Loading.on();

  $.ajax({
    url: '/veterinariaUNESC/public/js/CadastroTipoAnimalModal.js',
    method: 'GET',
    dataType: 'text',
    success: function(jsContent) {
        eval(jsContent);
    },
    error: function(xhr, status, error) {
    }
});
   
  $.ajax({
      url: '/veterinariaUNESC/modais/cadastroTipoAnimal',
      method: 'POST',
      data: { id: $id },
      success: function(response) {
          bootbox.dialog({
              title: "Cadastro de Tipo de Animal",
              message: response,
          });
      },
      error: function(xhr, status, error) {
      }
  });

  Loading.off();
}catch{

}
}

function salvarCadastroTipoAnimal(){
  
  Loading.on();
  var formData = $('#formCadastroTipoAnimal').serialize();

  $.ajax({
    url: '/veterinariaUNESC/server/cadastroTipoAnimal',
    method: 'POST',
    data: formData,
    success: function(response) {

    },
    error: function(xhr, status, error) {
    },
    complete: function(){
      Loading.off();
    },
});
}