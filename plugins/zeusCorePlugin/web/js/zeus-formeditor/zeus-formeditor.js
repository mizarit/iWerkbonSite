

var winFormeditor;
var tpf;

Ext.onReady(function(){

  if (!tpf) {
    tpf = new Ext.TabPanel({
        applyTo: 'formeditor-tabs',
        autoTabs:true,
        activeTab:0,
        deferredRender:false,
        border:false
    });
  }
  
  var button = Ext.get('validator-row-btn');

  button.on('click', function(){
    if (!$('validator-row-btn').hasClassName('zeus-button-disabled')) {
      // create the window on the first click and reuse on subsequent clicks
      if(!winFormeditor){
          winFormeditor = new Ext.Window({
              applyTo:'formeditor-win',
              layout:'fit',
              width:500,
              height:330,
              closeAction:'close',
              plain: true,

              items: tpf,

              buttons: [{
                  text: 'Toepassen',
                  handler: function(){
                    zformeditor.submit();
                    winFormeditor.hide();
                  }
              },{
                  text: 'Sluiten',
                  handler: function(){
                    zformeditor.cancel();
                    winFormeditor.hide();
                  }
              }],
              
              close: function()
              {
                zformeditor.cancel();
                winFormeditor.hide();
              }
              
          });
      }

      zformeditor.Options.rows.each(function(s,i) {
        if (i == selectedRow) {
          $('form-default-value').value = s.rvalue;
          $('form-validator').value = s.rvalidator;
          $('form-validator-value').value = s.rvalidatorvalue;
          $('form-options').value = s.roptions;
          $('form-required').checked = s.rrequired;
         
          switch(s.rtype) {
            case 'input':
              $('form-row-default-value').style.display = 'block';
              $('form-row-options').style.display = 'none';
              $('form-row-required').style.display = 'block';
              $('form-row-validator').style.display = 'block';
              $('form-row-validator-value').style.display = 'block';
            break;
            
            case 'select':
              $('form-row-default-value').style.display = 'block';
              $('form-row-options').style.display = 'block';
              $('form-row-required').style.display = 'block';
              $('form-row-validator').style.display = 'block';
              $('form-row-validator-value').style.display = 'block';
            break;
            
            case 'textarea':
              $('form-row-default-value').style.display = 'block';
              $('form-row-options').style.display = 'none';
              $('form-row-required').style.display = 'block';
              $('form-row-validator').style.display = 'block';
              $('form-row-validator-value').style.display = 'block';
            break;
            
            case 'radio':
              $('form-row-default-value').style.display = 'block';
              $('form-row-options').style.display = 'block';
              $('form-row-required').style.display = 'block';
              $('form-row-validator').style.display = 'none';
              $('form-row-validator-value').style.display = 'none';
            break;
            
            case 'checkbox':
              $('form-row-default-value').style.display = 'none';
              $('form-row-options').style.display = 'none';
              $('form-row-required').style.display = 'block';
              $('form-row-validator').style.display = 'none';
              $('form-row-validator-value').style.display = 'none';
            break;
          }
        }
      });

      winFormeditor.show(this);
    }
  });

});

zeusFormeditor = Class.create();
zeusFormeditor.prototype =
{
  initialize: function (Options)
  {
    this.Options = {
      rows: [ ]
    };
    
    Object.extend(this.Options, Options || {});
  },
  
  submit: function()
  {
    new Ajax.Updater('formeditor-container', editorurl, {
      parameters: {
        editrow: selectedRow,
        rvalue: $('form-default-value').value,
        rvalidator: $('form-validator').value,
        rvalidatorvalue: $('form-validator-value').value,
        roptions: $('form-options').value,
        rrequired: $('form-required').checked
      },
      evalScripts: true
    });
    
    selectedRow = false;
    
    $('delete-row-btn').addClassName('zeus-button-disabled');
    $('validator-row-btn').addClassName('zeus-button-disabled');
    
    
  },
  
  cancel: function()
  {
  }
}


