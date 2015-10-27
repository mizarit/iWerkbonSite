<h2>Laatste acties</h2>
<?php if (count($changes) > 0) { ?>
<p>Hieronder vindt je een overzicht van de meest recente acties die je genomen hebt in het CMS:</p>
<ul>
<?php foreach ($changes as $change) { ?>
  <li><?php if ($change->getActionurl() != '') echo '<a href="'. str_replace('/backend_dev.php', '', $change->getActionurl()).'">'; ?><?php echo $change->getTitle(); ?><?php if ($change->getActionurl() != '') echo '</a>'; ?></li>
<?php } ?>
</ul>
<?php } else { ?>
<p><strong>Geen recente acties gevonden.</strong></p>
<?php } ?>