<?php if (count($items) > 0) { ?>
<ul>
<?php foreach ($items as $item) { ?>
  				<li><a href="<?php echo route_for($item); ?>"><?php echo $item->getTitle(); ?></a></li>
<?php } ?>
</ul>
<?php } else { ?>
<p>Er zijn momenteel geen mededelingen.</p>
<?php } ?>