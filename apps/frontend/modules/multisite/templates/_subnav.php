  <div id="sub-nav-container">
    <ul id="sub-nav">
      <li><a href="http://<?php echo sfConfig::get('app_multisite_portal').'/'.$sf_user->getCulture(); ?>/home">NarComm</a></li>
      <li><a href="http://<?php echo sfConfig::get('app_multisite_portal').'/'.$sf_user->getCulture(); ?>/nieuws"><?php echo __('nieuws'); ?></a></li>
      <li><a href="http://<?php echo sfConfig::get('app_multisite_portal').'/'.$sf_user->getCulture(); ?>/colofon"><?php echo __('colofon'); ?></a></li>
      <li class="last-item"><a href="http://<?php echo sfConfig::get('app_multisite_portal').'/'.$sf_user->getCulture(); ?>/formulier/contact"><?php echo __('contact'); ?></a></li>
      </ul>
    </div>  