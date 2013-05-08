jQuery(function($){
  
  $('.custom-code .enqueue tbody').sortable();
  
  $('.custom-code .enqueue .button-add').click(function(e){
    
    e.preventDefault();
    
    var $table = $(this).parents('.enqueue')
      , $clone = $table.find('.clone').clone().removeClass('clone')
      , $tbody = $table.find('tbody')
    
    $clone.find('input').each(function(){
      $(this).attr('name', $(this).data('name'));
    });
      
    $tbody.append($clone);
    $table.find('thead').show();
  });
  
  $('.custom-code .enqueue').on('click',  '.button-remove', function(e){
    
    e.preventDefault();
    
    var $table = $(this).parents('table.enqueue')
    
    $(this).parents('tr.customcode-file').remove();
    if ( $table.find('tbody tr').length < 2 ) {
      $table.find('thead').hide();
    }
  });
});