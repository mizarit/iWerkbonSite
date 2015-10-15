

var winPermissions;
var tpp;

Ext.onReady(function(){

  if (!tpp) {
    tpp = new Ext.TabPanel({
        applyTo: 'permissions-tabs',
        autoTabs:true,
        activeTab:0,
        deferredRender:false,
        border:false
    });
  }
                
    var button = Ext.get('permissions-btn');
    if (!button) return;
    button.on('click', function(){
        // create the window on the first click and reuse on subsequent clicks
        if(!winPermissions){
            winPermissions = new Ext.Window({
                applyTo:'permissions-win',
                layout:'fit',
                width:800,
                height:530,
                closeAction:'close',
                plain: true,

                items: tpp,

                buttons: [{
                    text: 'Sluiten',
                    handler: function(){
                      zpermissions.cancel();
                      winPermissions.hide();
                    }
                }],
                
                close: function()
                {
                  zpermissions.cancel();
                  winPermissions.hide();
                }
                
            });
        }
        
        winPermissions.show(this);
        new Ajax.Updater('zeus-permissions', '/permissionsadmin/config', { });
    });

});

zeusPermissions = Class.create();
zeusPermissions.prototype =
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


