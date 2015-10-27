<?php

class baseSearchActions extends sfActions
{
  public function executeIndex()
  {
    $query = '';
    
    $this->suggested = false;
    
    if ($this->hasRequestParameter('query')) {
      $query = $this->getRequestParameter('query');
    }
    
    $results = zeusSearch::getInstance()->search($query);
    
    if ($results['total_hits'] == 0) {
      $suggenstions = new zeusLevenshtein(sfConfig::get('sf_plugins_dir'). '/zeusCorePlugin/data/levenshtein/woorden.med');
      
      $words = explode(' ', $query);
      $suggested = '';
      foreach ($words as $word) {
        
        $suggestion = $suggenstions->getSuggestion($word);
        
        if ($suggestion) {
          $suggested .= '<span class="suggested">'.$suggestion. '</span>';
        }
        else { 
          $suggested .= $word;
        }
        
        $suggested .= ' ';
      }
      
      $suggested = trim($suggested);
      
      if (strip_tags($suggested) != $query) {
        $this->suggested = $suggested;
      }
    }
    
    if (!$results || $results['total_hits'] == 0) {
      if ($this->suggested && strip_tags($this->suggested) != $query) {
        $this->getRequest()->setParameter('suggested', $this->suggested);
      }
      $this->forward('search', 'noresults');
    }
    
    $this->results = $results;
  }
  
  public function executeNoresults()
  {
    $this->suggested = $this->getRequest()->getParameter('suggested');
  }
  
  public function executeImport()
  {
    $wordlist = array();
    
    set_time_limit(0);
    
    $dictionary = file(sfConfig::get('sf_plugins_dir').'/zeusCorePlugin/data/levenshtein/woorden.med');
  
    
    $blogs = BlogPeer::doSelect(new Criteria);
    foreach ($blogs as $blog) {
      $lines = explode("\n", $blog->getContent());
      foreach ($lines as $line) {
        $words = explode(' ', str_replace("\r", ' ', str_replace("\n", ' ', strip_tags($line))));
        foreach ($words as $word) {
          $word = trim($word, " :,.;!?/\+-'\"()[]");
          
          $word = htmlspecialchars_decode($word);
          $word = html_entity_decode($word);
          
            if (!isset($wordlist[$word])) {
              $wordlist[$word] = 0;
            }
            
            $wordlist[$word]++;
        }
      }
    }
    
    asort($wordlist);
    
    foreach ($wordlist as $word => $count) {
      if ($count < 700) { 
        if (!in_array($word, $dictionary)) {
          echo $word . "\n";
        }
      }
    }
    exit;
  }
}