var winHistory;
var tp;

Ext.onReady(function(){

  if (!tp) {
    tp = new Ext.TabPanel({
        applyTo: 'history-tabs',
        autoTabs:true,
        layoutOnTabChange:true,
        activeTab:0,
        deferredRender:false,
        border:false
    });
  }
                
    var button = Ext.get('history-btn');

    if (!button) return;
    
    button.on('click', function(){
        // create the window on the first click and reuse on subsequent clicks
        if(!winHistory){
            winHistory = new Ext.Window({
                applyTo:'history-win',
                layout:'fit',
                width:800,
                height:530,
                closeAction:'close',
                plain: true,
                items: tp,

                buttons: [{
                    text: 'Sluiten',
                    handler: function(){
                      zhistory.cancel();
                      winHistory.hide();
                    }
                }],
                
                close: function()
                {
                  zhistory.cancel();
                  winHistory.hide();
                }
                
            });
        }
        winHistory.show(this);
    });

});

zeusHistory = Class.create();
zeusHistory.prototype =
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

