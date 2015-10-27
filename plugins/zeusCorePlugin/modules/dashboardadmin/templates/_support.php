<h2>Support</h2>
<?php if (count($tickets) > 0) { ?>
<p>Hieronder vindt je een overzicht van openstaande tickets:</p>
<ul>
<?php 
$c = new Criteria;
foreach ($tickets as $ticket) { 
  $c->clear();
  $c->add(RoutePeer::OBJECT, 'SupportTicket');
  $c->add(RoutePeer::OBJECT_ID, $ticket->getId());
  $route = RoutePeer::doSelectOne($c, Propel::getConnection('support'));
  
  ?>
  <li><a href="http://support.mizar-it.nl/nl_NL<?php echo $route->getUrl(); ?>?dli=<?php echo $dli; ?>"><?php echo $ticket->getTitle(); ?></a></li>
<?php } ?>
</ul>
<?php } else { ?>
<p><strong>Er zijn geen openstaande tickets gevonden.</strong></p>
<?php } ?>
<p style="margin-top:5px;"><a href="http://support.mizar-it.nl/nl_NL/tickets/algemeen/aanmaken?dli=<?php echo $dli; ?>">Maak een nieuw ticket aan</a></p>