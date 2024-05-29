$(document).ready(function () {
  $("#example").DataTable({
    language: {
      url: "/veterinariaUNESC/public/languages/datatablePt-BR.json",
    },
    ajax: 'scripts/objects.php',
    columns: [
        { data: 'first_name' },
        { data: 'last_name' },
        { data: 'position' }
    ],
    processing: true,
    serverSide: true
  });
});
