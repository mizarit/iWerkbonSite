<?php

function useradmin_edit_password($object, $config)
{
  return form_row('password', input_tag('password'), $config);
}