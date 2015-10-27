<div class="news-item tinymce">
<h1 class="news-title"><?php echo $object->getTitle(); ?></h1>
<p class="news-date"><?php echo date('d-m-Y', strtotime($object->getDate())); ?></p>
<?php echo $object->getContent() ?>
</div>