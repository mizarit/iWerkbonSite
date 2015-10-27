<?php

class formForm extends sfForm
{
  
  public function configure()
  {
    $widgets = array();
    
    sfContext::getInstance()->getConfiguration()->loadHelpers('I18N');
    
    foreach ($this->options['form-config']['fields'] as $name => $cfg) {
      $options = array();
      switch($cfg['type']) {
        case 'input':
        default:
          $type = 'sfWidgetFormInput';
          break;
          
        case 'textarea':
          $type = 'sfWidgetFormTextarea';
          break;
          
        case 'file':
          $type = 'sfWidgetFormInputFile';
          break;
          
        case 'checkbox':
          $type = 'sfWidgetFormInputCheckbox';
          break;
          
        case  'radio':
          $type = 'sfWidgetFormSelectRadio';
          if (is_array($cfg['choices'])) {
            if (isset($cfg['choices']['helper'])) {
              sfContext::getInstance()->getConfiguration()->loadHelpers($cfg['choices']['helper']);
              $options['choices'] = $cfg['choices']['method']();
            }
            else {
              $options['choices'] = $cfg['choices'];
            }
            
            $options['default'] = $cfg['value'];
            
            $this->options['form-config']['fields'][$name]['label'] = '&nbsp;';
            
            
          }
          break;
          
        case 'select':
          $type = 'sfWidgetFormSelect';
          if (is_array($cfg['choices'])) {
            if (isset($cfg['choices']['helper'])) {
              sfContext::getInstance()->getConfiguration()->loadHelpers($cfg['choices']['helper']);
              $options['choices'] = $cfg['choices']['method']();
            }
            else {
              $options['choices'] = $cfg['choices'];
            }
            
            
          }
          break;
      }
      
      $widgets[$name] = new $type($options);
      
      switch($cfg['type']) {
        case 'fixed':
          if (isset($_POST['form_form'][$name])) {
            $widgets[$name]->setAttribute('value', $_POST['form_form'][$name]);
          }
          else {
            $widgets[$name]->setAttribute('value', $cfg['value']);
          }
          $widgets[$name]->setAttribute('readonly', 'readonly');
          break;
          
        case 'select':
           if (isset($_POST['form_form'][$name])) {
            $widgets[$name]->setAttribute('value', $_POST['form_form'][$name]);
          }
          else {
            $widgets[$name]->setAttribute('value', $cfg['value']);
            $widgets[$name]->setDefault($cfg['value']);
          }
          break;
          
        case 'checkbox':
          $widgets[$name]->setAttribute('class', 'checkbox');
          if (isset($cfg['checked']) && $cfg['checked']) {
            $widgets[$name]->setAttribute('checked', 'checked');
          }
          break;
          
        case 'radio':
          $widgets[$name]->setAttribute('class', 'checkbox');
          //$widgets[$name]->setAttribute('value', $cfg['value']);
          break;
          
        case 'input':
        case 'textarea':
          if (isset($cfg['value']) && !isset($_POST['form_form'][$name])) {
            $widgets[$name]->setAttribute('value', $cfg['value']);
            $widgets[$name]->setDefault($cfg['value']);
          }
          break;
      }      
    }
    
    $this->setWidgets($widgets);
    
    foreach ($this->options['form-config']['fields'] as $name => $cfg) {
      if (isset($cfg['label'])) {
        $this->widgetSchema->setLabel($name, __($cfg['label']));
      }
      
      switch($cfg['type']) {
        case 'input':
        default:
          $validators[$name] = new sfValidatorPass;
          break;
          
        case 'file':
          $validators[$name] = new sfValidatorFile ;
          break;
      }
      
      if (isset($cfg['required']) && $cfg['required']) {
        $validators[$name] = new sfValidatorString(
	       array(
	         'required' => true
	       ),
	       array(
	         'required' => __($cfg['label'].' is een verplicht veld')
	       )
	     );
      }
    }
    
    if (isset($validators)) {
      $this->setValidators($validators);
    }

    $this->widgetSchema->setFormFormatterName('zeus');
    
    $this->widgetSchema->setNameFormat('form_form[%s]');
  }
}