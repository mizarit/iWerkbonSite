<ul id="deals">
<?php $js = ''; // for maps ?>

<li id="detail-view">

<button class="button-1-b" onclick="window.location.href='<?php echo url_for('@facebook_app'); ?>';"><span>Vorige pagina</span></button>

<h1><?php echo $object->getTitle(); ?></h1>
<?php
$parts = explode('/', route_for($object));
$deal_name = array_pop($parts);
$company_name = array_pop($parts);
$deal_route = '/facebook/'.$company_name.'/'.$deal_name;
?>
      
  <div class="col-container">
    <div class="col-1">
      <div class="image-container">
        <img src="<?php echo $img; ?>" alt="">
        
        <div id="photo-container">
				  <a rel="Fotos[Foto-impressies]" title="Foto-impressie" href="<?php echo $object->getMainImage(); ?>" class="lightwindow"><img id="photo-large" src="<?php echo zeusImages::getPresentation($object->getMainImage(), array('resize_method' => zeusImages::RESIZE_CHOP, 'width' => 300, 'height' => 150)); ?>" alt=""></a>
				  <img id="photo-dummy" src="<?php echo $object->getMainImage(); ?>" alt="" style="display: none;">
				  
				  </div>
				<ul id="photo-list">
				<?php 
				$images = $object->getImages(); 
				$first = true;
				$large = array();
				$images = array_slice($images,0,6);
				foreach ($images as $key => $image) {
				  $image_large = zeusImages::getPresentation($image, array('resize_method' => zeusImages::RESIZE_CHOP, 'width' => 800));
				  $cls = array();
				  if ($first) $cls[] = 'active-item';
				  if (($key + 1) % 3 == 0 && $key > 0) $cls[] = 'last-item';
				  $large[$key] = zeusImages::getPresentation($image, array('resize_method' => zeusImages::RESIZE_CHOP, 'width' => 300, 'height' => 150));
				  $first = false;
				  echo '<li class="'.implode(' ',$cls).'" id="photo-select-'.$key.'"><div class="overlay"></div><a rel="Fotos[Foto-impressies]" title="Foto-impressie" href="'.$image_large.'" class="lightwindow"><img onclick="selectPhoto('.$key.');" src="'.zeusImages::getPresentation($image, array('resize_method' => zeusImages::RESIZE_CHOP, 'width' => 95, 'height' => 59)).'" alt=""></a></li>';
				}
				?>
				</ul>
          				
          				
        <p class="city"><?php echo $object->getCompany()->getCity(); ?></p>
      </div> 
      <div style="clear:both;"></div>
      <?php
      $content = $object->getDescription();
      $lines = explode('</p>', $content);
      $first = array_shift($lines);
      $firstLine = $first.'</p>';
      $description = implode('</p>', $lines);
      ?>
      <div id="text-intro-<?php echo $object->getId(); ?>">
      <?php //echo $firstLine; ?>
      </div>
    </div>
    <div class="col-2">
    <?php echo $object->getPricingHTMLFacebook($deal_route); ?>
    
    <div id="social-box">
    <?php echo $app['all']['facebook']['appid']; ?>
    <div class="fb-like" data-href="http://plekjevrij.nl<?php echo route_for($object); ?>" data-app-id="<?php echo $app['all']['facebook']['appid']; ?>" data-send="false" data-width="120" data-show-faces="true" data-action="like"></div>
    </div>
                    
    </div>
    <div class="col-3">
    <?php echo $object->getTimelistHTMLFacebook($deal_route); ?>
    </div>
  </div>
  <div style="clear:both;"></div>
  
  
  <div class="text-container" id="text-<?php echo $object->getId(); ?>">
    <div>
      <div class="col-container">
        <div class="col-1">
          <?php echo $object->getDescription(); ?>
        </div>
        <div class="col-2">
          <div class="fb-activity-box">
          <div class="fb-facepile" data-href="http://plekjevrij.nl<?php echo route_for($object); ?>" data-app-id="<?php echo $app['all']['facebook']['appid']; ?>" data-max-rows="3" data-width="200"></div>
          </div>
        </div>
        <div class="col-3">
      <?php 
    $company = $object->getCompany(); 
    ?>
    
      <div style="clear:both;"></div>
      <div class="company-address">
        <h2><?php echo $company->getTitle(); ?></h2>
        <p><?php echo $company->getAddress(); ?><br><?php $company->getZipcode(); ?> <?php echo $company->getCity(); ?><br><a href="mailto:<?php echo $company->getEmail(); ?>"><?php echo $company->getEmail(); ?></a></p>
      </div>

      <div id="map-canvas-<?php echo $object->getId(); ?>" style="height: 170px;width: 170px;margin-bottom:20px;"></div>
      
    <?php ob_start(); ?>
<?php if (!$sf_request->isXmlHttpRequest()) { ?>
var map<?php echo $object->getId(); ?> = null;
var marker<?php echo $object->getId(); ?> = null;
<?php } ?>
initMap<?php echo $object->getId(); ?> = function()
{
  map<?php echo $object->getId(); ?> = new google.maps.Map(document.getElementById('map-canvas-<?php echo $object->getId(); ?>'), {
    zoom: 12,
    center: new google.maps.LatLng(<?php echo $company->getLatitude(); ?>,<?php echo $company->getLongitude(); ?>),
    mapTypeId: google.maps.MapTypeId.ROADMAP
  });
  
  marker<?php echo $object->getId(); ?> = new google.maps.Marker({
    position: map<?php echo $object->getId(); ?>.getCenter(),
    map: map<?php echo $object->getId(); ?>
  });
}

<?php if (!$sf_request->isXmlHttpRequest()) { ?>
google.maps.event.addDomListener(window, 'load', initMap<?php echo $object->getId(); ?>);
<?php } else { ?>
initMap<?php echo $object->getId(); ?>();
<?php } ?>
<?php $js .= ob_get_clean(); ?>
        </div>
      </div>
    </div>
  </div>  
  <div style="clear:both;"></div>
      
      
  <div style="height: 24px;"></div>
  <div class="deal-footer"></div>
  
  


</li>


</ul>
<?php if (!$sf_request->isXmlHttpRequest()) { ?>
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>
<script type="text/javascript">
var largeImages = <?php echo json_encode($large); ?>;
function selectPhoto(i)
{
  /*var ci = i;
  $('photo-large').src = largeImages[i];
  $('photo-large').parentNode.href = largeImages[i];
  $$('#photo-list li').each(function(s,i) {
    if (i == ci) {
      s.addClassName('active-item');
    }
    else {
      s.removeClassName('active-item');
    }
  });*/
}

function showMore(which, el)
{
  var wm = which;
  $('text-intro-'+which).hide();
  Effect.SlideDown('text-'+which, {
    duration: 0.4,
    afterFinish: function()
    {
      eval('google.maps.event.trigger(map'+wm+', \'resize\');');
      eval('map'+wm+'.setZoom(map'+wm+'.getZoom() );');
      eval('map'+wm+'.setCenter(marker'+wm+'.getPosition());');
    }
    
  });
  $(el).parentNode.hide();
}

Event.observe(window, 'load', nextPhotox);

var currentPhoto = 0;
var allPhotos = <?php echo count($images); ?>;
var photoTimeout = 3000;

function nextPhotox()
{
  nextPhoto = currentPhoto + 1;
  if (nextPhoto == allPhotos) nextPhoto = 0;
  
  // copy current photo to dummy photo, then change main photo, and fade both
  $('photo-dummy').src = $('photo-large').src;
  $('photo-dummy').show();
  $('photo-large').src = largeImages[nextPhoto];
  
  Effect.Appear('photo-large');
  Effect.Fade('photo-dummy');
  
  $$('#photo-list li').each(function(s,i) {
    if (i == nextPhoto) {
      s.addClassName('active-item');
    }
    else {
      s.removeClassName('active-item');
    }
  });
  
  currentPhoto = nextPhoto;
  
  setTimeout(function() { nextPhotox(); }, photoTimeout);
}

var isLoading = false;
var hasMore = true;

Event.observe(window, 'scroll', function(el, o) {
  var body = document.body,
    html = document.documentElement;

  var height = Math.max( body.scrollHeight, body.offsetHeight, 
                       html.clientHeight, html.scrollHeight, html.offsetHeight );
  if(document.viewport.getDimensions().height + document.viewport.getScrollOffsets().top == height) {
    if (!isLoading && hasMore) {
      var div = new Element('div');
      var img = new Element('img');
      img.src = '/zeusCore/img/ajax-loader.gif';
      div.insert(img);
      $(body).insert(div);
      
      new Ajax.Updater(div, '<?php echo url_for('@facebook_app'); ?>', {
        parameters: {
        },
        evalScripts: true,
        onSuccess: function(transport) {
          isLoading = false;
          if(transport.responseText == '') {
            hasMore = false;
          }
        }
      });
      isLoading = true;
    }
  }
});
</script>
<?php } ?>
<script type="text/javascript">
<?php echo $js; ?>
</script>