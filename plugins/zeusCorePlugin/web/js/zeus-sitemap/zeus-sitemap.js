function collapse(what) {
  var c = document.getElementById(what+'-sub');
  var t = $(what+'-toggle');

  $(what).classNames().each(function(s) {
    $(what).removeClassName(s);
    t.removeClassName(s);
    
    switch(s) {
      case 'open':
        $(what).addClassName('closed');    
        t.addClassName('closed');
        break;
        
      case 'closed':
        $(what).addClassName('open');   
        t.addClassName('open'); 
        break;
        
      case 'open-last':
        $(what).addClassName('closed-last');    
        t.addClassName('closed-last');  
        break;
        
      case 'closed-last':
        $(what).addClassName('open-last');    
        t.addClassName('open-last');  
        break;
    }
  });
}