        <h1><?php echo __('Zoeken'); ?></h1>
        <p><?php echo __('Er'); ?> <?php echo $results['total_hits'] == 1 ? __('is') : __('zijn'); ?> <?php echo $results['total_hits']; ?> <?php echo $results['total_hits'] == 1 ? __('resultaat') : __('resultaten'); ?> <?php echo __('gevonden voor de zoekopdracht'); ?> '<?php echo $sf_params->get('query'); ?>'.</p>
<?php if ($suggested) { ?>
        <p class="suggestion"><?php echo __('Bedoelde u misschien'); ?> <a href="<?php echo url_for('search/index?query='.strip_tags($suggested)) ?>">'<?php echo $suggested ?>'</a>?</p>
<?php } ?>
        <ul class="news-shortlist">
<?php foreach ($results['hits'] as $hit) { 
  $title = $hit['title'];
  $peer = $hit['object'].'Peer';
  $object = call_user_func_array(array($peer, 'retrieveByPk'), array($hit['id']));
  if ($object) {
    $title = $object->getTitle();
  }
  ?>
          <li><a href="<?php echo route_for($hit['object'].':'.$hit['id']); ?>"><?php echo $title; ?></a><span style="width:40px;"><?php echo number_format($hit['score']*100, 1) ?>%</span><br><em><?php echo zeusTools::smartText(str_replace('%%%FORM%%%', '', strip_tags($object->getContent())), 150); ?></em></li>
<?php } ?>
        </ul>