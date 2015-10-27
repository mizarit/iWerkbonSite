<?php

/**
 * Voert een cc uit op een remote installatie van dit project
 *
 * @author Bert-Jan de Lange <bert-jan@bugbyte.nl>
 */
class bbRemoteCCTask extends sfBaseTask
{
  protected function configure()
  {
    $this->addArgument('server', sfCommandArgument::REQUIRED, 'The server name');
    $this->addArgument('dir', sfCommandArgument::OPTIONAL, 'The directory on the remote server');
    $this->addArgument('host', sfCommandArgument::OPTIONAL, 'Hostname of the remote server');

    $this->aliases = array('remote-cc');
    $this->namespace = 'bb';
    $this->name = 'remote-cc';
    $this->briefDescription = 'Voert cc uit op een remote installatie';
  }

  protected function execute($arguments = array(), $options = array())
  {
    $env = $arguments['server'];

    $ini = sfConfig::get('sf_config_dir').'/properties.ini';
    if (!file_exists($ini))
    {
      throw new sfCommandException('You must create a config/properties.ini file');
    }

    $properties = parse_ini_file($ini, true);

    if (!isset($properties[$env]))
    {
      throw new sfCommandException(sprintf('You must define the configuration for server "%s" in config/properties.ini', $env));
    }

    $properties = $properties[$env];

    if (!isset($properties['host']))
    {
      throw new sfCommandException('You must define a "host" entry.');
    }

    if (!isset($properties['dir']))
    {
      throw new sfCommandException('You must define a "dir" entry.');
    }

    $host = isset($arguments['host']) ? $arguments['host'] : $properties['host'];
    $dir  = isset($arguments['dir']) ? $arguments['dir'] : $properties['dir']; // mogelijke override voor dcDeploy
    $user = isset($properties['user']) ? $properties['user'].'@' : '';

    if (substr($dir, -1) != '/')
    {
      $dir .= '/';
    }

    $ssh = 'ssh';

    if (isset($properties['port']))
    {
      $port = $properties['port'];
      $ssh = "ssh -p$port";
    }

    $cmd = "$ssh $user{$host} {$dir}symfony cc";

    echo $cmd . PHP_EOL;

    $this->log($this->getFilesystem()->execute($cmd));
  }
}
