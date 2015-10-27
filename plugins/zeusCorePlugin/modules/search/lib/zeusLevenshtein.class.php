<?php

class zeusLevenshtein {
  private $dictionary;
  
  function __construct($dict) {
    $this->setDictionary($dict);
  }
  
  public function getSuggestion($word = false) {
    if(strlen($word)==0) return false;
    $word = strtolower($word);
    //Registry::set('suggesties_caching', false);
    if(sfConfig::get('suggesties_caching')) {
      $key = ftok(__FILE__,'f');
      $sem = sem_get($key,1, 0600);
      sem_acquire($sem);
      $shm_id = shmop_open(0xff0, "c", 0644, sfConfig::get('suggestion_cachesize'));
      if($shm_id) {
        $suggestions = unserialize(shmop_read($shm_id, 0, $shm_size));
        if(isset($suggestions[$word])) {
          $sug = $suggestions[$word]['word'];
          $suggestions[$word]['cnt']++;
          shmop_write($shm_id, serialize($suggestions), 0);
          shmop_close($shm_id);
          sem_release($sem);
          return $sug;
        }
      }
    }
    $fp = fopen($this->dictionary, 'r');
    if(!$fp) {
      throw new Exception(sprintf("Failed to open Levenshtein dictionary %s", $this->dictionary));
    }
    $s = -1;
    while(!feof($fp)) {
      $word2 = trim(fgets($fp), "\n");
      $lev = levenshtein($word, $word2);
       if ($lev == 0) {
           $c = $word2;
           $s = 0;
           break;
       }
       if ($lev <= $s || $s < 0) {
           $c  = $word2;
           $s = $lev;
       }
    }
    
    $fp = fopen($this->dictionary.'.specific', 'r');
    if(!$fp) {
      throw new Exception(sprintf("Failed to open Levenshtein dictionary %s", $this->dictionary));
    }
    $s = -1;
    while(!feof($fp)) {
      $word2 = trim(fgets($fp), "\n");
      $lev = levenshtein($word, $word2);
       if ($lev == 0) {
           $c = $word2;
           $s = 0;
           break;
       }
       if ($lev <= $s || $s < 0) {
           $c  = $word2;
           $s = $lev;
       }
    }
    
    fclose($fp);
    if($s != 0) {
      // loggen voor eventuele aanpassingen aan library
      $suggestfile = $this->dictionary.'.suggested';
      if(file_exists($suggestfile)) {
        $fp = fopen($suggestfile, 'r');
        while($data = fgetcsv($fp, 256,";")) {
          $suggest[$data[0]] = $data[1];
        }
      }
      $suggest[$word] = $c;
      $fp = fopen($suggestfile, 'w');
      foreach($suggest as $word => $suggestion) {
        if(!is_numeric($word)){
          fwrite($fp, "{$word};{$suggestion}\n");
        }
      }
      fclose($fp);
    }
    if(sfConfig::get('suggesties_caching')) {
      $suggestions[$word]['word'] = $c;
      $suggestions[$word]['cnt'] = 1;
      $suggestions[$word]['create'] = time();
      $size = strlen(serialize($suggestions));
     
      $shm_id = shmop_open(0xff0, "c", 0644, sfConfig::get('suggestion_cachesize'));
      if ($shm_id) {
        $write = serialize($suggestions);
        if(strlen($write) < sfConfig::get('suggestion_cachesize')) {
          shmop_write($shm_id, serialize($suggestions), 0);
        }
        else {
          //fixme: te oude cache verwijderen uit suggestions
        }
        shmop_close($shm_id);
      }
      sem_release($sem);
      sem_remove($sem);
    }
    return ($s == 0) ? false : $c;
  }
  
  public function setDictionary($dict) {
    $this->dictionary = $dict;
  }
}


?>