var winFavorites;
var winFavoritesAdd;
var tpa;
var tpb;

Ext.onReady(function(){

  if (!tpa) {
    tpa = new Ext.TabPanel({
        applyTo: 'favorites-tabs',
        autoTabs:true,
        activeTab:0,
        deferredRender:false,
        border:false
    });
  }

  if (!tpb) {
    tpb = new Ext.TabPanel({
        applyTo: 'favorites-add-tabs',
        autoTabs:true,
        activeTab:0,
        deferredRender:false,
        border:false
    });
  }   
   
  var button = Ext.get('favorites-btn');

  if (!button) return;
  
  button.on('click', function(){
      // create the window on the first click and reuse on subsequent clicks
      if(!winFavorites){
          winFavorites = new Ext.Window({
              applyTo:'favorites-win',
              layout:'fit',
              width:800,
              height:530,
              closeAction:'close',
              plain: true,
              items: tpa,

              buttons: [{
                  text: 'Sluiten',
                  handler: function(){
                    zfavorites.cancel();
                    winFavorites.hide();
                  }
              }],
              
              close: function()
              {
                zfavorites.cancel();
                winFavorites.hide();
              }
              
          });
      }
      winFavorites.show(this);
  });
  
  var button2 = Ext.get('favorites-add-btn');

  if (!$('favorites-add-btn').hasClassName('disabled')) {
    button2.on('click', function(){
        // create the window on the first click and reuse on subsequent clicks
        if(!winFavoritesAdd){
            winFavoritesAdd = new Ext.Window({
                applyTo:'favorites-add-win',
                layout:'fit',
                width:500,
                height:230,
                closeAction:'close',
                plain: true,
                items: tpb,
  
                buttons: [{
                    text: 'Opslaan',
                    handler: function(){
                      zfavorites.add();
                      winFavoritesAdd.hide();
                    }
                },
                {
                    text: 'Sluiten',
                    handler: function(){
                      winFavoritesAdd.hide();
                    }
                }],
                
                close: function()
                {
                  winFavoritesAdd.hide();
                }
                
            });
        }
        winFavoritesAdd.show(this);
    });
  }
    
    

});

zeusFavorites = Class.create();
zeusFavorites.prototype =
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
  },
  
  add: function()
  {
    for (var i = 1; i <= favs; i++) {
      if ($('add-fav-'+i).checked) {
        new Ajax.Request('/core/addFavorite', {
          parameters: {
            title: $('add-fav-label-'+i).innerHTML,
            actionurl: $('add-fav-'+i).value
          },
          onSuccess: function(t) {
            alert(t.responseText);
          }
        });
      }
    }
  }
}

var zfavorites = new zeusFavorites({ });

