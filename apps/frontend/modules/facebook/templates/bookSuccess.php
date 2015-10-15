<div id="facebook-form">

  <button class="button-1-b" onclick="window.location.href='<?php echo url_for('@facebook_app'); ?>';"><span>Vorige pagina</span></button>

  <h3>Reserveer: '<?php echo $object->getTitle(); ?>'</h3>

<?php $profilename = $object->getCompany()->getCompanyprofile()->getTitle(); ?>

<?php use_helper('ZeusEdit'); ?>
<?php echo zeus_edit_textpartial(null, '', '', array('key' => 'facebook boekingsformulier intro '.$profilename)); ?>

<?php echo zeus_edit_textpartial(null, '', '', array('key' => 'facebook boekingsformulier intro')); ?>
        				
<?php if ($message != '') { ?>
<ul class="form-messages">
  <li><?php echo $message; ?></li>
</ul>
<?php } ?>
<?php if (count($errors) > 0) { ?>
<ul class="form-errors">
<?php foreach ($errors as $key => $error) { ?>
  <li><?php echo $error; ?></li>
<?php } ?>
</ul>
<?php } ?>

<form action="#" method="post">
  <fieldset>
    <legend></legend>
    
    <?php
sfContext::getInstance()->getUser()->setAttribute('fake_company', $object->getCompanyId());
$apptypes = CapacityConnector::getApptypes($object); 

$prefix[0] = '';
$prefix[1] = 'morgen om ';
$prefix[2] = ucfirst(strftime('%A', strtotime('+2 day'))).' om ';
for ($d = 3; $d < 28; $d++) {
  $prefix[$d] = ucfirst(strftime('%A', strtotime('+'.$d.' day'))).' om ';
}

$dates = array();
$get = $object->getUnit() == 'week' ? 28 : 4;
for ($d = 0; $d < $get; $d++) {
  $dates[] = date('Y-m-d', strtotime('+'.$d.' day'));
}

$timesPerApptype = array();
$pricePerApptype = array();
$capacityPerApptype = array();
$travelPerApptype = array();
$validVerticalPerApptypeTime = array();
$validHorizontalPerApptypeTime = array();
$durationPerApptype = array();

if ($object->getTravel()) {
  foreach ($apptypes as $apptype) {
    $travelPerApptype[$apptype->getId()] = CapacityConnector::getTravel($object, $apptype);
  }
}

$duration = false;

$hasTimes = false;
$travelDates = array();
foreach ($dates as $k => $date) {
  $times = $object->getTimelistRaw($date);
  foreach ($apptypes as $apptype) {
    $t = $object->getTimelistRaw($date, array($apptype));
    $rt = array();
    foreach ($t as $tm) {
      $rt[] = $tm['time'];
    }
    
    $interval = CapacityConnector::getInterval($object, $apptype);
    
    $durationPerApptype[$apptype->getId()] = $apptype->getDuration();
    
    if (!$duration) {
      $duration = $apptype->getDuration();
    }
    $pricePerApptype[$apptype->getId()] = $apptype->getPrice();
    foreach ($t as $key => $time_raw) {
      $x = $time_raw['time'];
      $capacity = $time_raw['capacity'];
      
      $validHorizontalPerApptypeTime[$apptype->getId()][$date.' '.$x] = $capacity;
      
      $hasTimes = true;
      if ($object->getTravel()) {
        $names[$date] = 'Vandaag';
        $names[date('Y-m-d', strtotime('+1 day'))] = 'Morgen';
        $names[date('Y-m-d', strtotime('+2 day'))] = ucfirst(strftime('%A', strtotime('+2 day')));
        for ($d = 3; $d < 28; $d++) {
          $names[date('Y-m-d', strtotime('+'.$d.' day'))] = ucfirst(strftime('%A', strtotime('+'.$d.' day'))).' om ';
        }

        if (!isset($timesPerApptype[$apptype->getId()]) || !in_array($names[$date], $timesPerApptype[$apptype->getId()])) {
          $timesPerApptype[$apptype->getId()][$date.' '.$x] = $names[$date];
        }
        $travelDates[$date] = true;
      }
      else if (!$object->getTravel()) {
        $timesPerApptype[$apptype->getId()][$date.' '.$x] = $prefix[$k].$x;
      }
      $capacityPerApptype[$apptype->getId()] = $apptype->getCapacity() > 1 ? $apptype->getCapacity() : 1;
      
      // find out how many connected slots are available
      list($h, $m) = explode(':', $x);
      $test = ($h * 60) + $m;
      $testing = true;
      $safety = 0;
      $valid = 1;
      while ($testing && $safety < 100) {
        $safety++;
        $test += $interval;
        $th = floor($test / 60);
        $time = str_pad($th, 2, '0', STR_PAD_LEFT).':'.str_pad($test-($th*60), 2, '0', STR_PAD_LEFT);
        if (in_array($time, $rt)) {
          $valid++;
        }
        else {
          $testing = false;
        }
      }
      
      $validVerticalPerApptypeTime[$apptype->getId()][$date.' '.$x] = $valid;
    }
  }
}

if (!$hasTimes) {
  ?>
  <h5>Selecteer het tijdstip</h5>
  <?php echo zeus_edit_textpartial(null, '', '', array('key' => 'boekingsformulier geen tijden gevonden')); ?>
  <?php
}
else {
  $firstApptype = false;
  $profilename = $object->getCompany()->getCompanyprofile()->getTitle();

?>

                    <h3>Details van je afspraak</h3>
                    <div class="book-section-inner">
                    <h5>De afspraak</h5>
                    <p id="apptype-name"><?php if (count($apptypes) == 1) { ?>
<span>Soort afspraak:</span><?php echo $apptypes[0]->getTitle(); ?>
<?php } ?></p>
                    <p id="apptype-datetime"></p>
                    <?php include_partial('lastminutes/calculator'.ucfirst($profilename), array('object' => $object, 'profilename' => $profilename)); ?>
                    <div id="duration-str-block">
                    <h5>Duur van je afspraak</h5>
                    <?php if ($object->getTravel()) { ?>
                      <p><strong>Je verblijf is van <span id="duration-str"></span>.</strong></p>
                      <?php echo $object->getExtra(); ?>
<?php } else { ?>
                      <p>Je afspraak duurt in totaal <span id="duration-str"><?php echo $duration; ?> minuten</span>.</p>
<?php } ?>
                    </div>
                    </div>
                    
                    
<h3>Je reservering</h3>
<div class="book-section-inner">
<?php
if (count($apptypes) > 1) { 
  include_partial('lastminutes/apptype'.ucfirst($profilename), array('apptypes' => $apptypes, 'timesPerApptype' => $timesPerApptype, 'profilename' => $profilename, 'object' => $object));
}
else {
  $firstApptype = $apptypes[0]->getId();
  echo '<input type="hidden" name="apptype" id="apptype" value="'.$apptypes[0]->getId().'">';
}
?>








<?php if ($object->getTravel()) { ?>
              				<h5>Selecteer de aankomstdatum</h5>
<?php echo zeus_edit_textpartial(null, '', '', array('key' => 'boekingsformulier tijdstip travel')); ?>
              				<div class="form-row">
<?php } elseif ($object->getUnit() == 'week') { ?>
  <h5>Selecteer de datum</h5>
<?php 
echo zeus_edit_textpartial(null, '', '', array('key' => 'boekingsformulier datum')); 
?>  
<?php } else { ?>
              				<h5>Selecteer het tijdstip</h5>
<?php 
echo zeus_edit_textpartial(null, '', '', array('key' => 'boekingsformulier tijdstip')); 

} 

if (!$firstApptype) {
  $keys = array_keys($timesPerApptype);
  $firstApptype = array_shift($keys);
}
$times = $timesPerApptype[$firstApptype];

$travelDates = array();
$selectedOption = '';
if (count($times) > 0) {
  foreach ($times as $time => $time_str) {
    $selected = '';
    if ($sf_params->get('t') == $time && $sf_params->get('d') == $date) {
      $selected = ' selected="selected"';
    }
    if ($sf_params->get('time') == $date.' '.$time) {
      $selected = ' selected="selected"';
    }
    
    if ($selected != '') $selectedOption = $date.' '.$time;
    
    if ($object->getTravel() && !isset($travelDates[$date])) {
      $names[$date] = 'Vandaag';
      $names[date('Y-m-d', strtotime('+1 day'))] = 'Morgen';
      $names[date('Y-m-d', strtotime('+2 day'))] = ucfirst(strftime('%A', strtotime('+2 day')));
      for ($d = 3; $d < 28; $d++) {
        $names[date('Y-m-d', strtotime('+'.$d.' day'))] = ucfirst(strftime('%A', strtotime('+'.$d.' day')));
      }
     // echo '<option'.$selected.' value="'.$time.'">'.$names[$date].'</option>'.PHP_EOL;
      $travelDates[$date] = true;
    }
    else if (!$object->getTravel()) {
     // echo '<option'.$selected.' value="'.$time.'">'.$time_str.'</option>'.PHP_EOL;
    }
  }
}

if ($selectedOption == '') {
  foreach ($timesPerApptype as $times) {
    if (count($times) > 0) {
      foreach ($times as $time => $timeStr) {
        $selected = '';
        $t = strtotime($time);
        $tt = date('H:i', $t);
        $td = date('Y-m-d', $t);
        if ($sf_params->get('t') == $tt && $sf_params->get('d') == $td) {
          $selected = ' selected="selected"';
        }
        
        if ($selected != '') $selectedOption = $td.' '.$tt;
      }
    }
  }
}
?>
              				
              				
<input type="hidden" name="time" id="time" onchange="fillVerticalLink();fillHorizontalLink();" value="">
<div id="time-list-rows">
<?php
$names[date('Y-m-d')] = 'Vandaag';
$names[date('Y-m-d', strtotime('+1 day'))] = 'Morgen';
$names[date('Y-m-d', strtotime('+2 day'))] = ucfirst(strftime('%A', strtotime('+2 day')));
for ($d = 3; $d < 28; $d++) {
  $names[date('Y-m-d', strtotime('+'.$d.' day'))] = ucfirst(strftime('%A', strtotime('+'.$d.' day')));
}

if ($object->getUnit() == 'week') {
  
  $dayofweek = date('N') - 1;
  $start = strtotime('-'.$dayofweek.' day');
?>
  <div class="time-list-row time-list-row-month" id="time-list-row">
    <table class="time-list">
      <tr>
<?php 
$week = 0;
$x = 0;
while ($x < 28) {
  $date = date('Y-m-d', strtotime('+'.$x.' day', $start));
  $valid = false;
  foreach ($times as $time => $time_str) {
    $test = date('Y-m-d', strtotime($time));
    if ($test == $date) {
      $valid = true;
      break;
    }
  }
  
  $txt = strftime('%A', strtotime($date)).'<br>'.date('j-n', strtotime($date));
   
  $selected = ' class="month';
  if ($valid){
    if ($sf_params->get('d') == $date) {
      $selected .= ' active';
    }
    $selected .= '"';
    
   
    echo '<td'.$selected.' id="date-'.$date.'" onclick="$$(\'.active\').each(function(e){e.removeClassName(\'active\')});$(\'time\').value=\''.$time.'\';this.addClassName(\'active\');showTimes(\''.$date.'\');">'.$txt.'</td>'.PHP_EOL;
  }
  else {
    echo '<td class="not-valid">'.$txt.'</td>'.PHP_EOL;
  }
  $x++;
  if ($x % 7 == 0) {
    echo '</tr><tr>';
  }
}
?>
    </tr>
    </table>
  </div>
  
  <div id="time-select-month" style="display:none;">
    <h5>Selecteer het tijdstip</h5>
    <?php echo zeus_edit_textpartial(null, '', '', array('key' => 'boekingsformulier tijdstip')); ?>
    <div class="form-row">
      <div class="form-label"><label for="time">Tijdstip</label></div>
      <select id="time-select" name="time-select" onchange="$('time').value=this.value;"><option /></select>
    </div>
  </div>
  <?php
}
else {
?>
<div class="time-list-row-head">
<?php foreach ($dates as $date) { ?>
  <?php echo $names[$date]; ?><br>
  <?php } ?>
</div>
<div class="time-list-row" id="time-list-row">
<?php foreach ($dates as $date) { ?>
    <table class="time-list">
      <tr>
<?php $tmp = false;
foreach ($times as $time => $time_str) {
  $test = date('Y-m-d', strtotime($time));
  if ($test == $date) {
    $tmp = true;
    $selected = '';
    $t = strtotime($time);
    $tt = date('H:i', $t);
    $td = date('Y-m-d', $t);
    if ($sf_params->get('t') == $tt && $sf_params->get('d') == $td) {
      $selected = ' class="active"';
    }
    if ($object->getTravel()) {
      $selected = $selected != '' ? ' class="travel active"' : ' class="travel"';
      echo '<td'.$selected.' onclick="$$(\'.active\').each(function(e){e.removeClassName(\'active\')});$(\'time\').value=\''.$time.'\';this.addClassName(\'active\');">Boek nu</td>'.PHP_EOL;
    }
    else {
      echo '<td'.$selected.' onclick="$$(\'.active\').each(function(e){e.removeClassName(\'active\')});$(\'time\').value=\''.$time.'\';this.addClassName(\'active\');">'.date('H:i', strtotime($time)).'</td>'.PHP_EOL;
    }
  }
}
if (!$tmp) {
  if ($object->getTravel()) {
    echo '<td class="no-times">geen beschikbaarheid</td>'.PHP_EOL;
  }
  else {
    echo '<td class="no-times">geen beschikbare tijden</td>'.PHP_EOL;
  }
}
?>
      </tr>
    </table>
<?php } ?>
  </div>
<?php } ?>
</div>
<p id="more-time-available">Er zijn meer tijden beschikbaar <img src="/zeusCore/img/icons/famfamfam/arrow_right.png" alt=""></p>

<?php if ($object->hasLinkVertical() || $object->hasLinkHorizontal()) { ?>
                      <h3>Kies eventuele meerdere afspraken</h3>
<?php } ?>
<?php if ($object->hasLinkVertical()) { ?>
              				<p>Geef hieronder aan of je meerdere dezelfde afspraken aansluitend aan elkaar wil reserven. Als je bijvoorbeeld een tennisbaan tweemaal achter elkaar wil reserveren kies je voor 2 afspraken.</p>
              				<div class="form-row">
              				  <div class="form-label"><label for="link-vertical">Aantal afspraken</label></div>
              				  <select name="link-vertical" id="link-vertical" style="width: 200px;" onchange="fillTimes($('apptype'));">

            				    </select>
              				</div>
<?php } else { ?>
<input type="hidden" name="link-vertical" id="link-vertical" value="1">
<?php } ?>

<?php if ($object->hasLinkHorizontal()) { ?>
              				<p>Geef hieronder aan of je meerdere dezelfde afspraken gelijktijdig wil reserveren. Als je bijvoorbeeld samen met je partner gelijktijdig dezelfde behandeling wil ondergaan kies je voor 2 personen.</p>
              				<div class="form-row">
              				  <div class="form-label"><label for="link-horizontal">Aantal personen</label></div>
              				  <select name="link-horizontal" id="link-horizontal" style="width: 200px;" onchange="fillTimes($('apptype'));">

            				    </select>
              				</div>
<?php } else { ?>
<input type="hidden" name="link-horizontal" id="link-horizontal" value="1">
<?php } ?>












              				</div>
              				<h3>Je persoonlijke gegevens</h3>
              				<div class="book-section-inner">
                				<?php echo zeus_edit_textpartial(null, '', '', array('key' => 'facebook boekingsformulier persoonlijke gegevens')); ?>
                				<div style="margin-bottom:10px;">
  
                				  <div class="form-row">
                  				  <div class="form-label"><label for="name">Naam</label></div>
                  				  <input type="text" name="name" id="name" value="<?php echo isset($booking['name']) ? $booking['name'] : '' ; ?>">
                  				</div>
                  				
                  				<div class="form-row">
                  				  <div class="form-label"><label for="email">E-mail adres</label></div>
                  				  <input type="text" name="email" id="email" value="<?php echo isset($booking['email']) ? $booking['email'] : ''; ?>">
                  				</div>
                				</div>
              				</div>
              				
              				
  <?php echo CapacityConnector::showBookingForm($object); ?>	
              				
              				
              				
              		    <h3>Blijf op de hoogte</h3>
              		    <div class="book-section-inner">
<?php echo zeus_edit_textpartial(null, '', '', array('key' => 'facebook boekingsformulier nieuwsbrief')); ?>
                				
              				  <div class="form-row form-checkbox">
                				  <input type="checkbox"<?php if ($sf_params->has('accept-newsletter')) echo ' checked="checked"'; ?> class="checkbox" name="accept-newsletter" id="accept-newsletter"> <label for="accept-newsletter">Ik wil de PlekjeVrij nieuwsbrief ontvangen.</label>
                				</div>
                				
                				<div class="form-row form-checkbox">
                				  <input type="checkbox"<?php if ($sf_params->has('accept-tagletter')) echo ' checked="checked"'; ?> class="checkbox" name="accept-tagletter" id="interval-chk" onchange="if(this.checked){$('interval-section-1').style.display='block';}else{$('interval-section-1').style.display='none';}"> <label for="interval-chk">Ik wil op de hoogte gehouden worden van soortgelijke last-minutes.</label>
                				</div>
                				
                				
                				
                				<div id="interval-section-1"<?php if (!$sf_params->has('accept-tagletter')) echo ' style="display: none;"'; ?>>
                  				<div class="form-row">
                  				<div style="margin-top: 5px;margin-left:20px;"><input type="checkbox" checked="checked" disabled="disabled" class="checkbox"> <label>Aanbiedingen die lijken op deze aanbieding</label></div>
                  				  <?php 
      $c = new Criteria; 
      $c->addAscendingOrderByColumn(CategoryI18NPeer::TITLE);
      $categories = CategoryPeer::doSelectWithI18N($c, 'nl_NL');
      $c->clear();
      $c->add(LastminuteCategoryPeer::LASTMINUTE_ID, $object->getId());
      $links = LastminuteCategoryPeer::doSelect($c);
      $categoryIds = array();
      foreach ($links as $link) {
        $categoryIds[] = $link->getCategoryId();
      }
      
      foreach ($categories as $category) { 
        $check = in_array($category->getId(), $categoryIds) ? ' checked="checked"' : '';
        ?>
      <div style="margin-top: 5px;margin-left:20px;"><input type="checkbox" <?php echo $check; ?> class="checkbox" value="<?php echo $category->getId(); ?>" id="category-<?php echo $category->getId(); ?>" name="category[]"> <label for="category-<?php echo $category->getId(); ?>"><?php echo $category->getTitle(); ?> aanbiedingen</label></div>
      <?php } ?>
                  				</div>
                  				
                  				<div class="form-row" style="margin-top: 10px;">
                  				  <div class="form-label" style="width:90px;"><label for="newsletter-frequency">Hoe vaak?</label></div>
                  				  <?php echo select_tag('newsletter-frequency', options_for_select(array(
                  				    1 => 'dagelijks',
                  				    7 => 'wekelijks',
                  				   /* 14 => '2-wekelijks',
                  				    30 => 'maandelijks',
                  				    91 => 'ieder kwartaal',
                  				    182 => 'ieder half jaar',
                  				    365 => 'jaarlijks'*/
                  				  
                  				  
                  				  ), $sf_params->get('newsletter-frequency'))); ?>
                  				</div>
                				</div>
				
		                    <div style="clear:both;"></div>
			
		                    </div>
		
		                    <?php if ($paymethod == 'Ideal') { ?>
		                    <h3>Blijf op de hoogte</h3>
              		      <div class="book-section-inner">
              		    <?php } ?>
              		    <?php if ($paymethod == 'Oprekening') { ?>
		                    <h3>Afronden</h3>
              		      <div class="book-section-inner">
              		      <p><strong>Let op:</strong> Je reserveert deze last-minute zonder online te betalen. Je korting wordt verrekend zodra je afrekent bij deze dienstverlener.</p>
              		    <?php } ?>
          				
          				      <?php echo $paymentForm; ?>
          				      <?php include_partial('lastminutes/calculatorSummary'.ucfirst($profilename), array('object' => $object, 'profilename' => $profilename)); ?>
          				      </div>
          				
          				
          				
              				
			
			
			
			<div class="form-button">
			  <button class="button-1-a"><span><?php echo $paymethod == 'Ideal' ? 'Start betaling!' : 'Reservering bevestigen'; ?></span></button>
			</div>
			
			<p><strong>Let op:</strong> Nadat je op 'Start betaling' hebt gedrukt wordt je doorgestuurd naar het betaalscherm van je bank. Klik nadat je de betaling hebt afgerond het scherm <strong>niet</strong> weg maar volg de instructies op het scherm. Pas als je op onze website het bedankt-scherm ziet is je afspraak definitief geboekt en betaald. Sluit de browser zich om een ander reden nadat je hebt betaald en kom je dus niet terug op deze website dan kan het tot een half uur duren voordat we je betaling verwerken. <strong>Doe dan niet nogmaals een boeking!</strong> Je betaling zal vanzelf verwerkt worden, hetzij later.</p>
			
<?php } // end $hasTimes ?>
	  </div>
              				
    
  </fieldset>
</form>
</div>
<div id="facebook-form-footer"></div>
<script type="text/javascript">
var times = <?php echo json_encode($timesPerApptype); ?>;
var prices = <?php echo json_encode($pricePerApptype); ?>;
var capacities = <?php echo json_encode($capacityPerApptype); ?>;
var travel = <?php echo json_encode($travelPerApptype); ?>;
var verticalLink = <?php echo json_encode($validVerticalPerApptypeTime); ?>;
var horizontalLink = <?php echo json_encode($validHorizontalPerApptypeTime); ?>;
var duration = <?php echo json_encode($durationPerApptype); ?>;
var selectedOption = '<?php echo isset($selectedOption) ? $selectedOption : ''; ?>';
var discount = <?php echo $object->getMaxdiscount() - $object->getFee(); ?>;

function formatCurrency(num) {
num = num.toString().replace(/\$|\,/g, '');
if (isNaN(num)) num = "0";
sign = (num == (num = Math.abs(num)));
num = Math.floor(num * 100 + 0.50000000001);
cents = num % 100;
num = Math.floor(num / 100).toString();
if (cents < 10) cents = "0" + cents;
for (var i = 0; i < Math.floor((num.length - (1 + i)) / 3); i++)
num = num.substring(0, num.length - (4 * i + 3)) + ',' + num.substring(num.length - (4 * i + 3));
return (((sign) ? '' : '-') + '€ ' + num + ',' + cents.toString().replace('00', '-'));
}

function fillVerticalLink()
{
  <?php if ($object->hasLinkVertical()) { ?>
  maxVertical = verticalLink[$('apptype').value][$('time').value];
  $('link-vertical').options.length = 0;
  for (i = 0; i < maxVertical; i++) {
    $('link-vertical').options[$('link-vertical').options.length] = new Option(i + 1, i + 1);
  }
  fillTimes($('apptype'));
  <?php } ?>
}

function fillHorizontalLink()
{
  <?php if ($object->hasLinkHorizontal()) { ?>
  maxHorizontal = horizontalLink[$('apptype').value][$('time').value];
  $('link-horizontal').options.length = 0;
  for (i = 0; i < maxHorizontal; i++) {
    $('link-horizontal').options[$('link-horizontal').options.length] = new Option(i + 1, i + 1);
  }
  fillTimes($('apptype'));
  <?php } ?>
}
<?php if ($sf_request->getMethod() == 'POST') { ?>
Event.observe(window, 'load', function() {
  $('apptype').value = '<?php echo $sf_params->get('apptype'); ?>';
  <?php if ($sf_params->get('time') != '') { ?>

  $('time-<?php echo str_replace(':','-',  str_replace(' ','-', $sf_params->get('time'))); ?>').click();
  <?php } ?>
});
<?php } else if ($sf_params->has('t') && $object->getUnit() != 'week') { ?>
Event.observe(window, 'load', function() {
  if ($('time-<?php echo $sf_params->get('d'); ?>-<?php echo substr($sf_params->get('t'), 0, 2); ?>-<?php echo substr($sf_params->get('t'), 2, 2) ?>')) {
    $('time-<?php echo $sf_params->get('d'); ?>-<?php echo substr($sf_params->get('t'), 0, 2); ?>-<?php echo substr($sf_params->get('t'), 2, 2) ?>').click();
  }
});
<?php } ?>

<?php
$tmp = array();
foreach ($timesPerApptype as $apptype_id => $times) {
  foreach ($times as $time => $time_str) {
    $tmp[$apptype_id][date('Y-m-d', strtotime($time))][] = date('H:i', strtotime($time));
  }
}
?>
var times_per_date = <?php echo json_encode($tmp); ?>;
var cdate = null;
var refill = false;
function showTimes(date, force)
{
  cdate = date;
  valid = false;
  if (times_per_date[$('apptype').value]) {
    
    v = '<?php echo substr($sf_params->get('t'),0,2).':'.substr($sf_params->get('t'),2,2); ?>';
    <?php if ($sf_request->getMethod() == 'POST') { ?>
    if (!refill && !force) {
      d = '<?php echo substr($sf_params->get('time'), 0, 10); ?>';
      t = '<?php echo str_replace('-', ':', substr($sf_params->get('time'), 11)); ?>';
      refill = true;
      $('date-'+d).addClassName('active');
      showTimes(d, true);
      $('time-select').value = t;
      $('time').value = '<?php echo $sf_params->get('time'); ?>';
    }
    <?php } ?>
    if (times_per_date[$('apptype').value][date]) {
      valid = true;
      if ($('time-select').selectedIndex > 0) {
        v = $('time-select').value;
      }
      $('time-select').options.length = 0;
      $(times_per_date[$('apptype').value][date]).each(function(s,i) {
        var opt = new Element('option');
        opt.value = s;
        opt.text = s;
        $('time-select').options[$('time-select').options.length] = opt;
      });
      $('time-select-month').show();
    }
    
    $('time-select').value = v;
    
    $('time-select').onchange = function(s)
    {
      $('time').value = cdate + ' ' + this.value;
    }
    $('time').value = date + ' ' + v;
  }
  
  <?php if ($sf_request->getMethod() == 'POST') { ?>
  $('time').value = '<?php echo $sf_params->get('time'); ?>';
  <?php } ?>
  
  if (!valid) {
    $('time-select-month').hide();
  }
}

<?php if ($object->getUnit() == 'week') { ?>
Event.observe(window, 'load', function() {
  showTimes('<?php echo $sf_params->get('d'); ?>');
});
<?php } ?>

<?php if ($object->hasLinkVertical()) { ?>
Event.observe(window, 'load', fillVerticalLink);
<?php } ?>

<?php if ($object->hasLinkHorizontal()) { ?>
Event.observe(window, 'load', fillHorizontalLink);
<?php } ?>
</script>