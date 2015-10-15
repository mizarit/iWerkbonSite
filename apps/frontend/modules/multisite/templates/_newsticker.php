<?php if (count($objects) > 0) { ?>
<ul id="news-ticker">
<?php
for($endless = 0; $endless < 30; $endless++) {
foreach ($objects as $object) {
  // portal is not multilocal, hack for now
 $object->setCulture($sf_user->getCulture());
  ?>
	<li><a href="http://<?php echo sfConfig::get('app_multisite_portal'); ?><?php echo route_for($object, true, Propel::getConnection('ticker')); ?>"><?php echo $object->getTitle(); ?></a></li>
<? } } ?>
</ul>
<script type="text/javascript">
var news_ticker_x = 0;
var news_ticker_speed = 10;
var news_ticker_width = $('news-ticker').getWidth();
var news_ticker_scrolling = true;

function news_ticker_scroll()
{
  if (news_ticker_scrolling) {
    news_ticker_x++;
    if (news_ticker_x > news_ticker_width) {
      news_ticker_x = 0;
    }
    
    $('news-ticker').style.left = (news_ticker_x * -1)+'px';
  }
  
  setTimeout(news_ticker_scroll, news_ticker_speed);
}

Event.observe(window, 'load', function() {
  
  if( window.innerHeight && window.scrollMaxY ) // Firefox 
  {
    pageHeight = window.innerHeight + window.scrollMaxY;
  }
  else if( document.body.scrollHeight > document.body.offsetHeight ) // all but Explorer Mac
  {
    pageHeight = document.body.scrollHeight;
  }
  else // works in Explorer 6 Strict, Mozilla (not FF) and Safari
  { 
    pageHeight = document.body.offsetHeight + document.body.offsetTop; 
  }
  
  if (document.viewport.getHeight() < pageHeight) {
    $('container-footer').style.top = pageHeight + 'px';
    if (/MSIE (\d+\.\d+);/.test(navigator.userAgent)){
      $('container-footer').style.marginTop = ($('container-footer').style.height + 160) + 'px';
    }
    else {
      $('container-footer').style.marginTop = $('container-footer').style.height + 'px';
    }
  }
  
    
  $$('#news-ticker li').each(function(s,i) {
    Event.observe(s, 'mouseover', function() {
      news_ticker_scrolling = false;
    });
    
    Event.observe(s, 'mouseout', function() {
      news_ticker_scrolling = true;
    });
    
  });
  setTimeout(news_ticker_scroll, news_ticker_speed);
  
});

</script>
<?php } ?>