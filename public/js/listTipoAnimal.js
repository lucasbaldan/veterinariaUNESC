$(document).ready(function () {
  $("#gridTipoAnimal").DataTable({
    language: {
      url: "/veterinariaUNESC/public/languages/datatablePt-BR.json",
    },
    ajax: {
      url: '/veterinariaUNESC/server/tipoAnimal/grid',
      type: 'POST',
      dataSrc: function (json) {
        json.draw = json.RETURN.draw;
        json.recordsTotal = json.RETURN.recordsTotal;
        json.recordsFiltered = json.RETURN.recordsFiltered;
        return json.RETURN.data;
      }
    },
    columns: [
        { data: 'cd_tipo_animal' },
        { data: 'descricao' },
        { data: 'fl_ativo' }
    ],
    processing: true,
    serverSide: true
  });
}); 
