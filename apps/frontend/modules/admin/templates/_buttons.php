<?php
$buttons = $sf_user->getAttribute('buttons');
foreach ($buttons as $button) {
  $type = isset($button['type']) ? $button['type'] : 'button';
  $cls = isset($button['class']) ? ' class="'.$button['class'].'"' : '';
  $action = isset($button['action']) ? ' onclick="'.$button['action'].'"' : '';
  echo "<button type=\"{$type}\"{$cls}{$action}>{$button['label']}</button>";
}