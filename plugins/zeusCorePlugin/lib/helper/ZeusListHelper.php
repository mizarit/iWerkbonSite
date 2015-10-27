<?php

function zeus_list($config = array())
{
  ob_start();
  
  if (isset($config['fields'])) {
    $model          = sfInflector::camelize($config['model']);
    $modelpeer      = $model.'Peer';
    $modeli18npeer  = $model.'I18NPeer';
    
    $c = new Criteria;
    
    if (isset($config['order'])) {
      
      $order_parts = explode(',',$config['order']);
      foreach ($order_parts as $order_part) {
        $order_part = trim($order_part);
        $order = 'addAscendingOrderByColumn';
        if (strpos($order_part, ' ')) {
          list($field, $direction) = explode(' ', $order_part);
          $order_part = $field;
          $order = 'add'.ucfirst(strtolower($direction)).'endingOrderByColumn';
        }
        $constant = @constant($modelpeer.'::'.strtoupper($order_part));
        if (!$constant) {
          $constant = constant($modeli18npeer.'::'.strtoupper($order_part));
        }
        
        $c->$order($constant);
      }
    }
    
    if (isset($config['criteria'])) {
      foreach($config['criteria'] as $key => $value) {
        if (is_array($value)) {
          if (isset($value['or'])) {
            $c1 = $c->getNewCriterion(constant($modelpeer.'::'.strtoupper($key)), $value['value'], constant('Criteria::'.strtoupper($value['method'])));
            $c2 = $c->getNewCriterion(constant($modelpeer.'::'.strtoupper($key)), $value['or']['value'], constant('Criteria::'.strtoupper($value['or']['method'])));
            $c1->addOr($c2);
            $c->add($c1);
          }
          else {
            
            $constant = @constant($modelpeer.'::'.strtoupper($key));
            if (!$constant) {
              $constant = constant($modeli18npeer.'::'.strtoupper($key));
            }

            $c->add($constant, $value['value'], constant('Criteria::'.strtoupper($value['method'])));
          }
        }
        else {
          $c->add(constant($modelpeer.'::'.strtoupper($key)), $value);
        }
      }
    }
    
    if (isset($config['distinct'])) {
      $c->setDistinct();
    }
    if (isset($config['groupby'])) {
      foreach ($config['groupby'] as $field) {
        $c->addGroupByColumn(constant($modelpeer.'::'.strtoupper($field)));
      }
    }
    
    //echo $c->toString();
    //exit;
    if (class_exists($modeli18npeer)) {
      $objects = call_user_func_array(array($modelpeer, 'doSelectWithI18N'), array($c, sfConfig::get('sf_default_culture')));
    }
    else {
      $objects = call_user_func_array(array($modelpeer, 'doSelect'), array($c));
    }
    
    $data = array();
  	foreach ($objects as $object) {
  	  $value = array();
  	  $value[] = $object->getId();
  	  foreach ($config['fields'] as $field => $cfg) {
  	    if (!isset($cfg['type'])) {
    	    $getter = 'get'.sfInflector::camelize($field);
    	    $v = strip_tags($object->$getter());
    	    
    	    if (isset($cfg['notation'])) {
    	      $v = date($cfg['notation'], strtotime($v));
    	      if ($v == date($cfg['notation'], 0)) {
    	        $v = '';
    	      }
    	    }
    	    
    	    if (isset($cfg['boolean'])) {
    	      $v = $object->$getter() ? 'Ja' : 'Nee';
    	    }
  	    }
  	    else {
  	      use_helper($cfg['type']['helper']);
  	      $cfg['objects'] = $objects;
  	      $v = $cfg['type']['method']($object, $cfg);
  	    }
  	    $value[] = $v;
  	  }
  	  
  	  if (class_exists($modeli18npeer) && !isset($config['i18n'])) {
  	    $cultures = zeusI18N::getValidCulturesForObject($object);
  	    
  	    $culture_flags = '';
  	    foreach ($cultures as $culture => $isset) {
  	      $img = '/zeusCore/img/flags/'.substr(strtolower($culture),3,2);
  	      if (!$isset) $img .= '-void';
  	      $img .= '.png';
  	      
  	      $culture_flags .= '<img onclick="window.location.href=\''.sfContext::getInstance()->getRequest()->getParameter('module'). '/edit/id/'.$object->getId().'/culture/'.$culture.'\';return false;" src="'.$img.'" title="Bewerk de '.$culture.' uitvoering van dit item" alt="Bewerk de '.$culture.' uitvoering van dit item" style="margin: 2px;">';
  	    }
  	    
    	  $value[] = ' '.$culture_flags;
    	}
  	
  	  $data[] = $value;
  	}
  	
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
   
?>
<script type="text/javascript">

var zeusSelectedRow = null;

Ext.onReady(function() {
   
  
  function renderBoolean(val){
    return val == 1 ? 'Ja' : 'Nee';
  }
  
  Ext.state.Manager.setProvider(new Ext.state.CookieProvider());

  var zeusListData = <?php echo json_encode($data) ?>;
   
  var zeusListStore = new Ext.data.Store({
        proxy: new Ext.ux.data.PagingMemoryProxy(zeusListData),
        remoteSort:true,
        //sortInfo: {field:'pdate', direction:'DESC'},
        reader: new Ext.data.ArrayReader({
            fields: <?php
  $fields = array();
  $fields[] = array('name' => 'id');
  foreach ($config['fields'] as $field => $cfg) {
    $fields[] = array('name' => $field);
  }
  if (class_exists($modeli18npeer) && !isset($config['i18n'])) {
    $fields[] = array('name' => 'i18n');
  }
  
  echo str_replace("\"},{\"", "\"},\n      {\"", json_encode($fields));
?>
        })
    });

  zeusListStore.loadData(zeusListData);
  zeusListStore.load({params:{start:0, limit:<?php echo isset($config['items']) ? $config['items'] : 20; ?>}});

  var zeusListGrid = new Ext.grid.GridPanel({
    store: zeusListStore,
    columns: <?php
$fields = array();

foreach ($config['fields'] as $field => $cfg) {
  
  $w = isset($cfg['width']) ? $cfg['width'] : false;
  $s = isset($config['ordering']) && in_array($field, $config['ordering']) ? 'true' : 'false';
  $col = array(
    'id' => $field,
    'dataIndex' => $field,
    'sortable' => $s,
    //'width' => $w,
    'header' => $cfg['label'],
  );
  
  if ($w) {
    $col['width'] = $w;
  }
  
  if (isset($cfg['renderer']) && $cfg['renderer'] == 'boolean') {
    $col['renderer'] = 'renderBoolean';
  }
  $fields[] = $col;
}

if (class_exists($modeli18npeer) && !isset($config['i18n'])) {
  $width = count(sfConfig::get('sf_enabled_cultures'))*24;
  $fields[] = array('id' => 'i18n', 'dataIndex' => 'i18n', 'header' => '', 'width' => $width);
}

$columns = str_replace("\"},{\"", "\"},\n      {\"", json_encode($fields));

$columns = preg_replace('/"renderer"\:"(.+?)"/i', '"renderer":$1', $columns);
echo $columns; 

$first_field = array_keys($config['fields']);

zeusFavorites::register('Lijstweergave van '.$config['title'], sfContext::getInstance()->getRequest()->getParameter('module').'/index');
?>,
    stripeRows: true,
    autoExpandColumn: '<?php echo $first_field[0] ?>',
    height: <?php 
    if (isset($config['items'])) {
    $h = ceil(($config['items'] / 20) * 360);
    echo $h;
    }
    else { echo 550; }
    ?>,
    title: "<?php echo $config['title'] ?>",
    plugins: new Ext.ux.PanelResizer({
      minHeight: 100
    }),
    bbar: new Ext.PagingToolbar({
      pageSize: <?php echo isset($config['items']) ? $config['items'] : 20; ?>,
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
        
        window.location.href = '<?php echo sfContext::getInstance()->getRequest()->getParameter('module') ?>/edit/id/' + record.data.id;
      }
    }
  });
    
  zeusListGrid.render('zeusList');
  
  zeusListGrid.getSelectionModel().on('selectionchange', function(sm){
    if ($('delete-btn')) {
      $('delete-btn').removeClassName('zeus-button-disabled');
    }
    
    if ($('edit-btn')) {
      $('edit-btn').removeClassName('zeus-button-disabled');
    }
  });

  
});
</script>
<?php
	  echo '<div id="zeusList"></div>';
  }
  
  if (isset($config['callback'])) {
    use_helper($config['callback']['helper']);
    echo $config['callback']['method']($config);
  }
  
	$label = 'Nieuw '.$config['name'];
	if (isset($config['labels']) && isset($config['labels']['create'])) {
	  $label = $config['labels']['create'];
	}
	
	$buttons['delete'] = new zeusRibbonButtonDelete(array('id' => 'delete-btn', 'label' =>  'Verwijderen', 'disabled' => true,  'path' => sfContext::getInstance()->getRequest()->getParameter('module').'/delete'));
	$buttons['edit'] = new zeusRibbonButtonEdit(array('id' => 'edit-btn', 'label' =>  'Bewerken', 'disabled' => true,  'path' => sfContext::getInstance()->getRequest()->getParameter('module').'/edit'));
	$buttons['create'] = new zeusRibbonButtonCreate(array('label' => $label, 'path' => sfContext::getInstance()->getRequest()->getParameter('module').'/create'));
	if (isset($config['buttons'])) {
	  foreach ($config['buttons'] as $button => $cfg) {
	    if ($cfg == 'disable') {
	      if (isset($buttons[$button])) unset($buttons[$button]);
	    }
	    
	    if (is_array($cfg)) {
	      if (isset($cfg['helper'])) {
	        use_helper($cfg['helper']);
	        $buttons[$button] = $cfg['method']($cfg);
	        
	        if (isset($cfg['toolbar'])) {
	          $buttons[$button] = array('button' => $cfg['method']($cfg), 'toolbar' => $cfg['toolbar']);
	        }
	      }
	    }
	  }
	}
	
	//zeusRibbon::addButton(new zeusRibbonButtonSmall(array('label' => 'Geschiedenis', 'id' => 'history-btn', 'disabled' => false, 'icon' => 'history')), 'Eigenschappen');
	//zeusRibbon::addButton(new zeusRibbonButtonSmall(array('label' => 'Rechten', 'id' => 'permissions-btn', 'disabled' => false, 'icon' => 'locked')), 'Eigenschappen');
	//zeusRibbon::addButton(new zeusRibbonButtonSmall(array('label' => 'Eigenschappen', 'id' => 'properties-btn', 'disabled' => true, 'icon' => 'advanced')), 'Eigenschappen');
	 
	
	foreach ($buttons as $button) {
	  if (is_array($button)) {
	    zeusRibbon::addButton($button['button'], $button['toolbar']);
	  }
	  else {
	    zeusRibbon::addButton($button);
	  }
	}
	
	if (isset($config['helper'])) {
	  use_helper($config['helper']['helper']);
	  echo $config['helper']['method']($config);
	}
	
	echo '<div id="list-loader"></div>';
	
	if (isset($model)) {
  	use_helper('ZeusHistory');
  	$history_html = zeus_history($model);
  	echo $history_html;
	}
	
	return ob_get_clean();
}