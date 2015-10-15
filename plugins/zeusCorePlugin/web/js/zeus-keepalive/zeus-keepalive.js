function keepalive()
{
  new Ajax.Request('/core/ping', {});
  setTimeout(keepalive, 10000);
}

keepalive();