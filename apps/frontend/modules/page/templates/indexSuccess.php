                <div class="tinymce"><?php
if (isset($page) && $page instanceof Page) { 
  ?>
                  <div class="heading">
                    <h1><?php echo $page->getTitle(); ?></h1>
                  </div>
                  <div style="padding: 10px;">
<?php echo zeusTiny::parse_contents($page->getContent()); ?>
                  </div>
<?php } ?>
</div>