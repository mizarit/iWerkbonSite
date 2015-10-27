<script type="text/javascript">
  var settings_data_url = '<?php echo url_for('admin/settingsData'); ?>';
  var settings_ajax_url = '<?php echo url_for('admin/settingsAjax'); ?>';
</script>
<div id="admin-content">
  <div class="settings-panel" id="settings-invoices" style="display: none;">
<form action="#" method="post">
  <fieldset>
    <legend>Settings form</legend>
    <?php include_component('admin', 'text', array('key' => 'settings-invoice-help-title', 'text' => 'Factuurgegevens', 'tag' => 'h1')); ?>
    <?php include_component('admin', 'text', array('key' => 'settings-invoice-help', 'text' => 'De gegevens die je hieronder invult worden gebruikt op de facturen die je naar je klanten stuurt.')); ?>

    <div class="form-row">
      <div class="form-label"><label for="companyname">Bedrijfsnaam</label></div>
      <input type="text" id="companyname" name="companyname" value="<?php echo $company->getSetting('companyname'); ?>">
    </div>
    <div class="form-row">
      <div class="form-label"><label for="kvk">KvK nummer</label></div>
      <input type="text" id="kvk" name="kvk" value="<?php echo $company->getSetting('kvk'); ?>" style="width:6em;">
    </div>
    <div class="form-row">
      <div class="form-label"><label for="btw">BTW-nummer</label></div>
      <input type="text" id="btw" name="btw" value="<?php echo $company->getSetting('btw'); ?>" style="width:8em;">
    </div>
    <div class="form-row">
      <div class="form-label"><label for="iban">IBAN nummer</label></div>
      <input type="text" id="iban" name="iban" value="<?php echo $company->getSetting('iban'); ?>" style="width:12em;">
    </div>
    <div class="form-row">
      <div class="form-label"><label for="iban_name">IBAN rekeninghouder</label></div>
      <input type="text" id="iban_name" name="iban_name" value="<?php echo $company->getSetting('iban_name'); ?>">
    </div>
    <div class="form-row">
      <div class="form-label"><label for="site">Website</label></div>
      <input type="text" id="site" name="site" value="<?php echo $company->getSetting('site'); ?>">
    </div>
    <div class="form-row">
      <div class="form-label"><label for="email">E-mail adres</label></div>
      <input type="text" id="email" name="email" value="<?php echo $company->getSetting('email'); ?>">
    </div>

    <?php include_component('admin', 'text', array('key' => 'settings-invoice-help-title2', 'text' => 'Hoe ziet de factuur eruit?', 'tag' => 'h2')); ?>
    <?php include_component('admin', 'text', array('key' => 'settings-invoice-help2', 'text' => '...')); ?>

    <div class="form-row">
      <div class="form-label"><label for="color1">Kleur 1</label></div>
      <input type="text" id="color1" name="color1" value="<?php echo $company->getSetting('color1'); ?>" style="width:120px;float:left;"> <div id="swatch-color1" class="swatch"></div>
      <div style="clear:both;"></div>
    </div>

    <div class="form-row">
      <div class="form-label"><label for="color2">Kleur 2</label></div>
      <input type="text" id="color2" name="color2" value="<?php echo $company->getSetting('color2'); ?>" style="width:120px;float:left;"> <div id="swatch-color2" class="swatch"></div>
      <div style="clear:both;"></div>
    </div>

    <script type="text/javascript">
      Event.observe(window, 'load', function() {
        new Control.ColorPicker('color1', {IMAGE_BASE: '/js/colorpickerjs-1.0/img/', swatch: 'swatch-color1'});
        new Control.ColorPicker('color2', {IMAGE_BASE: '/js/colorpickerjs-1.0/img/', swatch: 'swatch-color2'});

      });
    </script>

    <div class="form-row">
      <div class="form-label"><label for="logo">Logo</label></div>
    <?php
    $value = '/img/logo/'.$company->getSetting('logo');
    $cfg = array();
    ?>
    <input type="hidden" id="logo-fld" name="logo" value="<?php echo $value; ?>">
    <div class="image-preview" id="image-preview-logo" style="float: left; margin-right: 5px;">
      <img id="image-preview-img" src="<?php echo zeusImages::getPresentation($value, array( 'width' => 240, 'height' => 160, 'resize_method' => zeusImages::RESIZE_CHOP)); ?>" alt="">
      <span onclick="$('image-preview-logo').innerHTML = '';$('logo').value = '';" title="Afbeelding verwijderen" class="fa fa-remove image-remove"></span>
      <div style="margin-left: 150px;">
            <div id="upload-button"></div>
            <button id="annuleer-button" type="button" onclick="swfu.cancelQueue();" disabled="disabled" style="display: none;"><div>Annuleren</div></button>
            <div class="simple" id="upload-progress" style="width: 100px;"></div>
            <div id="upload-status"></div>
      </div>
      <div style="clear:both;"></div>
    </div>
  </fieldset>
</form>
    <script type="text/javascript">
      var swfu;

      Event.observe(window, 'load', function() {
        var settings = {
          flash_url : "/zeusCore/js/swfupload/swfupload.swf",
          upload_url: "<?php echo url_for('admin/upload') ?>",
          post_params: {"PHPSESSID" : "<?php echo session_id(); ?>"},
          file_size_limit : "100 MB",
          file_types : "*.*",
          file_types_description : "Alle bestanden",
          file_upload_limit : 100,
          file_queue_limit : 0,
          custom_settings : {
            progressTarget : "upload-progress",
            cancelButtonId : "annuleer-button",
            updateUrl: "<?php echo url_for('admin/update') ?>",
            fileContainer: "files-container",
            fileField: "logo-fld"
          },
          debug: false,

          button_image_url: "/zeusCore/js/swfupload/upload-button.png",
          button_width: "53",
          button_height: "24",
          button_placeholder_id: "upload-button",

          file_queued_handler : fileQueued,
          file_queue_error_handler : fileQueueError,
          file_dialog_complete_handler : fileDialogComplete,
          upload_start_handler : uploadStart,
          upload_progress_handler : uploadProgress,
          upload_error_handler : uploadError,
          upload_success_handler : function(file, serverData) {
            try {
              var progress = new FileProgress(file, this.customSettings.progressTarget);
              progress.setComplete();
              progress.setStatus("Gereed.");
              progress.toggleCancel(false);

            } catch (ex) {
              //this.debug(ex);
            }
            $('image-preview-img').src = serverData;
            $('logo-fld').value = serverData;
            swfu.startUpload();
          },
          upload_complete_handler : uploadComplete,
          queue_complete_handler : queueComplete  // Queue plugin event
        };

        swfu = new SWFUpload(settings);

      });
</script>
  </div>
  <div class="settings-panel" id="settings-general">
    <form action="#" method="post">
      <fieldset>
        <legend>Settings form</legend>

        <?php include_component('admin', 'text', array('key' => 'settings-general-help-title1a', 'text' => 'Algemene instellingen', 'tag' => 'h1')); ?>
        <?php include_component('admin', 'text', array('key' => 'settings-general-help-title1', 'text' => 'Bedrijfsgegevens', 'tag' => 'h2')); ?>
        <?php include_component('admin', 'text', array('key' => 'settings-general-help1', 'text' => '...')); ?>

        <div class="form-row">
          <div class="form-label"><label for="companyname2">Bedrijfsnaam</label></div>
          <input type="text" id="companyname2" name="companyname2" value="<?php echo $company->getSetting('companyname'); ?>">
        </div>
        <div class="form-row">
          <div class="form-label"><label for="address">Adres</label></div>
          <input type="text" id="address" name="address" value="<?php echo $company->getAddress()->getAddress(); ?>" style="width:11em;">
        </div>
        <div class="form-row">
          <div class="form-label"><label for="zipcode">Postcode & plaats</label></div>
          <input type="text" id="zipcode" name="zipcode" value="<?php echo $company->getAddress()->getZipcode(); ?>" style="width:4em;"> <input type="text" id="city" name="city" value="<?php echo $company->getAddress()->getCity(); ?>" style="width:12em;">
        </div>
        <div class="form-row">
          <div class="form-label"><label for="phone1">Telefoonnummer</label></div>
          <input type="text" id="phone1" name="phone1" value="<?php echo $company->getPhone(); ?>" style="width:8em;">
        </div>

        <?php include_component('admin', 'text', array('key' => 'settings-general-help-title2', 'text' => 'Afzender van e-mails', 'tag' => 'h2')); ?>
        <?php include_component('admin', 'text', array('key' => 'settings-general-help2', 'text' => '...')); ?>

        <div class="form-row">
          <div class="form-label"><label for="sender_name">Afzendernaam</label></div>
          <input type="text" id="sender_name" name="sender_name" value="<?php echo $company->getSetting('sender_name'); ?>">
        </div>
        <div class="form-row">
          <div class="form-label"><label for="sender_email">E-mail adres</label></div>
          <input type="text" id="sender_email" name="sender_email" value="<?php echo $company->getSetting('sender_email'); ?>">
        </div>

        <?php include_component('admin', 'text', array('key' => 'settings-general-help-title3', 'text' => 'Overige instellingen', 'tag' => 'h2')); ?>
        <?php include_component('admin', 'text', array('key' => 'settings-general-help3', 'text' => '...')); ?>

        <div class="form-row">
          <div class="form-label"><label for="admin_email">Admin e-mail adres</label></div>
          <input type="text" id="admin_email" name="admin_email" value="<?php echo $company->getSetting('admin_email'); ?>">
        </div>
        <div class="form-row">
          <div class="form-label"><label for="invoicedays">Betaaltermijn facturen</label></div>
          <input type="text" id="invoicedays" name="invoicedays" value="<?php echo $company->getSetting('invoicedays'); ?>" style="width:3em;">
        </div>

        <?php include_component('admin', 'text', array('key' => 'settings-general-help-title4', 'text' => 'OnlineAfspraken.nl koppeling', 'tag' => 'h2')); ?>
        <?php include_component('admin', 'text', array('key' => 'settings-general-help4', 'text' => '...')); ?>

        <div class="form-row">
          <div class="form-label"><label for="api_server">Server</label></div>
          <input type="text" id="api_server" name="api_server" value="<?php echo $company->getConnection()->getApiServer(); ?>" style="width:19em;">
        </div>

        <div class="form-row">
          <div class="form-label"><label for="api_key">API key</label></div>
          <input type="text" id="api_key" name="api_key" value="<?php echo $company->getConnection()->getApiKey(); ?>" style="width:10em;">
        </div>
        <div class="form-row">
          <div class="form-label"><label for="api_secret">API secret</label></div>
          <input type="text" id="api_secret" name="api_secret" value="<?php echo $company->getConnection()->getApiSecret(); ?>" style="width:23em;">
        </div>

        </fieldset>
      </form>
  </div>
  <div class="settings-panel" id="settings-products" style="display: none;">
    <script type="text/javascript">
      var products = <?php echo json_encode($nodes); ?>;
    </script>
    <?php
    /*
    function recurse_node($node, $depth = 0) {
      foreach ($node as $child) {
        echo '<li id="product_'.$child['id'].'">'.$child['title'];
        echo '<span><a><i class="fa fa-edit"></i></a>&nbsp;';
        echo '<a><i class="fa fa-remove"></i></a>&nbsp;</span>';
        if (isset($child['children'])) {
          echo PHP_EOL.'<ul>';
          recurse_node($child['children'], $depth + 1);
          echo '</ul>'.PHP_EOL;
        }
        echo '</li>'.PHP_EOL;
      }
    }*/
    ?>
    <form action="#" method="post">
      <fieldset>
        <legend>Products form</legend>

        <?php include_component('admin', 'text', array('key' => 'settings-products-help-title1', 'text' => 'Producten', 'tag' => 'h1', 'extra' => ' <a href="#" id="products-add-link"><span class="fa fa-edit" title="Product toevoegen"></span></a>')); ?>
        <?php include_component('admin', 'text', array('key' => 'settings-products-help1', 'text' => '...')); ?>
        <ul id="sortable-tree"><?php //recurse_node($nodes); ?></ul>
      </fieldset>
    </form>
  </div>

  <!-- micro edit for products -->
  <div id="microedit-product" style="display: none;">
    <div style="padding:10px;">
      <?php include_component('admin', 'text', array('key' => 'settings-products-help2', 'text' => '...')); ?>
      <div class="form-row">
        <div class="form-label"><label for="product-description">Omschrijving</label></div>
        <input type="text" name="product-description" id="product-description">
      </div>
      <div class="form-row">
        <div class="form-label"><label for="product-type">Type</label></div>
        <select type="text" name="product-type" id="product-type">
          <option value="category">Categorie</option>
          <option value="service">Dienst</option>
          <option value="product">Product</option>
          <option value="hours">Arbeidstijd</option>
        </select>
      </div>
      <div class="form-row" id="product-price-container">
        <div class="form-label"><label for="product-price">Prijs</label></div>
        <input type="text" name="product-price" id="product-price" class="currency">
      </div>
    </div>
    <div class="form-buttons">
      <button class="button-1">Annuleer</button>
      <button class="button-2">OK</button>
    </div>
  </div>

  <div class="settings-panel" id="settings-resources" style="display: none;">
    <form action="#" method="post">
      <fieldset>
        <legend>Settings form</legend>
        <?php include_component('admin', 'text', array('key' => 'settings-resources-help-title1', 'text' => 'Medewerkers', 'tag' => 'h1', 'extra' => ' <a href="#" id="resource-add-link"><span class="fa fa-edit" title="Medewerker toevoegen"></span></a>')); ?>
        <?php include_component('admin', 'text', array('key' => 'settings-resources-help1', 'text' => '...')); ?>

        <div id="resource-list"></div>
        <script type="text/javascript">
          var resource_data_url = '<?php echo url_for('admin/resourcesData'); ?>';
          var resource_ajax_url = '<?php echo url_for('admin/resourcesAjax'); ?>';
        </script>
      </fieldset>
    </form>
  </div>

  <!-- detail view -->
  <div id="resource-view" class="detail-view" style="display:none;">
    <div style="margin: 10px;overflow:auto;">
      <h2>Medewerker gegevens <a href="#" title="Medewerker gegevens bewerken" id="resource-edit-link"><span class="fa fa-edit"></span></a></h2>
      <table>
        <tr>
          <td style="width: 220px;font-weight: bold;">Naam</td>
          <td id="resource-view-title"></td>
        </tr>
        <tr>
          <td>E-mail adres</td>
          <td id="resource-view-email"></td>
        </tr>
      </table>
      <h2>Werkbonnen</h2>
      <div id="resource-workorders"></div>
    </div>
    <div class="form-buttons">
      <button class="button-1">Sluiten</button>
      <button class="button-2">Bewerken</button>
    </div>
  </div>
  <!-- edit view -->
  <div id="resource-form" style="display:none;">
    <div style="margin: 10px;overflow:auto;">

      <?php include_component('admin', 'text', array('key' => 'settings-resources-edit-title1', 'text' => 'Algemene gegevens', 'tag' => 'h2')); ?>
      <?php include_component('admin', 'text', array('key' => 'settings-resources-edit-help1', 'text' => '...')); ?>
      <input type="hidden" name="resource-method" id="resource-method" value="save">
      <div class="form-row">
        <div class="form-label"><label for="resource-title">Naam</label></div>
        <input type="text" name="resource-title" id="resource-title">
      </div>
      <div class="form-row">
        <div class="form-label"><label for="resource-email">E-mail adres</label></div>
        <input type="text" name="resource-email" id="resource-email">
      </div>
      <div class="form-row">
        <div class="form-label"><label for="resource-phone">Telefoonnummer</label></div>
        <input type="text" name="resource-phone" id="resource-phone">
      </div>

      <?php include_component('admin', 'text', array('key' => 'settings-resources-edit-title2', 'text' => 'Inloggegevens voor deze resource', 'tag' => 'h2')); ?>
      <?php include_component('admin', 'text', array('key' => 'settings-resources-edit-help2', 'text' => '...')); ?>

      <div class="form-row">
        <div class="form-label"><label for="resource-username">Gebruikersnaam</label></div>
        <input type="text" name="resource-username" id="resource-username" value="" style="width:7em;">
      </div>
      <div class="form-row">
        <div class="form-label"><label for="resource-password1">Wachtwoord</label></div>
        <input type="password" name="resource-password1" id="resource-password1" style="width:7em;">
      </div>
      <div class="form-row">
        <div class="form-label"><label for="resource-password2">Wachtwoord controle</label></div>
        <input type="password" name="resource-password2" id="resource-password2" style="width:7em;">
      </div>

      <div class="form-row">
        <div class="form-label"><label for="resource-active">Actief account</label></div>
        <input type="checkbox" class="checkbox" name="resource-active" id="resource-active"> <span>Zet dit vinkje uit als je de toegang van dit account (tijdelijk) wilt uitschakelen, zonder het account te verwijderen.</span>
      </div>

      <?php include_component('admin', 'text', array('key' => 'settings-resources-edit-title3', 'text' => 'Koppeling met OnlineAfspraken.nl', 'tag' => 'h2')); ?>
      <?php include_component('admin', 'text', array('key' => 'settings-resources-edit-help3', 'text' => '...')); ?>

      <div class="form-row">
        <div class="form-label"><label for="resource-oa">Resource in agenda</label></div>
        <select name="resource-oa" id="resource-oa" style="width:7em;">
        <?php
        $oaapi = new OAAPI;
        $response = $oaapi->sendRequest('getResources');
        if ($response) {
          $data = array();
          foreach ($response['Resource'] as $resource) {
            $data[$resource['Id']] = $resource['Name'];
            echo '<option value="'.$resource['Id'].'">'.$resource['Name'].'</option>';
          }
        }
        ?>
        </select>
      </div>
    </div>
    <div class="form-buttons">
      <button class="button-1">Sluiten</button>
      <button class="button-4" id="invite-btn" class="disabled" title="Stuur een uitnodiging met inloggegevens.">Opslaan en uitnodiging sturen</button>
      <button class="button-2">Opslaan</button>
    </div>
  </div>


  <div class="settings-panel" id="settings-app" style="display: none;">
    <form action="#" method="post">
      <fieldset>
        <legend>Settings form</legend>
        <?php include_component('admin', 'text', array('key' => 'settings-app-help-title1', 'text' => 'App functionaliteiten', 'tag' => 'h1')); ?>
        <?php include_component('admin', 'text', array('key' => 'settings-app-help1', 'text' => '...')); ?>
      </fieldset>

      <div class="form-row">
        <input <?php if($company->getSetting('app-setting-1')==1) echo ' checked="checked"'; ?> type="checkbox" class="checkbox" name="app-setting-1" id="app-setting-1"><label for="app-setting-1">De medewerker mag de klantgegevens aanpassen</label><br>
        <input <?php if($company->getSetting('app-setting-2')==1) echo ' checked="checked"'; ?> type="checkbox" class="checkbox" name="app-setting-2" id="app-setting-2"><label for="app-setting-2">De medewerker mag de orderregels aanpassen</label><br>
        <input <?php if($company->getSetting('app-setting-3')==1) echo ' checked="checked"'; ?> type="checkbox" class="checkbox" name="app-setting-3" id="app-setting-3"><label for="app-setting-3">De medewerker mag foto's toevoegen</label><br>
        <input <?php if($company->getSetting('app-setting-4')==1) echo ' checked="checked"'; ?> type="checkbox" class="checkbox" name="app-setting-4" id="app-setting-4"><label for="app-setting-4">De medewerker mag de klantgeschiedenis inzien</label><br>
        <input <?php if($company->getSetting('app-setting-5')==1) echo ' checked="checked"'; ?> type="checkbox" class="checkbox" name="app-setting-5" id="app-setting-5"><label for="app-setting-5">De medewerker mag de start- en eindtijd aanpassen</label><br>
        <input <?php if($company->getSetting('app-setting-6')==1) echo ' checked="checked"'; ?> type="checkbox" class="checkbox" name="app-setting-6" id="app-setting-6"><label for="app-setting-6">De medewerker mag een werkbon handmatig aanmaken</label><br>
        <input <?php if($company->getSetting('app-setting-7')==1) echo ' checked="checked"'; ?> type="checkbox" class="checkbox" name="app-setting-7" id="app-setting-7"><label for="app-setting-7">De medewerker mag werkbonnen verwijderen</label><br>
        <input <?php if($company->getSetting('app-setting-8')==1) echo ' checked="checked"'; ?> type="checkbox" class="checkbox" name="app-setting-8" id="app-setting-8"><label for="app-setting-8">De gewerkte tijd, bepaald door de start- en eindtijd, wordt doorberekend op de werkbon en factuur</label><br>
        <input <?php if($company->getSetting('app-setting-9')==1) echo ' checked="checked"'; ?> type="checkbox" class="checkbox" name="app-setting-9" id="app-setting-9"><label for="app-setting-9">De klant moet de werkbon ondertekenen</label><br>
        <input <?php if($company->getSetting('app-setting-10')==1) echo ' checked="checked"'; ?> type="checkbox" class="checkbox" name="app-setting-10" id="app-setting-10"><label for="app-setting-10">De klant mag de werkzaamheden direct afrekenen</label><br>
        <input <?php if($company->getSetting('app-setting-11')==1) echo ' checked="checked"'; ?> type="checkbox" class="checkbox" name="app-setting-11" id="app-setting-11"><label for="app-setting-11">De medewerker moet aangeven wanneer de werkzaamheden starten en eindigen.</label><br>
        <input <?php if($company->getSetting('app-setting-12')==1) echo ' checked="checked"'; ?> type="checkbox" class="checkbox" name="app-setting-11" id="app-setting-12"><label for="app-setting-12">De werkbon toont eventuele controlelijsten</label><br>
      </div>
    </form>
  </div>


  <div class="settings-panel" id="settings-checklists" style="display: none;">
    <form action="#" method="post">
      <fieldset>
        <legend>Settings form</legend>
        <?php include_component('admin', 'text', array('key' => 'settings-checklists-help-title1', 'text' => 'Controlelijsten', 'tag' => 'h1', 'extra' => ' <a href="#" id="checklist-add-link"><span class="fa fa-edit" title="Controlelijst toevoegen"></span></a>')); ?>
        <?php include_component('admin', 'text', array('key' => 'settings-checklists-help1', 'text' => '...')); ?>

        <div id="checklist-list"></div>
        <script type="text/javascript">
          var checklist_data_url = '<?php echo url_for('admin/checklistData'); ?>';
          var checklist_ajax_url = '<?php echo url_for('admin/checklistAjax'); ?>';
        </script>
      </fieldset>
    </form>
  </div>

  <!-- detail view -->
  <div id="checklist-view" class="detail-view" style="display:none;">
    <div style="margin: 10px;overflow:auto;">
      <h2>Controlelijst informatie <a href="#" title="Controlelijst bewerken" id="checklist-edit-link"><span class="fa fa-edit"></span></a></h2>
      <table>
        <tr>
          <td style="width: 220px;font-weight: bold;">Naam</td>
          <td id="checklist-view-title"></td>
        </tr>
      </table>
      <div id="checklist-view-checklist"></div>
    </div>
    <div class="form-buttons">
      <button class="button-1">Sluiten</button>
      <button class="button-2">Bewerken</button>
    </div>
  </div>
  <!-- edit view -->
  <div id="checklist-form" style="display:none;">
    <div style="margin: 10px;overflow:auto;">

      <?php include_component('admin', 'text', array('key' => 'settings-checklist-edit-title1', 'text' => 'Algemene gegevens', 'tag' => 'h2')); ?>
      <?php include_component('admin', 'text', array('key' => 'settings-checklist-edit-help1', 'text' => '...')); ?>
      <div class="form-row">
        <div class="form-label"><label for="checklist-title">Naam</label></div>
        <input type="text" name="checklist-title" id="checklist-title">
      </div>

      <div id="checklist-checklist-container">
        <h2>Controlepunten <a href="#" title="Controlepunt toevoegen" id="checklist-add-row-link"><span class="fa fa-edit"></span></a></h2>
        <div id="checklist-checklist"></div>
      </div>
    </div>
    <div class="form-buttons">
      <button class="button-1">Sluiten</button>
      <button class="button-2">Opslaan</button>
    </div>
  </div>
  <!-- micro edit for checklist row-->
  <div id="microedit-checklist-checklist" style="display: none;">
    <div style="padding:10px;">
      <?php include_component('admin', 'text', array('key' => 'settings-checklist-row-help2', 'text' => '...')); ?>
      <div class="form-row">
        <div class="form-label"><label for="checklist-checklist-title">Omschrijving</label></div>
        <input type="text" name="checklist-checklist-title" id="checklist-checklist-title">
     </div>
    </div>
    <div class="form-buttons">
      <button class="button-1">Annuleer</button>
      <button class="button-2">OK</button>
    </div>
  </div>


  <div class="settings-panel" id="settings-fields" style="display: none;">
    <form action="#" method="post">
      <fieldset>
        <legend>Settings form</legend>
        <?php include_component('admin', 'text', array('key' => 'settings-fields-help-title1', 'text' => 'Extra velden', 'tag' => 'h1', 'extra' => ' <a href="#" id="fields-add-link"><span class="fa fa-edit" title="Veld toevoegen"></span></a>')); ?>
        <?php include_component('admin', 'text', array('key' => 'settings-fields-help1', 'text' => '...')); ?>

        <div id="fields-list"></div>
        <script type="text/javascript">
          var fields_data_url = '<?php echo url_for('admin/fieldsData'); ?>';
          var fields_ajax_url = '<?php echo url_for('admin/fieldsAjax'); ?>';
        </script>
      </fieldset>
    </form>
  </div>

  <!-- detail view -->
  <div id="fields-view" class="detail-view" style="display:none;">
    <div style="margin: 10px;overflow:auto;">
      <h2>Veld informatie <a href="#" title="Veld bewerken" id="fields-edit-link"><span class="fa fa-edit"></span></a></h2>
      <table>
        <tr>
          <td style="width: 220px;font-weight: bold;">Naam</td>
          <td id="fields-view-title"></td>
        </tr>
      </table>
    </div>
    <div class="form-buttons">
      <button class="button-1">Sluiten</button>
      <button class="button-2">Bewerken</button>
    </div>
  </div>
  <!-- edit view -->
  <div id="fields-form" style="display:none;">
    <div style="margin: 10px;overflow:auto;">

      <?php include_component('admin', 'text', array('key' => 'settings-fields-edit-title1', 'text' => 'Algemene gegevens', 'tag' => 'h2')); ?>
      <?php include_component('admin', 'text', array('key' => 'settings-fields-edit-help1', 'text' => '...')); ?>
      <div class="form-row">
        <div class="form-label"><label for="fields-title">Naam</label></div>
        <input type="text" name="fields-title" id="fields-title">
      </div>
      <div class="form-row">
        <div class="form-label"><label for="fields-form">Formulier</label></div>
        <select type="text" name="fields-fform" id="fields-fform" style="width:26em;">
          <option value="customer">Dit veld heeft een unieke waarde per klant</option>
          <option value="app">Dit veld heeft een unieke waarde per afspraak</option>
        </select>
      </div>
    </div>
    <div class="form-buttons">
      <button class="button-1">Sluiten</button>
      <button class="button-2">Opslaan</button>
    </div>
  </div>


  <div class="settings-panel" id="settings-login" style="display: none;">

    <form action="#" method="post">
      <fieldset>
        <legend>Settings form</legend>
        <?php include_component('admin', 'text', array('key' => 'settings-login-help-title1', 'text' => 'Beheerder', 'tag' => 'h1')); ?>
        <?php include_component('admin', 'text', array('key' => 'settings-login-help1', 'text' => '...')); ?>

        <div class="form-row">
          <div class="form-label"><label for="admin-title">Naam</label></div>
          <input type="text" name="admin-title" id="admin-title" value="<?php echo $administrator->getTitle(); ?>">
        </div>
        <div class="form-row">
          <div class="form-label"><label for="admin-email">E-mail adres</label></div>
          <input type="text" name="admin-email" id="admin-email" value="<?php echo $administrator->getEmail(); ?>">
        </div>
        <div class="form-row">
          <div class="form-label"><label for="admin-username">Gebruikersnaam</label></div>
          <input type="text" name="admin-username" id="admin-username" value="<?php echo $credentials->getUsername(); ?>" style="width:7em;">
        </div>
        <div class="form-row">
          <div class="form-label"><label for="admin-password1">Wachtwoord</label></div>
          <input type="password" name="admin-password1" id="admin-password1" style="width:7em;">
        </div>
        <div class="form-row">
          <div class="form-label"><label for="admin-password2">Wachtwoord controle</label></div>
          <input type="password" name="admin-password2" id="admin-password2" style="width:7em;">
        </div>
      </fieldset>
    </form>

  </div>
  </div>
