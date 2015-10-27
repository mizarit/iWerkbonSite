<div id="zeusList"></div>
<script type="text/javascript">

var zeusSelectedRow = null;

Ext.onReady(function() {
  
  Ext.state.Manager.setProvider(new Ext.state.CookieProvider());
  var zeusListData = <?php echo json_encode($data->getRawValue()) ?>;
   
  var zeusListStore = new Ext.data.Store({
        proxy: new Ext.ux.data.PagingMemoryProxy(zeusListData),
        remoteSort:true,
        //sortInfo: {field:'pdate', direction:'DESC'},
        reader: new Ext.data.ArrayReader({
            fields: <?php
  $fields = array();
  $fields[] = array('name' => 'id');
  $fields[] = array('name' => 'feed');
  
  
  echo str_replace("\"},{\"", "\"},\n      {\"", json_encode($fields));
?>
        })
    });

  zeusListStore.loadData(zeusListData);
  zeusListStore.load({params:{start:0, limit:20}});

  var zeusListGrid = new Ext.grid.GridPanel({
    store: zeusListStore,
    columns: <?php
$fields = array();

$config['fields'] = array(
  'feed' => 'Feed URL'
);
foreach ($config['fields'] as $field => $label) {
  
  $col = array(
    'id' => $field,
    'dataIndex' => $field,
    'sortable' => 'false',
    'width' => 350,
    'header' => $label,
  );
  

  $fields[] = $col;
}



$columns = str_replace("\"},{\"", "\"},\n      {\"", json_encode($fields));

$columns = preg_replace('/"renderer"\:"(.+?)"/i', '"renderer":$1', $columns);
echo $columns; 

?>,
    stripeRows: true,
    autoExpandColumn: 'feed',
    height: 550,
    title: "Nieuwsfeeds",
    plugins: new Ext.ux.PanelResizer({
      minHeight: 100
    }),
    bbar: new Ext.PagingToolbar({
      pageSize: 20,
      store: zeusListStore,
      displayInfo: true,
      plugins: new Ext.ux.SlidingPager()
    }),


    listeners: {
      rowclick: function(grid, rowIndex, colIndex) {
        var selectionModel = grid.getSelectionModel();
        var record = selectionModel.getSelected();
        zeusSelectedRow = record.data.id;
        
        $('delete-feed-btn').removeClassName('zeus-button-disabled');
      },
      rowdblclick: function(grid, rowIndex, colIndex) {
        var selectionModel = grid.getSelectionModel();
        var record = selectionModel.getSelected();
        
        window.location.href = '<?php echo url_for('newsadmin/import'); ?>/id/' + record.data.id;
      }
    }
  });
    
  zeusListGrid.render('zeusList');
  
});

function deleteFeed()
{
  if(!$('delete-feed-btn').hasClassName('zeus-button-disabled')) {
    if (confirm('Weet je zeker dat je deze nieuwsfeed wilt verwijderen?')) {
      window.location.href = '<?php echo url_for('newsadmin/deletefeed'); ?>/id/' + zeusSelectedRow;
    }
  }
}
</script>
<?php 
zeusRibbon::addButton(new zeusRibbonButtonSmall(array('label' => 'Geschiedenis', 'id' => 'history-btn', 'disabled' => false, 'icon' => 'history')), 'Eigenschappen');
zeusRibbon::addButton(new zeusRibbonButtonSmall(array('label' => 'Rechten', 'id' => 'permissions-btn', 'disabled' => false, 'icon' => 'locked')), 'Eigenschappen');
zeusRibbon::addButton(new zeusRibbonButtonSmall(array('label' => 'Eigenschappen', 'id' => 'properties-btn', 'disabled' => true, 'icon' => 'advanced')), 'Eigenschappen');

//sfContext::getInstance()->getConfiguration()->loadHelpers('Url');

zeusRibbon::addButton(new zeusRibbonButton(array(
'label' => 'Terug naar lijst', 
'icon'  => 'previous',
'type'  => 'large', 
'id'    => 'import-news-btn',
'callback' => "window.location.href='".url_for('newsadmin/index')."'"
  )), 'Acties');
  
  zeusRibbon::addButton(new zeusRibbonButton(array(
'label' => 'Verwijderen', 
'icon'  => 'fileclose',
'type'  => 'large', 
'disabled' => true,
'id'    => 'delete-feed-btn',
'callback' => "deleteFeed();"
  )), 'Acties');
  
  
zeusRibbon::addButton(new zeusRibbonButton(array(
'label' => 'Nieuwe feed', 
'icon'  => 'new_window',
'type'  => 'large', 
'id'    => 'new-feed-btn',
'callback' => "window.location.href='".url_for('newsadmin/createfeed')."'"
  )), 'Acties');
  
include_component('core', 'helpers');