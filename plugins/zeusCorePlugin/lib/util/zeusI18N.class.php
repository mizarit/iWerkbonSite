<?php

class zeusI18N
{
  public static function getValidCulturesForObject($object)
  {
    $all_cultures = sfConfig::get('sf_enabled_cultures');
    
    foreach ($all_cultures as $culture) {
      $cultures[$culture] = zeusI18N::hasValidCultureForObject($object, $culture);
    }
    
    return $cultures;
  }
  
  public static function hasValidCultureForObject($object, $culture)
  {
    $c = new Criteria;
    $model = get_class($object).'I18N';
    $model_peer = get_class($object).'I18NPeer';
    $c->add(constant($model_peer.'::CULTURE'), $culture);
    $c->add(constant($model_peer.'::ID'), $object->getId());
    
    $check = call_user_func_array(array($model_peer, 'doSelectOne'), array($c));
    return (bool)$check;
  }
  
  public static function getLanguageName($culture, $use_culture = false)
  {
    if (!$use_culture) $use_culture = sfContext::getInstance()->getUser()->getCulture();
    
    $names['nl_NL']['nl_NL'] = 'Nederlands';
    $names['nl_NL']['fr_FR'] = 'Frans';
    $names['nl_NL']['de_DE'] = 'Duits';
    $names['nl_NL']['en_US'] = 'Engels';
    
    $names['en_US']['nl_NL'] = 'Dutch';
    $names['en_US']['fr_FR'] = 'French';
    $names['en_US']['de_DE'] = 'German';
    $names['en_US']['en_US'] = 'English';
    
    $names['de_DE']['nl_NL'] = 'Niederlandisch';
    $names['de_DE']['fr_FR'] = 'Französisch';
    $names['de_DE']['de_DE'] = 'Deutsch';
    $names['de_DE']['en_US'] = 'English';
    
    $names['fr_FR']['nl_NL'] = 'Néerlandais';
    $names['fr_FR']['fr_FR'] = 'Français';
    $names['fr_FR']['de_DE'] = "l'Allemand";
    $names['fr_FR']['en_US'] = 'Anglais';
    return isset($names[$use_culture][$culture]) ? $names[$use_culture][$culture] : $culture;
  }
}   