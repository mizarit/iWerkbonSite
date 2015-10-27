<?php

/**
 * Deployment task is verantwoordelijk voor het publiceren van de site en het uitvoeren van databasemigraties
 *
 * @author Bert-Jan de Lange <bert-jan@bugbyte.nl>
 */
class bbDeployTask extends sfBaseTask
{
	protected function configure()
	{
		$this->addOptions(array(
			new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'frontend'),
			new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'prod'),
			new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'propel')
		));

		$this->addArguments(array(
			new sfCommandArgument('action', sfCommandArgument::REQUIRED, 'deploy of rollback'),
			new sfCommandArgument('target', sfCommandArgument::REQUIRED, 'stage of prod')
		));

		$this->namespace = 'bb';
		$this->name = 'deploy';
		$this->briefDescription = 'Upload de site naar productie en voert databasemigraties uit';
		$this->detailedDescription = 'Gebruikt: bb:deploy [check|deploy|rollback]';
	}

	protected function execute($arguments = array(), $options = array())
	{
	    // lokale implementatie prefereren (doet de autoloader niet altijd goed
		if (file_exists(sfConfig::get('sf_lib_dir') .'/bbDeploy.class.php'))
			require_once(sfConfig::get('sf_lib_dir') .'/bbDeploy.class.php');
	$deploy = new bbDeploy(array(
			'arguments' => $arguments,
			'options' => $options,
			'dispatcher' => $this->dispatcher,
			'formatter' => $this->formatter,
			'task' => $this
		));

		switch($arguments['action'])
		{
			case 'deploy':
				$deploy->deploy();
				break;
			case 'rollback':
				$deploy->rollback();
				break;
			case 'cleanup':
				$deploy->cleanup();
				break;
			default:
				$this->logSection('deploy', 'Gebruik: bb:deploy [deploy|rollback|cleanup] [stage|prod]');
		}
	}
}
