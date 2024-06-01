function salvarCadastroTipoAnimal(){
  
    Loading.on();
    var formData = $('#formCadastroTipoAnimal').serialize();
  
    $.ajax({
      url: '/veterinariaUNESC/server/tipoAnimal/controlar',
      method: 'POST',
      data: formData,
      success: function(response) {
         if(response.RESULT){
          Notificacao.NotificacaoSucesso();
          bootbox.hideAll();
          if (typeof dataTableTipoAnimal !== 'undefined') {
            dataTableTipoAnimal.ajax.reload();
          }
         }
      },
      error: function(xhr, status, error) {
        Notificacao.NotificacaoErro(xhr.responseJSON.MESSAGE);
      },
      complete: function(){
        Loading.off();
      },
  });
  }
  
  function excluirCadastroTipoAnimal(){
    
    Loading.on();
    var formData = $('#formCadastroTipoAnimal').serialize();
  
    $.ajax({
      url: '/veterinariaUNESC/server/tipoAnimal/excluir',
      method: 'POST',
      data: formData,
      success: function(response) {
         if(response.RESULT){
          Notificacao.NotificacaoSucesso();
          bootbox.hideAll();
          if (typeof dataTableTipoAnimal !== 'undefined') {
            dataTableTipoAnimal.ajax.reload();
          }
         }
      },
      error: function(xhr, status, error) {
        Notificacao.NotificacaoErro(xhr.responseJSON.MESSAGE);
      },
      complete: function(){
        Loading.off();
      },
  });
  }