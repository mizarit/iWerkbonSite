<ul>
  <li class="home"><i class="fa fa-home"></i></li>
  <?php foreach ($sf_user->getAttribute('crumblepath') as $key => $value) { ?>
  <li><?php if (is_numeric($key)) {
  echo $value;
  } else { ?>
    <a href="<?php echo url_for($key); ?>"><?php echo $value; ?></a>
<?php
} ?></li>
<?php } ?>
</ul>