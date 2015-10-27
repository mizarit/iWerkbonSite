<h2>Favoriete acties</h2>
<?php if (count($favorites) > 0) { ?>
<p>Hieronder vindt je een overzicht van je favoriete acties in het CMS:</p>
<ul>
<?php foreach ($favorites as $favorite) { ?>
  <li><a href="<?php echo url_for($favorite->getActionurl()); ?>"><?php echo $favorite->getTitle(); ?></a></li>
<?php } ?>
</ul>
<?php } else { ?>
<p><strong>Er zijn geen favoriete acties gevonden.</strong></p>
<?php } ?>