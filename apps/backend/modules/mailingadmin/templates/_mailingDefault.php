<?php $path = 'http://plekjevrij.nl'; 
?>
<style type="text/css">
body {
  background: #fff;
  font-family: "trebuchet ms",helvetica,sans-serif;
}

p {
  margin: 0 10px 15px 10px;
  color: #585858;
  font-size: 12px;
  font-family: "trebuchet ms",helvetica,sans-serif;
}

td {
  color: #585858;
  font-size: 12px;
  font-family: "trebuchet ms",helvetica,sans-serif;
  padding: 0;
  margin: 0;
}

table {
  margin: 10px;
  border-collapse: collapse;
  padding: 0;
}

strong {
  color: #0b4a94;
  font-weight: bold;
}

h1, h2, h3 {
  font-family: "trebuchet ms",helvetica,sans-serif;
  width: 750px;
  margin: 10px 10px 15px 10px;
  color: #0b4a94;
  font-weight: normal;
  letter-spacing: -1px;
  font-size: 24px;
}

h1 {
  border-bottom: #cecece 1px dotted;
}


h2 a {
  text-decoration: none;
}

h3 {
  font-size: 18px;
  margin-top: 15px;
  margin-bottom: 5px;
}

a {
  color: #034896;
}

.item h3 {
  margin-top: 10px;
  margin-left: 10px;
}

.item h3 a {
  color: #0b4a94;
}

.item p {
  color: #666;
}

.item {
  width: 760px;
  position: relative;
  border-top: #cecece 1px dotted;
  padding-top: 5px;
  padding-bottom: 15px;
}

table {
  border-collapse: collapse;
  padding: 0;
  margin: 0 0 0 10px;
}

td {
  padding: 0;
}


</style>
<img src="<?php echo $path; ?>/img/header-mailing.png" alt="">
<table width="760" cellpadding="0" cellspacing="0" style="margin-top:10px;">
  <tr>
    <td>
    
    
    <?php echo htmlspecialchars_decode($mailing->getContent()); ?>
    
    </td>
      </tr> 
      <tr>
        <td></td>
        <td colspan="1" align="left" style="padding-top:20px;"></td>
      </tr> 
      </table>
</td>
  </tr>
</table>

<table width="760" style="background: #d1d51d;">
  <tr>
    <td colspan="2" style="height:120px;background:#f3f4c6;"></td>
  </tr>
  <tr>
    <td></td>
    <td style="height:120px;vertical-align:top;text-align:right;padding: 10px 10px 0 0;color:#0b4a94">
    Volg ons op 
    <a href="https://twitter.com/#!/onlineafspraken"><img alt="" src="http://plekjevrij.nl/img/soc-twitter-small.png"></a>
 	  <a href="http://www.facebook.com/OnlineAfspraken"><img alt="" src="http://plekjevrij.nl/img/soc-facebook-small.png"></a>
 	  <a href="#"><img alt="" src="http://plekjevrij.nl/img/soc-linkedin-small.png"></a>
 	
 	  <a href="http://www.youtube.com/user/OnlineAfspraken"><img alt="" src="http://plekjevrij.nl/img/soc-youtube-small.png"></a>
 		
    </td>
  </tr>
</table>

