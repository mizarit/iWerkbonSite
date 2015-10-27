<?php

function zeus_favorites($object = null, $config = array())
{
  $response = sfContext::getInstance()->getResponse();
  $response->addStylesheet('/zeusCore/js/extjs/resources/css/ext-all.css');
	$response->addStylesheet('/zeusCore/js/extjs/resources/css/xtheme-slate.css');
	$response->addJavascript('/zeusCore/js/extjs/adapter/ext/ext-base.js');
	$response->addJavascript('/zeusCore/js/extjs/ext-all.js');
	$response->addJavascript('/zeusCore/js/extjs/src/locale/ext-lang-nl.js');
	$response->addJavascript('/zeusCore/js/extjs/examples/ux/SlidingPager.js');
	$response->addJavascript('/zeusCore/js/extjs/examples/ux/SliderTip.js');
	$response->addJavascript('/zeusCore/js/extjs/examples/ux/PanelResizer.js');
	$response->addJavascript('/zeusCore/js/extjs/examples/ux/PagingMemoryProxy.js');
  $response->addJavascript('/zeusCore/js/zeus-favorites/zeus-favorites.js');
  
  $c = new Criteria;
  $c->addAscendingOrderByColumn(FavoritePeer::TITLE);
  $favorites = FavoritePeer::doSelect($c);
  $panel1 = array();
  foreach ($favorites as $favorite) {
    $panel1[] = array(
      $favorite->getTitle(),
      "<img onclick=\"window.location.href='".$favorite->getActionurl()."';\" src=\"/zeusCore/img/icons/famfamfam/application_go.png\" alt=\"Openen\" title=\"Openen\">"
    );
  }
  
  ob_start();
  ?>
  <div id="favorites-win" class="x-hidden">
    <div class="x-window-header">Favorieten</div>
    <div id="favorites-tabs">
      <div class="x-tab" title="Mijn favoriete acties">
        <div id="zeusFavoriteList1"></div>
      </div>
    </div>
  </div>
  
  <div id="favorites-add-win" class="x-hidden">
    <div class="x-window-header">Favoriet toevoegen</div>
    <div id="favorites-add-tabs">
      <div class="x-tab" title="Toevoegen van actie aan favorieten">
          <div id="favorites-add-inner"></div>  
      </div>
    </div>
  </div>
<script type="text/javascript">
Ext.onReady(function() {
  
  Ext.state.Manager.setProvider(new Ext.state.CookieProvider());
  
  var zeusFavoriteListData1 = <?php echo json_encode($panel1); ?>;
  
  var zeusFavoriteListStore1 = new Ext.data.Store({
        proxy: new Ext.ux.data.PagingMemoryProxy(zeusFavoriteListData1),
        remoteSort:true,
        //sortInfo: {field:'pdate', direction:'DESC'},
        reader: new Ext.data.ArrayReader({
            fields: [
      {"name":"title"},
      {"name":"actions"}
      ]        })
    });
    
  zeusFavoriteListStore1.loadData(zeusFavoriteListData1);
  zeusFavoriteListStore1.load({params:{start:0, limit:15}});
  
  var zeusFavoriteListGrid1 = new Ext.grid.GridPanel({
    store: zeusFavoriteListStore1,
    columns: [
      {"id":"title","dataIndex":"title","header":"Titel"},
      {"id":"actions","dataIndex":"actions","header":"","width":44}
    ],
    stripeRows: true,
    autoExpandColumn: 'title',
    height: 410,
    width: 764,
    title: "Favorieten",
    plugins: new Ext.ux.PanelResizer({
      minHeight: 100
    }),
    bbar: new Ext.PagingToolbar({
      pageSize: 15,
      store: zeusFavoriteListStore1,
      displayInfo: true,
      plugins: new Ext.ux.SlidingPager()
    }),

    listeners: {
      rowclick: function(grid, rowIndex, colIndex) {
        var selectionModel = grid.getSelectionModel();
        var record = selectionModel.getSelected();
        zeusSelectedRow = record.data.id;
      },
      rowdblclick: function(grid, rowIndex, colIndex) {
        var selectionModel = grid.getSelectionModel();
        var record = selectionModel.getSelected();
        
        window.location.href = 'pageadmin/edit/id/' + record.data.id;
      }
    }
  });
  
  zeusFavoriteListGrid1.render('zeusFavoriteList1');
});
</script>
  <?php
  
  return ob_get_clean();
}