<h1>Sitemap</h1>
<ul id="sitemap" class="sitemap">
  <li class="root"><a href="<?php echo url_for('@homepage'); ?>">Homepage</a></li>
<?php recurse_sitemap($tree, 1); ?>
</ul>


<?php

function recurse_sitemap($elements, $branch)
{
  $branch++;
  foreach ($elements as $element) {
    
    if (isset($element['children']) && count($element['children']) > 0) {
      echo "\n  <li class=\"open\" id=\"branch-{$branch}\">\n";
      echo "<a id=\"branch-{$branch}-toggle\" class=\"open\" href=\"javascript:collapse('branch-{$branch}');\"></a>\n";
      echo "<span><a id=\"menu-{$branch}-\" href=\"{$element['url']}\">{$element['title']}</a></span>\n";
      echo "\n<ul class=\"sitemap\" id=\"branch-".($branch+1)."-sub\">\n";
      $branch = recurse_sitemap($element['children'], $branch);
      echo "\n</ul>\n";
    }
    else {
      echo "\n  <li><a href=\"".$element['url'].'">'.$element['title']."</a>\n";
    }
  
    echo "  </li>\n";
  }
  
  return $branch;
}