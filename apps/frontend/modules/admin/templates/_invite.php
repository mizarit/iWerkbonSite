<?php $path = zeusTools::getDomainName(); ?>
<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>iWerkbon</title>
  <style tyle="text/css">
    tr, td {
      padding: 0;
      margin: 0;
      border: none;
    }

    * {
      font-family: 'Verdana';
      font-size: 13px;
      color: #1a1a1a;
    }

    h1 {
      font-size: 18px;
      text-align: left;
      color: #00b4ff;
    }

    p {
      margin-top: 20px;
    }
    p a {
      text-decoration: none;
      font-size: 13px;
    }
    table {
  margin-bottom: 30px;
}
    td {
      padding: 5px 0;
    }
  </style>
<img src="http://<?php echo $_SERVER['SERVER_NAME']; ?>/img/logo-iwerkbon.png" style="width:140px;">
  <h1>Uitnodiging</h1>
<p>Beste <?php echo $data['name']; ?>,</p>
<p>Je bent uitgenodigd om de digitale werkbon app van iWerkbon te gebruiken.</p>
<p>Om gebruik te kunnen maken van de app moet je inloggen met onderstaande gegevens:</p>

<table>
  <tr>
    <td><strong>Gebruikersnaam</strong>:</td>
    <td><?php echo $data['username']; ?></td>
  </tr>
  <tr>
    <td><strong>Wachtwoord</strong>:</td>
    <td><?php echo $data['password']; ?></td>
  </tr>
</table>

  <p>Je kunt de iWerkbon app downloaden in de App Store of Google Play.</p>
  <a href="https://play.google.com/store/apps/details?id=app.medusa.nl.medusa&hl=en"><img src="http://<?php echo $_SERVER['SERVER_NAME']; ?>/img/android.png"></a>&nbsp;
  <a href=""><img src="http://<?php echo $_SERVER['SERVER_NAME']; ?>/img/appstore.png"></a>



