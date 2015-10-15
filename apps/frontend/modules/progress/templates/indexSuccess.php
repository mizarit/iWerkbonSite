<h1><?php echo strip_tags(str_replace('<br>', ' ', $challenge->getTitle())); ?></h1>
<?php
// find first user of the challenge
$c = new Criteria;
$c->add(ChallengeUserPeer::CHALLENGE_ID, $challenge->getId());
$users = ChallengeUserPeer::doSelect($c);
foreach ($users as $user) {
  $user = UsersPeer::retrieveByPk($user->getUserId());
  if ($user->getXid() != '') {
    break;
  }
}
?>
<iframe src="https://app.health-challenge.nl?ju=<?php echo $user->getXid(); ?>&guest=true" width="100%;" height="2000px;" frameborder="0"></iframe>