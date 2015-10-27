<form action="<?php echo url_for('newsadmin/import'); ?>" method="post" id="zeus-2">
  <fieldset>
    <legend>Import form</legend>
    <input type="hidden" name="id" id="id" value="<?php echo $sf_params->get('id'); ?>">
    <div id="zeusList"></div>
  </fieldset>
</form>
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
  $fields[] = array('name' => 'import');
  $fields[] = array('name' => 'title');
  $fields[] = array('name' => 'description');
  $fields[] = array('name' => 'link');
  $fields[] = array('name' => 'pubdate');
  
  
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
  'import' => '',
  'title' => 'Titel',
  'description' => 'Samenvatting',
  'link' => 'Link naar bericht'
);
foreach ($config['fields'] as $field => $label) {
  
  $col = array(
    'id' => $field,
    'dataIndex' => $field,
    'sortable' => $field == 'import' ? 'false' : 'true',
    'width' => $field == 'description' ? 700 : $field == 'import' ? 25 : 350,
    'header' => $label,
  );
  

  $fields[] = $col;
}



$columns = str_replace("\"},{\"", "\"},\n      {\"", json_encode($fields));

$columns = preg_replace('/"renderer"\:"(.+?)"/i', '"renderer":$1', $columns);
echo $columns; 

?>,
    stripeRows: true,
    autoExpandColumn: 'description',
    height: 550,
    title: "Nieuwsberichten",
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
      },
      rowdblclick: function(grid, rowIndex, colIndex) {
        var selectionModel = grid.getSelectionModel();
        var record = selectionModel.getSelected();
        
        alert('Selecteer dit bericht links en druk hierna op Importeren om dit bericht toe te voegen.');
      }
    }
  });
    
  zeusListGrid.render('zeusList');
  
});
</script>
<?php 
zeusRibbon::addButton(new zeusRibbonButtonSmall(array('label' => 'Geschiedenis', 'id' => 'history-btn', 'disabled' => false, 'icon' => 'history')), 'Eigenschappen');
zeusRibbon::addButton(new zeusRibbonButtonSmall(array('label' => 'Rechten', 'id' => 'permissions-btn', 'disabled' => false, 'icon' => 'locked')), 'Eigenschappen');
zeusRibbon::addButton(new zeusRibbonButtonSmall(array('label' => 'Eigenschappen', 'id' => 'properties-btn', 'disabled' => true, 'icon' => 'advanced')), 'Eigenschappen');

zeusRibbon::addButton(new zeusRibbonButtonBack(array('path' => sfContext::getInstance()->getRequest()->getParameter('module').'/importfeeds')), 'Acties');

zeusRibbon::addButton(new zeusRibbonButton(array(
  'label' => 'Importeren', 
  'icon'  => 'fileimport',
  'type'  => 'large', 
  'id'    => 'import-news-btn',
  'callback' => "$('zeus-2').submit();"
)), 'Acties');
	
include_component('core', 'helpers'); ?>