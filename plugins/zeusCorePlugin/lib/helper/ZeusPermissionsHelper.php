<?php

function list_permissions_save($config = array())
{
  return new zeusRibbonButtonSave(array('form' => 'form-1'));
}

function list_permissions_form($config)
{
  use_helper('ZeusEdit');

  if (sfContext::getInstance()->getRequest()->hasParameter('message')) {
    echo '<ul class="form-messages"><li>'.sfContext::getInstance()->getRequest()->getParameter('message').'</li></ul>';
  }
  ?>
<form method="post" action="#" id="form-1">
  <fieldset><legend></legend>
  <div class="form-row">
    <div class="form-label" style="width:240px;"><label for="">Ik wil de rechten bekijken of aanpassen van</label></div>
    <select name="" id="" style="width:300px;">
      <optgroup label="Gebruiker">
<?php
$c = new Criteria;
$c->addAscendingOrderByColumn(UserPeer::TITLE);
$users = UserPeer::doSelect($c);
foreach ($users as $user) { ?>
        <option value="User:<?php echo $user->getId(); ?>"><?php echo $user->getTitle(); ?></option>
<?php } ?>
      </optgroup>
      <optgroup label="Groep">
<?php
$c = new Criteria;
$c->addAscendingOrderByColumn(UgroupPeer::TITLE);
$groups = UgroupPeer::doSelect($c);
foreach ($groups as $group) { ?>
        <option value="Ugroup:<?php echo $group->getId(); ?>"><?php echo $group->getTitle(); ?></option>
<?php } ?>
      </optgroup>
    </select>
  </div>
  
  <div class="form-row">
    <div class="form-label" style="width:240px;"><label for="">Ik wil de rechten bekijken of aanpassen voor</label></div>
    <select name="" id="" style="width:300px;">
      <option value="">De module 'Pagina's'</option>
      <option value="">De actie 'bewerken' van de module 'Pagina's'</option>
      <option value="">Het huidige object wat ik aan het bewerken ben</option>
    </select>
  </div>
  
  </fieldset>
</form>
<p><strong>Let op! Dit formulier werkt nog niet ;)</strong></p>
<table class="permission-list">
  <tr>
    <td><h3>Huidige rechten</h3></td>
    <th title="Aanmaken" class="perm"><img src="/zeusCore/img/icons/famfamfam/page_add.png" alt="Aanmaken"></th>
    <th title="Lezen" class="perm"><img src="/zeusCore/img/icons/famfamfam/page_edit.png" alt="Lezen"></th>
    <th title="Bewerken" class="perm"><img src="/zeusCore/img/icons/famfamfam/page_save.png" alt="Bewerken"></th>
    <th title="Verwijderen" class="perm"><img src="/zeusCore/img/icons/famfamfam/page_delete.png" alt="Verwijderen"></th>
    <th title="Publiceren" class="perm"><img src="/zeusCore/img/icons/famfamfam/page_lightning.png" alt="Publiceren"></th>
    <th title="Exporteren" class="perm"><img src="/zeusCore/img/icons/famfamfam/page_go.png" alt="Exporteren"></th>
    <th title="Kopiëren" class="perm"><img src="/zeusCore/img/icons/famfamfam/page_copy.png" alt="Kopiëren"></th>
  </tr>
  <tr>
    <td>Deze groep of gebruiker mag</td>
    <td class="perm"><img src="/zeusCore/img/checkbox/check-on.png" alt=""></td>
    <td class="perm"><img src="/zeusCore/img/checkbox/check-off.png" alt=""></td>
    <td class="perm"><img src="/zeusCore/img/checkbox/check-off.png" alt=""></td>
    <td class="perm"><img src="/zeusCore/img/checkbox/check-on.png" alt=""></td>
    <td class="perm"><img src="/zeusCore/img/checkbox/check-on.png" alt=""></td>
    <td class="perm"><img src="/zeusCore/img/checkbox/check-off.png" alt=""></td>
    <td class="perm"><img src="/zeusCore/img/checkbox/check-off.png" alt=""></td>
  </tr>
</table>

<br><br>

<table class="permission-list">
  <tr>
    <td><h3>Instellingen aanpassen</h3></td>
    <th class="perm"></th>
    <th title="Aanmaken" class="perm"><img src="/zeusCore/img/icons/famfamfam/page_add.png" alt="Aanmaken"></th>
    <th title="Lezen" class="perm"><img src="/zeusCore/img/icons/famfamfam/page_edit.png" alt="Lezen"></th>
    <th title="Bewerken" class="perm"><img src="/zeusCore/img/icons/famfamfam/page_save.png" alt="Bewerken"></th>
    <th title="Verwijderen" class="perm"><img src="/zeusCore/img/icons/famfamfam/page_delete.png" alt="Verwijderen"></th>
    <th title="Publiceren" class="perm"><img src="/zeusCore/img/icons/famfamfam/page_lightning.png" alt="Publiceren"></th>
    <th title="Exporteren" class="perm"><img src="/zeusCore/img/icons/famfamfam/page_go.png" alt="Exporteren"></th>
    <th title="Kopiëren" class="perm"><img src="/zeusCore/img/icons/famfamfam/page_copy.png" alt="Kopiëren"></th>
  </tr>
  <tr>
    <td>Rechten toewijzen voor de geselecteerd groep of gebruiker voor de module 'Pagina's'</td>
    <td class="perm" style="position:relative;">
      <img onmouseover="$('legenda-1').style.display='block';" style="cursor:pointer;" onmouseout="$('legenda-1').style.display='none';" src="/zeusCore/img/icons/famfamfam/help.png" alt="Legenda">
      <div id="legenda-1" class="legenda-text" style="display:none;position:absolute;left:370px;width: 350px;height:90px;background:#fff;">
        <p><strong>Legenda:</strong><br>
        <img src="/zeusCore/img/checkbox/check-on.png" alt=""> Deze privilege toestaan<br>
        <img src="/zeusCore/img/checkbox/check-off.png" alt=""> Deze privilege niet toestaan<br>
        <img src="/zeusCore/img/checkbox/check-ignore.png" alt=""> Bepalen of deze privilege is toegestaan uit onderliggende instellingsregels<br>
        </p>
      </div>
    </td>
    <td class="perm"><img src="/zeusCore/img/checkbox/check-ignore.png" alt=""></td>
    <td class="perm"><img src="/zeusCore/img/checkbox/check-ignore.png" alt=""></td>
    <td class="perm"><img src="/zeusCore/img/checkbox/check-ignore.png" alt=""></td>
    <td class="perm"><img src="/zeusCore/img/checkbox/check-ignore.png" alt=""></td>
    <td class="perm"><img src="/zeusCore/img/checkbox/check-ignore.png" alt=""></td>
    <td class="perm"><img src="/zeusCore/img/checkbox/check-ignore.png" alt=""></td>
    <td class="perm"><img src="/zeusCore/img/checkbox/check-ignore.png" alt=""></td>
  </tr>
</table>
<div class="form-button" style="width: 760px;margin: 5px 0;"><button type="button"><div>Toepassen</div></button></div>



<table class="permission-list">
  <tr>
    <td><h3>Totstandkoming van rechten</h3></td>
    <th class="perm"></th>
    <th title="Aanmaken" class="perm"><img src="/zeusCore/img/icons/famfamfam/page_add.png" alt="Aanmaken"></th>
    <th title="Lezen" class="perm"><img src="/zeusCore/img/icons/famfamfam/page_edit.png" alt="Lezen"></th>
    <th title="Bewerken" class="perm"><img src="/zeusCore/img/icons/famfamfam/page_save.png" alt="Bewerken"></th>
    <th title="Verwijderen" class="perm"><img src="/zeusCore/img/icons/famfamfam/page_delete.png" alt="Verwijderen"></th>
    <th title="Publiceren" class="perm"><img src="/zeusCore/img/icons/famfamfam/page_lightning.png" alt="Publiceren"></th>
    <th title="Exporteren" class="perm"><img src="/zeusCore/img/icons/famfamfam/page_go.png" alt="Exporteren"></th>
    <th title="Kopiëren" class="perm"><img src="/zeusCore/img/icons/famfamfam/page_copy.png" alt="Kopiëren"></th>
  </tr>
  <tr>
    <td>Verkregen uit rechten van huidige gebruiker voor actie pageadmin/edit</td>
    <td class="perm" style="position:relative;">
      <img onmouseover="$('legenda-2').style.display='block';" style="cursor:pointer;" onmouseout="$('legenda-2').style.display='none';" src="/zeusCore/img/icons/famfamfam/help.png" alt="Legenda">
      <div id="legenda-2" class="legenda-text" style="display:none;position:absolute;left:370px;width: 350px;height:105px;background:#fff;">
        <p><strong>Legenda:</strong><br>
        <img src="/zeusCore/img/checkbox/check-on.png" alt=""> In deze instellingsregel expliciet toegestaan<br>
        <img src="/zeusCore/img/checkbox/check-off.png" alt=""> In deze instellingsregel expliciet niet toegestaan<br>
        <img src="/zeusCore/img/checkbox/check-on-gray.png" alt=""> Toegestaan uit een onderliggende instellingsregel<br>
        <img src="/zeusCore/img/checkbox/check-off-gray.png" alt=""> Niet toegestaan uit een onderliggende instellingsregel<br>
        </p>
      </div>
    </td>
    <td class="perm"><img src="/zeusCore/img/checkbox/check-on-gray.png" alt=""></td>
    <td class="perm"><img src="/zeusCore/img/checkbox/check-off-gray.png" alt=""></td>
    <td class="perm"><img src="/zeusCore/img/checkbox/check-off-gray.png" alt=""></td>
    <td class="perm"><img src="/zeusCore/img/checkbox/check-on-gray.png" alt=""></td>
    <td class="perm"><img src="/zeusCore/img/checkbox/check-on-gray.png" alt=""></td>
    <td class="perm"><img src="/zeusCore/img/checkbox/check-off.png" alt=""></td>
    <td class="perm"><img src="/zeusCore/img/checkbox/check-off.png" alt=""></td>
  </tr>
  <tr>
    <td>Verkregen uit rechten van huidige gebruiker voor module pageadmin</td>
    <td class="perm"></td>
    <td class="perm"><img src="/zeusCore/img/checkbox/check-on-gray.png" alt=""></td>
    <td class="perm"><img src="/zeusCore/img/checkbox/check-off.png" alt=""></td>
    <td class="perm"><img src="/zeusCore/img/checkbox/check-off.png" alt=""></td>
    <td class="perm"><img src="/zeusCore/img/checkbox/check-on-gray.png" alt=""></td>
    <td class="perm"><img src="/zeusCore/img/checkbox/check-on-gray.png" alt=""></td>
    <td class="perm"><img src="/zeusCore/img/checkbox/check-on-gray.png" alt=""></td>
    <td class="perm"><img src="/zeusCore/img/checkbox/check-on-gray.png" alt=""></td>
  </tr>
  <tr>
    <td>Verkregen uit rechtn van groep Beheerders voor module pageadmin</td>
    <td class="perm"></td>
    <td class="perm"><img src="/zeusCore/img/checkbox/check-on.png" alt=""></td>
    <td class="perm"><img src="/zeusCore/img/checkbox/check-on-gray.png" alt=""></td>
    <td class="perm"><img src="/zeusCore/img/checkbox/check-on.png" alt=""></td>
    <td class="perm"><img src="/zeusCore/img/checkbox/check-on.png" alt=""></td>
    <td class="perm"><img src="/zeusCore/img/checkbox/check-on.png" alt=""></td>
    <td class="perm"><img src="/zeusCore/img/checkbox/check-on.png" alt=""></td>
    <td class="perm"><img src="/zeusCore/img/checkbox/check-on.png" alt=""></td>
  </tr>
  <tr>
    <td>Verkregen uit rechten van groep Algemeen voor module pageadmin</td>
    <td class="perm"></td>
    <td class="perm"><img src="/zeusCore/img/checkbox/check-off.png" alt=""></td>
    <td class="perm"><img src="/zeusCore/img/checkbox/check-on.png" alt=""></td>
    <td class="perm"><img src="/zeusCore/img/checkbox/check-off.png" alt=""></td>
    <td class="perm"><img src="/zeusCore/img/checkbox/check-off.png" alt=""></td>
    <td class="perm"><img src="/zeusCore/img/checkbox/check-off.png" alt=""></td>
    <td class="perm"><img src="/zeusCore/img/checkbox/check-off.png" alt=""></td>
    <td class="perm"><img src="/zeusCore/img/checkbox/check-off.png" alt=""></td>
  </tr>
</table>
<div class="help-text">
<p>De standaard rechten voor de geselecteerde groep of gebruiker is tot stand gekomen door de bovenstaande instellingen. De rechten worden van onder naar boven bepaald, dus bij conflicterende instellingen wordt de bovenste genomen. Zo kan iemand in een bepaalde groep geen leesrechten hebben, maar doordat deze gebruiker ook aan de andere hogere groep zit en deze groep wél leesrechten heeft alsnog leesrechten krijgen.</p>
</div>  
<br><br><br>
  <?php
}



function zeus_permissions($object = null, $config = array())
{
  $response = sfContext::getInstance()->getResponse();
  $response->addStylesheet('/zeusCore/js/extjs/resources/css/ext-all.css');
	$response->addStylesheet('/zeusCore/js/extjs/resources/css/xtheme-slate.css');
	$response->addJavascript('/zeusCore/js/extjs/adapter/ext/ext-base.js');
	$response->addJavascript('/zeusCore/js/extjs/ext-all.js');
	$response->addJavascript('/zeusCore/js/extjs/src/locale/ext-lang-nl.js');
	$response->addJavascript('/zeusCore/js/extjs/examples/ux/SlidingPager.js');
	$response->addJavascript('/zeusCore/js/extjs/examples/ux/SliderTip.js');
	$response->addJavascript('/zeusCore/js/extjs/examples/ux/PanelResizer.js');
	$response->addJavascript('/zeusCore/js/extjs/examples/ux/PagingMemoryProxy.js');
  $response->addJavascript('/zeusCore/js/zeus-permissions/zeus-permissions.js');
  
  ob_start();
  ?>
  <div id="permissions-win" class="x-hidden">
    <div class="x-window-header">Rechten</div>
    <div id="permissions-tabs">
      <div id="zeus-permissions" style="padding:10px;overflow:scroll;width:790px;height:440px;" class="x-tab" title="Rechten instellen">
        <p style="margin-top:50px;text-align:center;"><img src="/zeusCore/img/ajax-loader.gif" alt="Bezig met laden..."></p>
      </div>
    </div>
  </div>
<script type="text/javascript">
var zpermissions = new zeusPermissions({});


</script>
  <?php
  
  return ob_get_clean();
}