<?php

function zeus_history($object = null)
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
	
	$response->addJavascript('/zeusCore/js/zeus-history/zeus-history.js');
	
  if (is_object($object)) {
    $object_name = get_class($object);
    if ($object_name == 'sfOutputEscaperObjectDecorator') {
      $object_name = get_class($object->getRawValue());
    }
  }
  else {
    $object_name = $object;
  }
  
  ob_start();
  ?>
   <div id="history-win" class="x-hidden">
    <div class="x-window-header">Geschiedenis</div>
    <div id="history-tabs">
      <?php if ($object && is_object($object)) { ?>
      <div class="x-tab" title="Huidig object">
        <div id="zeusHistoryList1"></div>
      </div>
      <?php } ?>
      <div class="x-tab" title="Verwijderde objecten">
        <div id="zeusHistoryList2"></div>
      </div>
      <div class="x-tab" title="Alle objecten">
        <div id="zeusHistoryList3"></div>
      </div>
      
      <div class="x-tab" title="Vergelijken" style="overflow:scroll; height: 410px;" id="diff-container">
        <p><strong>Geen versie geselecteerd om te vergelijken.</strong></p>
        <p>Kies een versie uit de overzichten op de overige tabbladen om deze te vergelijken met de huidige versie.</p>
      </div>
    </div>
  </div>
  <script type="text/javascript">
  var zhistory = new zeusHistory({});
  
  <?php

$panel1 = array();
$panel2 = array();
$panel3 = array();

$c = new Criteria;

if ($object && is_object($object)) {

  $object_id = $object->getId();
  
  $c->add(VersionPeer::OBJECT, $object_name);
  $c->add(VersionPeer::OBJECT_ID, $object_id);
  $c->add(VersionPeer::CULTURE, 'base');
  $c->addDescendingOrderByColumn(VersionPeer::CREATED_AT);
  $versions = VersionPeer::doSelect($c);
  foreach ($versions as $version) { 
    $panel1[] = array(
      $version->getVersion(),
      $version->getTitle(),
      $version->getMutationStr(),
      date('d-m-Y H:i:s', strtotime($version->getCreatedAt())),
      $version->getCreatedByStr(),
      '<img onclick="loadVersion('.$version->getId().');" src="/zeusCore/img/icons/famfamfam/magnifier.png" alt="Bekijken" title="Bekijken"><img onclick="restoreVersion('.$version->getId().');" src="/zeusCore/img/icons/famfamfam/arrow_undo.png" alt="Versie herstellen" title="Versie herstellen">'
    );
  } 
}

$c->clear();
$c->add(VersionPeer::OBJECT, $object_name);
$c->add(VersionPeer::MUTATION, 'delete');
$c->add(VersionPeer::CULTURE, 'base');
$c->addDescendingOrderByColumn(VersionPeer::CREATED_AT);
$versions = VersionPeer::doSelect($c);
foreach ($versions as $version) { 
  $panel2[] = array(
    $version->getVersion(),
    $version->getTitle(),
    $version->getMutationStr(),
    date('d-m-Y H:i:s', strtotime($version->getCreatedAt())),
    $version->getCreatedByStr(),
    '<img onclick="loadVersion('.$version->getId().');" src="/zeusCore/img/icons/famfamfam/magnifier.png" alt="Bekijken" title="Bekijken"><img onclick="restoreVersion('.$version->getId().');" src="/zeusCore/img/icons/famfamfam/arrow_undo.png" alt="Versie herstellen" title="Versie herstellen">'
  );
} 

$c->clear();
$c->add(VersionPeer::OBJECT, $object_name);
$c->add(VersionPeer::CULTURE, 'base');
$c->addDescendingOrderByColumn(VersionPeer::CREATED_AT);
$versions = VersionPeer::doSelect($c);
foreach ($versions as $version) { 
  $panel3[] = array(
    $version->getVersion(),
    $version->getTitle(),
    $version->getMutationStr(),
    date('d-m-Y H:i:s', strtotime($version->getCreatedAt())),
    $version->getCreatedByStr(),
    '<img onclick="loadVersion('.$version->getId().');" src="/zeusCore/img/icons/famfamfam/magnifier.png" alt="Bekijken" title="Bekijken"><img onclick="restoreVersion('.$version->getId().');" src="/zeusCore/img/icons/famfamfam/arrow_undo.png" alt="Versie herstellen" title="Versie herstellen">'
  );
}
?>
  
  
function restoreVersion(version_id)
{
  if (confirm('Weet je zeker dat je deze versie wilt herstellen?')) {
    new Ajax.Request('<?php echo url_for('historyadmin/revert'); ?>', {
    parameters: { 
      version: version_id,
      mmodule: '<?php echo sfContext::getInstance()->getRequest()->getParameter('module'); ?>'
    },
    onSuccess: function(t)
    {
      alert(t.responseText);
      window.location.href = window.location.href + '?reload=true';
    }
  });
  }
}

function loadVersion(version_id)
{
  $('diff-container').innerHTML = '<p style="margin-top:50px;text-align:center;"><img src="/zeusCore/img/ajax-loader.gif" alt=""><\/p>';
  new Ajax.Updater('diff-container', '<?php echo url_for('historyadmin/diff'); ?>', {
    parameters: { 
      version: version_id,
      mmodule: '<?php echo sfContext::getInstance()->getRequest()->getParameter('module'); ?>'
    }
  });
  
  tp.setActiveTab(<?php echo ($object && is_object($object)) ? 3 : 2; ?>);
}

Ext.onReady(function() {
   
  Ext.state.Manager.setProvider(new Ext.state.CookieProvider());
  
  var zeusHistoryListData1 = <?php echo json_encode($panel1); ?>;
   
  var zeusHistoryListStore1 = new Ext.data.Store({
        proxy: new Ext.ux.data.PagingMemoryProxy(zeusHistoryListData1),
        remoteSort:true,
        //sortInfo: {field:'pdate', direction:'DESC'},
        reader: new Ext.data.ArrayReader({
            fields: [
      {"name":"version"},
      {"name":"title"},
      {"name":"mutation"},
      {"name":"mutationdate"},
      {"name":"mutationuser"},
      {"name":"actions"}
      ]        })
    });
    
  zeusHistoryListStore1.loadData(zeusHistoryListData1);
  zeusHistoryListStore1.load({params:{start:0, limit:15}});
    
  var zeusHistoryListGrid1 = new Ext.grid.GridPanel({
    store: zeusHistoryListStore1,
    columns: [
      {"id":"version","dataIndex":"version","width":45,"header":"Versie"},
      {"id":"title","dataIndex":"title","header":"Titel"},
      {"id":"mutation","dataIndex":"mutation","width":75,"header":"Mutatie"},
      {"id":"mutationdate","dataIndex":"mutationdate","width":110,"header":"Datum"},
      {"id":"mutationuser","dataIndex":"mutationuser","width":110,"header":"Gebruiker"},
      {"id":"actions","dataIndex":"actions","header":"","width":44}
    ],
    stripeRows: true,
    autoExpandColumn: 'title',

    height: 410,
    width: 764,
    title: "Mutaties",
    plugins: new Ext.ux.PanelResizer({
      minHeight: 100
    }),
    bbar: new Ext.PagingToolbar({
      pageSize: 15,
      store: zeusHistoryListStore1,
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
  
  var zeusHistoryListData2 = <?php echo json_encode($panel2); ?>;
  
  var zeusHistoryListStore2 = new Ext.data.Store({
      proxy: new Ext.ux.data.PagingMemoryProxy(zeusHistoryListData2),
      remoteSort:true,
      //sortInfo: {field:'pdate', direction:'DESC'},
      reader: new Ext.data.ArrayReader({
          fields: [
    {"name":"version"},
    {"name":"title"},
    {"name":"mutation"},
    {"name":"mutationdate"},
    {"name":"mutationuser"},
    {"name":"actions"}
    ]        })
  });
    
  zeusHistoryListStore2.loadData(zeusHistoryListData2);
  zeusHistoryListStore2.load({params:{start:0, limit:15}});
    
  var zeusHistoryListGrid2 = new Ext.grid.GridPanel({
    store: zeusHistoryListStore2,
    columns: [
      {"id":"version","dataIndex":"version","width":45,"header":"Versie"},
      {"id":"title","dataIndex":"title","header":"Titel"},
      {"id":"mutation","dataIndex":"mutation","width":75,"header":"Mutatie"},
      {"id":"mutationdate","dataIndex":"mutationdate","width":110,"header":"Datum"},
      {"id":"mutationuser","dataIndex":"mutationuser","width":110,"header":"Gebruiker"},
      {"id":"actions","dataIndex":"actions","header":"","width":44}
    ],
    stripeRows: true,
    autoExpandColumn: 'title',
    height: 410,
    width: 764,
    title: "Mutaties",
    plugins: new Ext.ux.PanelResizer({
      minHeight: 100
    }),
    bbar: new Ext.PagingToolbar({
      pageSize: 15,
      store: zeusHistoryListStore2,
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
  
  var zeusHistoryListData3 = <?php echo json_encode($panel3); ?>;
  var zeusHistoryListStore3 = new Ext.data.Store({
      proxy: new Ext.ux.data.PagingMemoryProxy(zeusHistoryListData3),
      remoteSort:true,
      //sortInfo: {field:'pdate', direction:'DESC'},
      reader: new Ext.data.ArrayReader({
          fields: [
    {"name":"version"},
    {"name":"title"},
    {"name":"mutation"},
    {"name":"mutationdate"},
    {"name":"mutationuser"},
    {"name":"actions"}
    ]        })
  });
  
  zeusHistoryListStore3.loadData(zeusHistoryListData3);
  zeusHistoryListStore3.load({params:{start:0, limit:15}});
  
  var zeusHistoryListGrid3 = new Ext.grid.GridPanel({
    store: zeusHistoryListStore3,
    columns: [
      {"id":"version","dataIndex":"version","width":45,"header":"Versie"},
      {"id":"title","dataIndex":"title","header":"Titel"},
      {"id":"mutation","dataIndex":"mutation","width":75,"header":"Mutatie"},
      {"id":"mutationdate","dataIndex":"mutationdate","width":110,"header":"Datum"},
      {"id":"mutationuser","dataIndex":"mutationuser","width":110,"header":"Gebruiker"},
      {"id":"actions","dataIndex":"actions","header":"","width":44}
    ],
    stripeRows: true,
    autoExpandColumn: 'title',
    height: 410,
    width: 764,
    title: "Mutaties",
    plugins: new Ext.ux.PanelResizer({
      minHeight: 100
    }),
    bbar: new Ext.PagingToolbar({
      pageSize: 15,
      store: zeusHistoryListStore3,
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
  
<?php if ($object && is_object($object)) { ?>
  zeusHistoryListGrid1.render('zeusHistoryList1');
<?php } ?>
  zeusHistoryListGrid2.render('zeusHistoryList2');
  zeusHistoryListGrid3.render('zeusHistoryList3');
});
  </script>
  <?php
  
  return ob_get_clean();
}