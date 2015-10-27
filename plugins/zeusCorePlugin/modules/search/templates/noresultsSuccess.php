<h1><?php echo __('Zoeken'); ?></h1>
<p><?php echo __('Er zijn geen resultaten gevonden'); ?>.</p>
<?php if ($suggested) { ?>
        <p class="suggestion"><?php echo __('Bedoelde u misschien'); ?> <a href="<?php echo url_for('search/index?query='.strip_tags($suggested)) ?>">'<?php echo $suggested ?>'</a>?</p>
<?php } ?>