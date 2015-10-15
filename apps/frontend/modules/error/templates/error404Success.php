<!DOCTYPE html>
<html lang="en">
<head>
  <link href="/css/error.css" rel="stylesheet" type="text/css">
</head>
<body >
<div id="container">
  <div id="stage" class="stage">
    <div style="background-position: 2838px 0px;" id="space" class="stage"></div>
    <div style="top: 145.68px; left: 112.777px; background-position: 0px 0%;" id="astronaut" class="stage">
      <div id="text_1">Houston,<br>we have a<br>problem!</div>
      <div id="text_2">Oh oh!</div>
      <div id="text_3">Onze servers hebben <br> momenteel te veel<br> bezoekers</div>
      <div id="text_4">Probeer het later nog eens...</div>
    </div>
  </div>
</div>
<script src="/js/jquery-1.js" type="text/javascript" charset="utf-8"></script>
<script src="/js/sprite.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" charset="utf-8">
  (function($) {
    $(document).ready(function() {
      $('#astronaut')
          .sprite({fps: 30, no_of_frames: 1})
          .spRandom({top: 130, bottom: 200, left: 30, right: 200});
      $('#space').pan({fps: 40, speed: 3, dir: 'right', depth: 50});
    });
  })(jQuery);
</script>
</body> 
<?php exit; ?>