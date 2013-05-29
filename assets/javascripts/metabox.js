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
  
  $('table.enqueue').each(function(){
    if ( $(this).find('tbody tr').length > 1 ) {
      $(this).find('thead').show();
    }
  });
  
  var $customcode = $('.custom-code');
  $('.custom-code [name="_customcode_js_enabled"]').change(function(){
    $customcode[ $(this).is(':checked') ? 'addClass' : 'removeClass']('js-enabled');
    fix_width();
  });
  $('.custom-code [name="_customcode_css_enabled"]').change(function(){
    $customcode[ $(this).is(':checked') ? 'addClass' : 'removeClass']('css-enabled');
    fix_width();
  });
  
  var inst = [];
  
  inst[inst.length] = CodeMirror.fromTextArea($('.codemirror.js')[0], {
    mode: 'javascript',
    theme: 'blackboard',
    lineNumbers: true,
    viewportMargin: Infinity
  });
  inst[inst.length] = CodeMirror.fromTextArea($('.codemirror.css')[0], {
    mode: 'css',
    theme: 'blackboard',
    lineNumbers: true,
    viewportMargin: Infinity
  });
  
  var $cm = $('.custom-code .CodeMirror');
  $(window).on('resize', fix_width);
  fix_width();
  function fix_width() {
    $cm.each(function(){
      $(this).width($(this).parents('.codemirror-container').width());
    });
  }
  
  setTimeout(function(){
    var show = postboxes.pbshow;
    postboxes.pbshow = function(id){
      if ( show && typeof(show) == 'function') show(id);
      if ( id != 'custom-code-meta-box') return;
      fix_width();
      for (var i=0; i<inst.length; i++) inst[i].refresh();
    };
  },10);
});