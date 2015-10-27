                <div class="tinymce"><?php
if (isset($page) && $page instanceof Page) { 
  ?>
                  <div class="heading">
                    <h3><?php echo $page->getTitle(); ?></h3>
                  </div>
<?php 
  echo zeusTiny::parse_contents($page->getContent());
} ?>
</div>