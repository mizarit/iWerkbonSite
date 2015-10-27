<?php

class zeusActions extends sfActions
{
  protected $errors = array();
  protected $cobject = null;
  
  public function preExecute()
  {
    // check if custom template exists
    $files = glob(sfConfig::get('sf_plugins_dir').'/*/modules/'.$this->getRequestParameter('module').'/templates/'.$this->getRequestParameter('action').'Success.php');
    if (!$files) {
      $files = glob(sfConfig::get('sf_app_dir').'/modules/'.$this->getRequestParameter('module').'/templates/'.$this->getRequestParameter('action').'Success.php'); 
      if (!$files) {
        $this->setTemplate(sfConfig::get('sf_plugins_dir').'/zeusCorePlugin/templates/'.$this->getRequestParameter('action'));
      }
    }
    
    $config = zeusYaml::load('admin.yml');
    if (is_array($config) && isset($config['title'])) {
      sfContext::getInstance()->getResponse()->setTitle($config['title']);
    }
  }
  
  public function executeIndex(sfWebRequest $request)
  {
  }
  
  public function executeListproxy(sfWebRequest $request)
  {

  }
  
  public function executeCreate(sfWebRequest $request)
  {
    $config = zeusYaml::load('admin.yml');

    if (isset($config['wizard'])) {
      $this->redirect($this->getRequestParameter('module').'/wizard');
    }
    
    $this->redirect($this->getRequestParameter('module').'/edit');
  }
  
  public function executeUpdate(sfWebRequest $request, $customConfig = false)
  {
    $new = false;
    if (!$this->hasRequestParameter('id')) {
      $object = new $this->model;
      $new = true;
    }
    else {
      $object = call_user_func_array(array($this->model.'Peer', 'retrieveByPk'), array($this->getRequestParameter('id')));
    }
    
    if (!$object) {
      $object = new $this->model;
      $new = true;
    } 
    
    $this->cobject = $object;
    
    $i18n_values = array();
    
    if ($this->hasRequestParameter('i18n_culture')) {
      // i18n save, we need some repairs here
      $i18n_data = json_decode($this->getRequestParameter('i18n_values'));
      
      foreach ($i18n_data as $field => $values) {
        foreach ($values as $culture => $value) {
          $i18n_values[(string)$field][(string)$culture] = $value;
        }
        
        // load last values from post into correct culture
        $i18n_values[(string)$field][$this->getRequestParameter('i18n_culture')] = $this->getRequestParameter((string)$field);
      }
      
      // handle culture deletions
      $count_per_culture = array();
      foreach ($i18n_values as $field => $values) {
        foreach ($values as $culture => $value) {
          if ($value != '') {
            if (!isset($count_per_culture[$culture])) $count_per_culture[$culture] = 0;
            $count_per_culture[$culture]++;
          }
        }
      }
      
      $delete_cultures = array();
      
      foreach (sfConfig::get('sf_enabled_cultures') as $culture) {
        if (!isset($count_per_culture[$culture])) {
          $delete_cultures[] = $culture;
        }
      }
      
      if (count($delete_cultures) > 0) {
        $c = new Criteria;
        $model_i18n_peer = $this->model.'I18NPeer';
        $c->add(constant($model_i18n_peer.'::CULTURE'), $delete_cultures, Criteria::IN);
        $c->add(constant($model_i18n_peer.'::ID'), $object->getId());
        call_user_func_array(array($model_i18n_peer, 'doDelete'), array($c));
      }
    }
     
    $config = $customConfig ? $customConfig : zeusYaml::load('admin.yml');

    if (isset($config['edit'])) {
      
      foreach ($config['edit']['fields'] as $field => $cfg)
      {
        $value = $this->getRequestParameter($field);

        if (isset($cfg['required'])) {
          if (($cfg['type'] == 'checkbox' && !$this->hasRequestParameter($field)) || $value == '') {
            $this->errors[$field] = $cfg['label'].' is een verplicht veld.';
          }
        }

        if (isset($cfg['validate'])) {
          $this->validateField($cfg['validate'], $value, $cfg, $field);
        }
        
        
        switch ($cfg['type']) {
          case 'date':
            $numericalTime = @strtotime($value);
            if ($numericalTime === false)
            {
              $value = date('Y-m-d');
            }
            break;
            
          case 'price':
            // convert human readable to valid float
            $value = str_replace(',', 'xx', $value);
            $value = str_replace('.', ',', $value);
            $value = str_replace('xx', '.', $value);
            break;
            
          case 'file':
            // move file to appropiate place
            if (!is_dir(sfConfig::get('sf_web_dir').'/docs')) {
              @mkdir(sfConfig::get('sf_web_dir').'/docs', 0777);
            }
            if (!is_dir(sfConfig::get('sf_web_dir').'/docs/'.$this->model)) {
              @mkdir(sfConfig::get('sf_web_dir').'/docs'.$this->model);
            }
            
            $file_src = sfConfig::get('sf_upload_dir').'/'.$value;
            $file_target = sfConfig::get('sf_web_dir').'/docs/'.$this->model.'/'.$value;
            
            $getter = 'get'.ucfirst($field);
            $old = $object->$getter();
              
            if ($value == '') {
              $file_target = sfConfig::get('sf_web_dir').'/docs/'.$this->model.'/'.$old;
              if (file_exists($file_target)) {
                @unlink($file_target);
              }
            }
            elseif (file_exists($file_src)) {
              // remove old file if available
              $file_old = sfConfig::get('sf_web_dir').'/docs/'.$this->model.'/'.$old;
              if (file_exists($file_old) && $old != '') {
                @unlink($file_old);
              }
              
              // remove if same file already exists ( perhaps changed? )
              if (file_exists($file_target)) {
                @unlink($file_target);
              }
              
              // move the file from upload to the model doc dir
              @rename($file_src, $file_target);
              
              $value = '/docs/'.$this->model.'/'.$value;
            }
            elseif ($value != '') {
              // save old value again, no new file and it was not deleted
              $value = $old;
            }
            break;
            
          case 'link1ton':
            
            $c = new Criteria;
            $thismodel = get_class($object);
            $model = ucfirst($cfg['table']);
            $modelpeer = $model . 'Peer';
            $link = $thismodel.$model;
            $linkpeer = $link.'Peer';
            
            $c->add(constant($linkpeer.'::'.strtoupper($thismodel).'_ID'), $object->getId());
    
            $s1 = 'set'.$thismodel.'Id';
            $s2 = 'set'.$model.'Id';
            call_user_func_array(array($linkpeer, 'doDelete'), array($c));
  
            foreach ($_POST as $k => $v) {
              if (strpos($k, '-')) {
                list($kk, $vv) = explode('-', $k);
                if ($kk == $field) {
                  $linko = new $link;
                  $linko->$s1($object->getId());
                  $linko->$s2($vv);
                  $linko->save();
                }
              }
            }
            break;
            
        }
        
        $setter = 'set'.ucfirst($field);
        if (method_exists($object, $setter)) {
          
          if (isset($i18n_values[$field])) {
            foreach ($i18n_values[$field] as $culture => $value) {

              if (!in_array($culture, $delete_cultures)) {
                $object->setCulture($culture);
                $object->$setter($value);
                $object->save();
              }
            }
          }
          else {
            $object->$setter($value);
          }
        }
      }

      if(method_exists($this, 'preValidate')) {
        $this->preValidate($object);
      }
      
      if (count($this->errors) > 0) {
        $this->getRequest()->setParameter('id', $object->getId());
        $this->getRequest()->setParameter('action', 'edit');
        $this->getRequest()->setParameter('errors', $this->errors);
        $this->preExecute();
        return $this->executeEdit($request);
      }
      else {
        
        if(method_exists($this, 'preSave')) {
          $this->preSave($object);
        }
        
        $object->save();
        
        if(method_exists($this, 'postSave')) {
          $this->postSave($object);
        }
      
      
        sfContext::getInstance()->getConfiguration()->loadHelpers('ZeusRoute');
        $route = route_for($object);
  
        // check if properties are set
        if ($this->hasRequestParameter('meta-title')) {
          $c = new Criteria;
          $c->add(PropertiesPeer::OBJECT, get_class($object).':'.$object->getId());
          $properties = PropertiesPeer::doSelectOne($c);
          if (!$properties) {
            $properties = new Properties;
            $properties->setObject(get_class($object).':'.$object->getId());
          }
          
          $properties->setMetaTitle($this->getRequestParameter('meta-title'));
          $properties->setMetaKeywords($this->getRequestParameter('meta-keywords'));
          $properties->setMetaDescription($this->getRequestParameter('meta-description'));
          $properties->setJavascript($this->getRequestParameter('meta-javascript'));
          
          $properties->setManualkeyword(!$this->hasRequestParameter('meta-keywords-auto') ? 1 : 0);
          $properties->setExcludesitemap($this->hasRequestParameter('meta-exclude') ? 1 : 0);
          
          $properties->save();
          
          $route = str_replace('/frontend_dev.php', '', $route);
          
          $new_route = $this->getRequestParameter('meta-url');
          if ($route != $new_route) {
            // update url, but is it unique?
            $c->clear();
            $c->add(RoutePeer::URL, $new_route);
            $test = RoutePeer::doSelectOne($c);
            if (!$test) {
              $c->clear();
              $c->add(RoutePeer::OBJECT, get_class($object));
              $c->add(RoutePeer::OBJECT_ID, $object->getId());
              $route = RoutePeer::doSelectOne($c);
              $new_url = str_replace('/nl_NL', '', $new_route);
              if ($new_url != '') {
                $route->setUrl($new_url);
                $route->save();
              }
            }
          }
        }
        if (isset($config['list'])) {
          sfContext::getInstance()->getConfiguration()->loadHelpers('Url');
          if ($new) {
            $str = isset($config['list']['labels']['create']) ? strtolower($config['list']['labels']['create']) : 'nieuwe '.$config['list']['name'];
            $change = 'Aanmaken van '.$str." '".$object->getTitle()."'";
            Changelog::add($change, 'create', $object, url_for($this->getRequestParameter('module').'/edit?id='.$object->getId()));
          }
          else {
            $change = 'Bewerken van '.$config['list']['name']." '".$object->getTitle()."'";
            Changelog::add($change, 'edit', $object, url_for($this->getRequestParameter('module').'/edit?id='.$object->getId()));
          }
        }
      }
    }
    
    $this->redirect($this->getRequestParameter('module').'/index?saved=true');
  }
  
  protected function validateField($validator, $value = '', $cfg = array(), $field)
  {
    switch ($validator) {
      case 'numeric':
        if ($value == '') return;
        if (!is_numeric($value)) {
          $this->errors[$field] = $cfg['label'].' is geen numerieke waarde.';
        }
        break;
        
      case 'min':
        if (is_array($cfg['min'])) {
          switch ($cfg['min']['type']) {
            case 'checkbox':
              if (count($this->getRequestParameter($cfg['min']['field'])) < $cfg['min']['min']) {
                if ($cfg['min']['min'] == 1) {
                  $this->errors[$cfg['min']['field']] = 'Van '.strtolower($cfg['label']).' moet er minimaal '.$cfg['min']['min'].' geselecteerd zijn.';
                }
                else {
                  $this->errors[$cfg['min']['field']] = 'Van '.strtolower($cfg['label']).' moeten er minimaal '.$cfg['min']['min'].' geselecteerd zijn.';
                }
              }
          }
        }
        elseif ($value < $cfg['min']) {
          $this->errors[$field] = $cfg['label'].' moet minimaal '.$cfg['min'].' zijn.';
        }
        break;
        
      case 'maxlength':
        if (strlen($value) > $cfg['maxlength']) {
          $this->errors[$field] = $cfg['label'].' mag maximaal '.$cfg['maxlength'].' tekens lang zijn.';
        }
        break;
        
      case 'minlength':
        if (strlen($value) < $cfg['minlength']) {
          $this->errors[$field] = $cfg['label'].' moet minstens '.$cfg['minlength'].' tekens lang zijn.';
        }
        break;
        
      case 'email':
        if ($value == '') return;
        
        $validator = new sfValidatorEmail();
        
        try {
          $validator->clean($value);
        }
        catch (sfValidatorError $e)
        {
          $this->errors[$field] = $cfg['label'].' is geen geldig e-mail adres.';
        }
        break;
        
      case 'password':
        $password = $this->getRequestParameter('password1');
        if ($password != '') {
          if (strlen($password) < 5) {
            $this->errors[$field] = $cfg['label'].' moet minimaal 5 tekens lang zijn.';
            $this->errors['password1'] = '';
            $this->errors['password2'] = '';
          }
          elseif($password != $this->getRequestParameter('password2')) {
            $this->errors[$field] = $cfg['label'].' is niet hetzelfde als het controle-wachtwoord.';
            $this->errors['password1'] = '';
            $this->errors['password2'] = '';
          }
        }
        
        if ($this->hasRequestParameter('username')) {
          // also a username to validate
          $user = $this->cobject->getUser();
          if (!$user) {
            $user = new User;
          }
          
          $c = new Criteria;
          $c->add(UserPeer::USERNAME, $this->getRequestParameter('username'));
          $users = UserPeer::doSelect($c);
          foreach ($users as $testUser) {
            if ($testUser->getId() != $user->getId()) {
              $this->errors['username'] = 'Deze gebruikersnaam is al in gebruik.';
            }
          }
        }
        break;
        
      case 'url':
        if ($value == '') return;
        
        $validator = new sfValidatorUrl();
        
        try {
          $validator->clean($value);
        }
        catch (sfValidatorError $e)
        {
          $this->errors[$field] = $cfg['label'].' is geen geldige URL.';
        }
        break;
        
      case 'color':
        if ($value == '') return;
        break;
        
      case 'phone':
        if ($value == '') return;
        break;
        
      case 'mobile':
        if ($value == '') return;
        break;
        
      case 'zipcode':
        if ($value == '') return;
        break;
    }
  }
  
  public function executeEdit(sfWebRequest $request)
  {
    $this->errorList = $this->getRequest()->getParameter('errors') ? $this->getRequest()->getParameter('errors') : array();
    
    if (!$this->hasRequestParameter('id')) {
      $object = new $this->model;
    }
    else {
      $object = call_user_func_array(array($this->model.'Peer', 'retrieveByPk'), array($this->getRequestParameter('id')));
    }
    
    
    if(!$object) {
      $object = new $this->model;
    }
    
    //$this->forward404Unless($object);
    
    if (class_exists($this->model.'I18NPeer')) {
      $object->setCulture('nl_NL');
    }

    $this->object = $object;
  }
  
  public function executeDelete(sfWebRequest $request)
  {
    if ($this->hasRequestParameter('id')) {
      $object = call_user_func_array(array($this->model.'Peer', 'retrieveByPk'), array($this->getRequestParameter('id')));
    }
    
    $this->forward404Unless($object);
    
    $config = zeusYaml::load('admin.yml');
    if (isset($config['list'])) {
      $change = 'Verwijderen van '.$config['list']['name']." '".$object->getTitle()."'";
      Changelog::add($change, 'delete', $object);
    }
    
    sfContext::getInstance()->getConfiguration()->loadHelpers('ZeusRoute');
    remove_route_for($object);
      
    $object->delete();
    $this->redirect($this->getRequestParameter('module').'/index');
  }
  
  public function executeExport(sfWebRequest $request)
  {
    
    $c = new Criteria;
    $c->addDescendingOrderByColumn(FormdataPeer::DATE);
    
    if (is_numeric($this->getRequestParameter('object'))) {
      $c->add(FormdataPeer::ID, $this->getRequestParameter('object'));
    }
    
    $forms = FormdataPeer::doSelect($c);
    
    $head = false;
    foreach ($forms as $form) {
      $fields = unserialize($form->getData());
      $row = array(
        $form->getDate(),
        $form->getTitle(),
        $form->getName()
      );
      
      if (!$head) {
        $head = array('Datum', 'Formulier', 'Afzender');
        foreach ($fields as $key => $value) {
          $head[$key] = $key;
        }
        $data[] = $head;
      }
      
      foreach ($fields as $key => $value) {
        $row[$key] = $value;
      }
      
      $data[] = $row;
    }
   
    $domain = str_replace('cms.', '', $_SERVER['HTTP_HOST']);
    
    switch($this->getRequestParameter('type')) {
      case 'xlsx':
        $excel = new PHPExcel();
    
        // Set properties
        $excel->getProperties()->setCreator("zeusCMS4");
        $excel->getProperties()->setLastModifiedBy("zeusCMS4");
        $excel->getProperties()->setTitle("Office 2007 XLSX zeusCMS4 Export");
        $excel->getProperties()->setSubject("Office 2007 XLSX zeusCMS4 Export");
        $excel->getProperties()->setDescription("");
        
        // Add some data
        $excel->setActiveSheetIndex(0);
        
        $cr = 0;
        foreach ($data as $row) {
          $cr++;
          $cc = 0;
          foreach ($row as $value) {
            $cc++;
            $cell = chr($cc + 64).$cr;
            $excel->getActiveSheet()->SetCellValue($cell, $value);
          }
        }
        
        // Rename sheet
        $excel->getActiveSheet()->setTitle('Export');
        
        		
        // Save Excel 2007 file
        $writer = new PHPExcel_Writer_Excel2007($excel);
        $writer->save(sfConfig::get('sf_web_dir').'/docs/export.xlsx');
        header('Location: http://'.$domain.'/docs/export.xlsx');
        break;
        
      case 'csv':
        $fp = fopen(sfConfig::get('sf_web_dir').'/docs/export.csv', 'w+');
        foreach ($data as $row) {
          fputcsv($fp, $row, ';', '"');
        }
        fclose($fp);
        header('Location: http://'.$domain.'/docs/export.csv');
        break;
        
      case 'ocsv':
        $fp = fopen(sfConfig::get('sf_web_dir').'/docs/export.csv', 'w+');
        foreach ($data as $row) {
          fputcsv($fp, $row, ',', '"');
        }
        fclose($fp);
        header('Location: http://'.$domain.'/docs/export.csv');
        break;
        
      case 'xml':
        $fp = fopen(sfConfig::get('sf_web_dir').'/docs/export.xml', 'w+');
        fwrite($fp, '<data>'."\n");
        foreach ($data as $row) {
          fwrite($fp, '  <row>'."\n");
          foreach ($row as $cell) {
            fwrite($fp, '    <cell>'.$cell.'</cell>'."\n");
          }
          fwrite($fp, '  </row>'."\n");
        }
        fwrite($fp, '</data>'."\n");
        fclose($fp);
        header('Location: http://'.$domain.'/docs/export.xml');
        break;
    }
    
    exit;
  }
  
  public function executeWizard(sfWebRequest $request)
  {
    $config = zeusYaml::load('admin.yml');
    
    if (!$this->hasRequestParameter('id')) {
      $object = new $this->model;
    }
    else {
      $object = call_user_func_array(array($this->model.'Peer', 'retrieveByPk'), array($this->getRequestParameter('id')));
    }
    
    
    if(!$object) {
      $object = new $this->model;
    }
    
    //$this->forward404Unless($object);
    
    if (class_exists($this->model.'I18NPeer')) {
      $object->setCulture(sfConfig::get('sf_default_culture'));
    }

    $steps = array_keys($config['wizard']['steps']);
    $step = $this->hasRequestParameter('step') ? $this->getRequestParameter('step') : $steps[0];
    
    if (!$this->hasRequestParameter('step')) {
      $parameters = array();
    }
    else {
      $parameters = unserialize($this->getUser()->getAttribute('wizardFields'));
    }

    // todo : set fields for this step here
    if ($this->hasRequestParameter('step') && $this->getRequestParameter('modus') == 'next') {
      $i18n_data = isset($parameters['i18n_values']) ? json_decode($parameters['i18n_values']) : array();
      $i18n_culture = isset($parameters['i18n_culture']) ? $parameters['i18n_culture'] : $this->getUser()->getCulture();
      $i18n_values = array();
      if (!$i18n_data) $i18n_data = array();
      foreach ($i18n_data as $field => $values) {
        foreach ($values as $culture => $value) {
          $i18n_values[(string)$field][(string)$culture] = $value;
        }
        
        // load last values from post into correct culture
        $i18n_values[(string)$field][$i18n_culture] = $this->getRequestParameter((string)$field);
      }
      
      unset($parameters['i18n_values']);
      
      $stepParams = $this->getRequest()->getParameterHolder()->getAll();
      
      if (!isset($config['wizard']['steps'][$step]['fields']) && isset($config['wizard']['steps'][$step]['depends'])) {
        $depends = isset($parameters[$config['wizard']['steps'][$step]['depends']]) ? $parameters[$config['wizard']['steps'][$step]['depends']] : array_shift(array_keys($config['wizard']['steps'][$step]));
        $config['wizard']['steps'][$step]['fields'] = $config['wizard']['steps'][$step][$depends]['fields'];
      }
      
      foreach ($config['wizard']['steps'][$step]['fields'] as $field => $cfg) {
        $value = isset($stepParams[$field]) ? $stepParams[$field] : '';
        if (isset($cfg['required'])) {
          if (($cfg['type'] == 'checkbox' && !$this->hasRequestParameter($field)) || $value == '') {
            $this->errors[$field] = $cfg['label'].' is een verplicht veld.';
          }
        }

        if (isset($cfg['validate'])) {
          $this->validateField($cfg['validate'], $value, $cfg, $field);
        }
      }
     
      $parameters = array_merge($parameters, $stepParams);
      
      $i18n_data = isset($parameters['i18n_values']) ? json_decode($parameters['i18n_values']) : array();
      $new_i18n_culture = isset($parameters['i18n_culture']) ? $parameters['i18n_culture'] : $this->getUser()->getCulture();
      $new_i18n_values = array();
      
      foreach ($i18n_data as $field => $values) {
        foreach ($values as $culture => $value) {
          $new_i18n_values[(string)$field][(string)$culture] = $value;
        }
        
        // load last values from post into correct culture
        $new_i18n_values[(string)$field][$new_i18n_culture] = $this->getRequestParameter((string)$field);
      }
      
      $i18n_values = array_merge($i18n_values, $new_i18n_values);
      $parameters['i18n_values'] = json_encode($i18n_values);
    }
    
    foreach ($parameters as $key => $value) {
      if (!in_array($key, array('step', 'modus', 'id', 'i18n_values', 'i18n_culture'))) {
        $this->getRequest()->setParameter($key, $value);
      }
    }
    
    $this->getUser()->setAttribute('wizardFields', serialize($parameters));
    
    if ($this->hasRequestParameter('step') && $this->getRequestParameter('modus') == 'next') {
      
      
      if (count($this->errors) > 0) {
        $this->getRequest()->setParameter('id', $object->getId());
        $this->getRequest()->setParameter('action', 'wizard');
        $this->getRequest()->setParameter('errors', $this->errors);
        $this->preExecute();
      }
      else {
        $nextStep = array_search($step, $steps) + 1;
        if (isset($steps[$nextStep])) {
          $step = $steps[$nextStep];
        }
        else {
          
          $i18n_data = isset($parameters['i18n_values']) ? json_decode($parameters['i18n_values']) : array();
          $i18n_culture = isset($parameters['i18n_culture']) ? $parameters['i18n_culture'] : $this->getUser()->getCulture();
          $i18n_values = array();
          if (!$i18n_data) $i18n_data = array();
          foreach ($i18n_data as $field => $values) {
            foreach ($values as $culture => $value) {
              $i18n_values[(string)$field][(string)$culture] = $value;
            }
            
            // load last values from post into correct culture
            $i18n_values[(string)$field][$i18n_culture] = $this->getRequestParameter((string)$field);
          }
          
          $parameters['i18n_values'] = json_encode($i18n_values);
          
          $adminFields = array();
          foreach ($config['wizard']['steps'] as $step => $cfg) {
            if (!isset($cfg['fields']) && isset($cfg['depends'])) {
              $depends = isset($parameters[$cfg['depends']]) ? $parameters[$cfg['depends']] : array_shift(array_keys($cfg[$step]));
              $cfg['fields'] = $cfg[$depends]['fields'];
            }
            $adminFields = array_merge($adminFields, $cfg['fields']);
          }
          
          $adminConfig = array('edit' => array('fields' => $adminFields));
          $this->executeUpdate($request, $adminConfig);
        }
      }
    }
    
    // determine which step we are on, to enable prev and next buttons
    $prev = $next = false;
    $prevStep = $steps[0];
    
    if ($step != $steps[0]) {
      $prev = true;
      $prevStep = isset($steps[array_search($step, $steps) - 1]) ? $steps[array_search($step, $steps) - 1] : $steps[0];
    }
    if ($step != $steps[count($steps) -1]) {
      $next = true;
    }
    
    zeusRibbon::addButton(new zeusRibbonButton(array('label' => 'Vorige', 'icon' => '1leftarrow', 'disabled' => !$prev, 'callback' => "$('modus').value='prev';$('step').value='{$prevStep}';$('zeus-1').submit()")), 'Acties');
    if ($step == $steps[count($steps) -1]) {
      zeusRibbon::addButton(new zeusRibbonButtonSave(array('form' => 'zeus-1'))); 
    }
    else {
      zeusRibbon::addButton(new zeusRibbonButton(array('label' => 'Volgende', 'icon' => '1rightarrow', 'disabled' => !$next, 'callback' => "$('zeus-1').submit()")), 'Acties');
    }
    
    
    if (!isset($config['wizard']['steps'][$step]['fields']) && isset($config['wizard']['steps'][$step]['depends'])) {
      $depends = isset($parameters[$config['wizard']['steps'][$step]['depends']]) ? $parameters[$config['wizard']['steps'][$step]['depends']] : array_shift(array_keys($config['wizard']['steps'][$step]));
      $config['wizard']['steps'][$step]['fields'] = $config['wizard']['steps'][$step][$depends]['fields'];
    }
    
    
    $this->step = $step;
    $this->object = $object;
    $this->config = $config;
    $this->errorList = $this->getRequest()->getParameter('errors') ? $this->getRequest()->getParameter('errors') : array();

  }
}