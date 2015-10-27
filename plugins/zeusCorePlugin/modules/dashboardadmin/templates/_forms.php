<h2>Ingevulde formulieren</h2>
<?php if (count($forms) > 0) { ?>
<p>Hieronder vindt je een overzicht van recent ingevulde formulieren:</p>
<ul>
<?php foreach ($forms as $form) { ?>
  <li><a href="<?php echo url_for('formadmin/edit?id='.$form->getId()); ?>"><?php echo ucfirst($form->getTitle()); ?>formulier op <?php echo date('d-m-Y', strtotime($form->getDate())); ?> door <?php echo $form->getName(); ?></a></li>
<?php } ?>
</ul>
<?php } else { ?>
<p><strong>Er zijn geen ingevulde formulieren gevonden.</strong></p>
<?php } ?>