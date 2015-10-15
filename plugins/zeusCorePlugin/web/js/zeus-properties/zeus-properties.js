Ext.onReady(function(){
    var win;
    var button = Ext.get('properties-btn');

    button.on('click', function(){
        // create the window on the first click and reuse on subsequent clicks
        if(!win){
            win = new Ext.Window({
                applyTo:'properties-win',
                layout:'fit',
                width:700,
                height:340,
                closeAction:'close',
                plain: true,

                items: new Ext.TabPanel({
                    applyTo: 'properties-tabs',
                    autoTabs:true,
                    activeTab:0,
                    deferredRender:false,
                    border:false
                }),

                buttons: [{
                    text:'Toepassen',
                    handler: function() {
                      properties.submit();
                      win.hide();
                    }
                },{
                    text: 'Sluiten',
                    handler: function(){
                      properties.cancel();
                      win.hide();
                    }
                }],
                
                close: function()
                {
                  properties.cancel();
                  win.hide();
                }
                
            });
        }
        win.show(this);
    });
});

zeusProperties = Class.create();
zeusProperties.prototype =
{
  initialize: function (Options)
  {
    this.Options = {
      manualkeyword: '',
      url: '',
      excludesitemap: '',
      metatitle: '',
      metadescription: '',
      metakeywords: { keywords: '' },
      javascript: { javascript: '' }
    };
    
    Object.extend(this.Options, Options || {});
  },
  
  submit: function()
  {
    this.Options.url = $('meta-url').value;
    this.Options.manualkeyword = $('meta-keywords-auto').checked;
    this.Options.excludesitemap = $('meta-exclude').checked;
    this.Options.metatitle = $('meta-title').value;
    this.Options.metadescription = $('meta-description').value;
    this.Options.metakeywords.keywords = $('meta-keywords').value;
    this.Options.javascript.javascript = $('meta-javascript').value;
  },
  
  cancel: function()
  {
    $('meta-url').value = this.Options.url;
    $('meta-keywords-auto').checked = this.Options.manualkeyword;
    $('meta-exclude').checked = this.Options.excludesitemap;
    $('meta-title').value = this.Options.metatitle;
    $('meta-description').value = this.Options.metadescription;
    $('meta-keywords').value = this.Options.metakeywords.keywords;
    $('meta-javascript').value = this.Options.javascript.javascript;
  }
}


