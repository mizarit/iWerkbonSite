

var winHelp;
var tph;

Ext.onReady(function(){

  if (!tph) {
    tph = new Ext.TabPanel({
        applyTo: 'help-tabs',
        autoTabs:true,
        activeTab:0,
        deferredRender:false,
        border:false
    });
  }
                
    var button = Ext.get('help-btn');

    button.on('click', function(){
        // create the window on the first click and reuse on subsequent clicks
        if(!winHelp){
            winHelp = new Ext.Window({
                applyTo:'help-win',
                layout:'fit',
                width:800,
                height:530,
                closeAction:'close',
                plain: true,

                items: tph,

                buttons: [{
                    text: 'Sluiten',
                    handler: function(){
                      zhelp.cancel();
                      winHelp.hide();
                    }
                }],
                
                close: function()
                {
                  zhelp.cancel();
                  winHelp.hide();
                }
                
            });
        }
        
        winHelp.show(this);
        new Ajax.Updater('zeus-help', '/help', { });
    });

});

zeusHelp = Class.create();
zeusHelp.prototype =
{
  initialize: function (Options)
  {
    this.Options = {

    };
    
    Object.extend(this.Options, Options || {});
  },
  
  submit: function()
  {
  
  },
  
  cancel: function()
  {
  }
}


