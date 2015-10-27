<?php

require_once sfConfig::get('sf_symfony_lib_dir').'/plugins/sfPropelPlugin/lib/builder/SfObjectBuilder.php';

class ZeusI18NFallbackObjectBuilder extends SfObjectBuilder {
  protected function addI18nMethods(&$script)
  {
    $table = $this->getTable();
    $pks = $table->getPrimaryKey();
    $pk = $pks[0]->getPhpName();

    foreach ($table->getReferrers() as $fk)
    {
      $tblFK = $fk->getTable();
      if ($tblFK->getName() == $table->getAttribute('i18nTable'))
      {
        $className = $tblFK->getPhpName();
        $culture = '';
        $culture_peername = '';
        foreach ($tblFK->getColumns() as $col)
        {
          if (("true" === strtolower($col->getAttribute('isCulture'))))
          {
            $culture = $col->getPhpName();
            $culture_peername = PeerBuilder::getColumnName($col, $className);
          }
        }

        foreach ($tblFK->getColumns() as $col)
        {
          if ($col->isPrimaryKey()) continue;

          $script .= '
  public function get'.$col->getPhpName().'($culture = null)
  {
    $string = $this->getCurrent'.$className.'($culture)->get'.$col->getPhpName().'();
    if( $string == null ) {
      if(sfConfig::get("sf_use_fallback") && ($this->getCulture() != sfConfig::get("sf_default_culture"))) {
        $this->setCulture(sfConfig::get("sf_default_culture"));
        $string=$this->getAvailable'.$className.'()->get'.$col->getPhpName().'();
      }
    }
    return $string;
  }

  public function set'.$col->getPhpName().'($value, $culture = null)
  {
    $this->getCurrent'.$className.'($culture)->set'.$col->getPhpName().'($value);
  }
';
        }

$script .= '
  protected $current_i18n = array();

  public function getCurrent'.$className.'($culture = null)
  {
    if (is_null($culture))
    {
      $culture = is_null($this->culture) ? sfPropel::getDefaultCulture() : $this->culture;
    }

    if (!isset($this->current_i18n[$culture]))
    {
      $obj = '.$className.'Peer::retrieveByPK($this->get'.$pk.'(), $culture);
      if ($obj)
      {
        $this->set'.$className.'ForCulture($obj, $culture);
      }
      else
      {
        $this->set'.$className.'ForCulture(new '.$className.'(), $culture);
        $this->current_i18n[$culture]->set'.$culture.'($culture);
      }
    }

    return $this->current_i18n[$culture];
  }

  public function set'.$className.'ForCulture($object, $culture)
  {
    $this->current_i18n[$culture] = $object;
    $this->add'.$className.'($object);
  }

  // Return avaliable translation, default culture prefered
  public function getAvailable'.$className.' () {
    $i18n_object = new '.$className.'();
    $objects = $this->get'.$className.'s();

    foreach ($objects as $i18n_object) {
      if ($i18n_object->getCulture() == sfConfig::get(\'sf_default_culture\')) {
        return $i18n_object;
      }
    }

    // If there is no default culture object return last
    return $i18n_object;
  }
';
      }
    }
  }
}
