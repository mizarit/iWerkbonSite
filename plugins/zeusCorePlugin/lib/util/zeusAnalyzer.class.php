<?php

class zeusAnalyzer
{
  var $changes = array();
	var $diff = array();
	var $linepadding = null;
	
  public static function getKeywords($text)
  {
    $list = file(sfConfig::get('sf_data_dir').'/th_nl_v2.dat');
    
    $key = '';
    
    $items = array();
    
    foreach ($list as $item) {
      if ($item[0] == '-') {
        $parts = explode('|', $item);
        foreach ($parts as $part) {
          $part = trim($part);
          
          if ($part == '-') continue;
          
          if (!strstr($part, '(')) {
            // no antonym, general term etc
            if (!isset($items[$key])) {
              $items[$key][] = $key;
            }
            
            $items[$key][] = $part;
          }
        }
        
      }
      else {
        // key
        $parts = explode('|', $item);
        $key = $parts[0];
      }
    }
    
    $counted_words = array();
    $important_words = array();
    $long_words = array();
    $suggested_words = array();
    $suggested_synonyms = array();
    
    $text = str_replace("\n", ' ', $text);
    
    $words = explode(' ', $text);
    foreach ($words as $word) {
      $word = trim($word, ",.?!'\"\n\r");
      
      $word = strtolower($word);
      
      if (strstr($word, '.')) continue;
      
      if (is_numeric($word)) continue;
      
      if (in_array($word, self::getCommonWords())) continue;
      
      if (!isset($counted_words[$word])) {
        $counted_words[$word] = 0;
      }
      $counted_words[$word]++;
    }
 
    $range = max($counted_words) / 5; // < 20% occurrence 
    
    foreach ($counted_words as $word => $count) {
      if ($count < $range) {
        $important_words[$word] = $count;
      }
    }
    
    if (count($important_words) == 0) {
      $important_words = $counted_words;
    }
    

    foreach ($important_words as $word => $count) {
      $long_words[$word] = strlen($word) + $count; // take both occurences and length in regard
    }
    
    arsort($long_words);
    foreach ($long_words as $word => $score) {
      $suggested_words[] = $word;
    
      foreach ($items as $synonym_block) {
        if (in_array($word, $synonym_block)) {
          foreach ($synonym_block as $synonym) {
            $suggested_words[] = $synonym;
            
            if ($word != $synonym) {
              $suggested_synonyms[$word][] = $synonym;
            }
          }
        }
      }
      
      $suggested_words = array_unique($suggested_words);
      //$suggested_synonyms = array_unique($suggested_synonyms);
      
      if (count($suggested_words) > 75) break;
    }
    
    $v = array(
      'suggested_words' => $suggested_words,
      'suggested_synonyms' => $suggested_synonyms,
      'counted_words' => $counted_words,
      'important_words' => $important_words
    );
    
    ob_start();
    echo implode(', ', $v['suggested_words']); 
    foreach ($v['suggested_synonyms'] as $word => $suggestions) { 
      $suggestions = array_unique($suggestions);
      echo ', '.$word.', '.implode(', ', $suggestions);
    }
    
    $ret = ob_get_clean();
    return trim($ret, ', ');
  }
  
  private static function getCommonWords()
  {
    return array(
      'de',
      'het',
      'een',
      'bij',
      'was',
      'om',
      'zijn',
      'sinds',
      'aan',
      'gaan',
      'ads',
      'door',
      'beter',
      'onze',
      'kom',
      'wat',
      'je',
      'wie',
      'ik',
      'jouw',
      'hier',
      'na',
      'vol',
      'val',
      'mag',
      'nieuwe',
      'ongeveer',
      'heeft',
      'die',
      'uit',
      'wel',
      'meteen',
      'naar'
      );
  }
	
	public function doDiff($old, $new){
		if (!is_array($old)) $old = explode("\n", $old);
		if (!is_array($new)) $new = explode("\n", $new);
	
		$maxlen = 0;
		
		foreach($old as $oindex => $ovalue){
			$nkeys = array_keys($new, $ovalue);
			foreach($nkeys as $nindex){
				$matrix[$oindex][$nindex] = isset($matrix[$oindex - 1][$nindex - 1]) ? $matrix[$oindex - 1][$nindex - 1] + 1 : 1;
				if($matrix[$oindex][$nindex] > $maxlen){
					$maxlen = $matrix[$oindex][$nindex];
					$omax = $oindex + 1 - $maxlen;
					$nmax = $nindex + 1 - $maxlen;
				}
			}       
		}
		if($maxlen == 0) return array(array('d'=>$old, 'i'=>$new));
		
		return array_merge(
						$this->doDiff(array_slice($old, 0, $omax), array_slice($new, 0, $nmax)),
						array_slice($new, $nmax, $maxlen),
						$this->doDiff(array_slice($old, $omax + $maxlen), array_slice($new, $nmax + $maxlen)));
						
	}
	
	public function diffWrap($old, $new){
		$this->diff = $this->doDiff($old, $new);
		$this->changes = array();
		$ndiff = array();
		foreach ($this->diff as $line => $k){
			if(is_array($k)){
				if (isset($k['d'][0]) || isset($k['i'][0])){
					$this->changes[] = $line;
					$ndiff[$line] = $k;
				}
			} else {
				$ndiff[$line] = $k;
			}
		}
		$this->diff = $ndiff;
		return $this->diff;
	}
	
	public function formatcode($code){
		$code = htmlentities($code);
		$code = str_replace(" ",' ',$code);
		$code = str_replace("\t",'    ',$code);
		return $code;
	}
	
	public function showline($line){
		if ($this->linepadding === 0){
			if (in_array($line,$this->changes)) return true;
			return false;
		}
		if(is_null($this->linepadding)) return true;

		$start = (($line - $this->linepadding) > 0) ? ($line - $this->linepadding) : 0;
		$end = ($line + $this->linepadding);
		//echo '<br />'.$line.': '.$start.': '.$end;
		$search = range($start,$end);
		//pr($search);
		foreach($search as $k){
			if (in_array($k,$this->changes)) return true;
		}
		return false;

	}
	
	public function inline($old, $new, $linepadding=null){
		$this->linepadding = $linepadding;
		
		$ret = '<table class="code">';
		$ret.= '<tr><td>O</td><td>N</td><td></td></tr>';
		$count_old = 1;
		$count_new = 1;
		
		$insert = false;
		$delete = false;
		$truncate = false;
		
		$diff = $this->diffWrap($old, $new);

		foreach($diff as $line => $k){
			if ($this->showline($line)){
				$truncate = false;
				if(is_array($k)){
					foreach ($k['d'] as $val){
						$class = '';
						if (!$delete){
							$delete = true;
							$class = 'first';
							if ($insert) $class = '';
							$insert = false;
						}
						$ret.= '<tr><th>'.$count_old.'</th>';
						$ret.= '<th> </th>';
						$ret.= '<td class="del '.$class.'">'.$this->formatcode($val).'</td>';
						$ret.= '</tr>';
						$count_old++;
					}
					foreach ($k['i'] as $val){
						$class = '';
						if (!$insert){
							$insert = true;
							$class = 'first';
							if ($delete) $class = '';
							$delete = false;
						}
						$ret.= '<tr><th> </th>';
						$ret.= '<th>'.$count_new.'</th>';
						$ret.= '<td class="ins '.$class.'">'.$this->formatcode($val).'</td>';
						$ret.= '</tr>';
						$count_new++;
					}
				} else {
					$class = ($delete) ? 'del_end' : '';
					$class = ($insert) ? 'ins_end' : $class;
					$delete = false;
					$insert = false;
					$ret.= '<tr><th>'.$count_old.'</th>';
					$ret.= '<th>'.$count_new.'</th>';
					$ret.= '<td class="'.$class.'">'.$this->formatcode($k).'</td>';
					$ret.= '</tr>';
					$count_old++;
					$count_new++;
				}
			} else {
				$class = ($delete) ? 'del_end' : '';
				$class = ($insert) ? 'ins_end' : $class;
				$delete = false;
				$insert = false;
				
				if (!$truncate){
					$truncate = true;
					$ret.= '<tr><th>...</th>';
					$ret.= '<th>...</th>';
					$ret.= '<td class="truncated '.$class.'">&nbsp;</td>';
					$ret.= '</tr>';
				}
				$count_old++;
				$count_new++;

			}
		}
		$ret.= '</table>';
		return $ret;
	}
}