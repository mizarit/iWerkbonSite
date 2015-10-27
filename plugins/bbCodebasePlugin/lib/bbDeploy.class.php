<?php

/**
 * Symfony-specialisatie van de Deploy class
 */
class bbDeploy extends BaseDeploy
{
	protected $dispatcher = null;
	protected $formatter = null;
	protected $arguments = null;
	protected $options = null;
	protected $task = null;

	/**
	 * Het configuratie van de deploy-class aansluiten op symfony
	 *
	 * @param array $options
	 * @return bbDeploy
	 */
	public function __construct(array $options)
	{
		$this->dispatcher = $options['dispatcher'];
		$this->formatter = $options['formatter'];
		$this->arguments = $options['arguments'];
		$this->options = $options['options'];
		$this->task = $options['task'];

		$options = $options['options'];

		unset(
			$options['dispatcher'],
			$options['formatter'],
			$options['arguments'],
			$options['options'],
			$options['task']
		);

	    // standaard sync settings van symfony gebruiken
	    $config = parse_ini_file(sfConfig::get('sf_root_dir') .'/config/properties.ini', true);

		// de eerste host wordt, als er meerdere hosts zijn, gezien als clustermaster
		$remote_hosts = $config[$this->arguments['target']]['host'];

		foreach ($config[$this->arguments['target']] as $config_key => $config_value)
		{
			if ($config_key != 'host' && substr($config_key, 0, 4) == 'host')
			{
				if (!is_array($remote_hosts))
					$remote_hosts = array($remote_hosts);

				$remote_hosts[] = $config_value;
			}
		}

		$deployment_options = array(
			'project_name'		=> $config['symfony']['name'],
			'basedir'			=> sfConfig::get('sf_root_dir'),
			'remote_host'		=> $remote_hosts,
			'remote_dir'		=> $config[$this->arguments['target']]['dir'],
			'remote_user'		=> $config[$this->arguments['target']]['user'],
			'rsync_excludes'	=> sfConfig::get('sf_root_dir') .'/config/rsync_exclude.txt',
			'database_dirs'		=> array(
										'plugins/bbCodebasePlugin/sql-updates',
								   ),
			'target'			=> $this->arguments['target'],
			'database_patcher'	=> 'plugins/bbCodebasePlugin/lib/deploy/database-patcher.php',
			'data_dirs'			=> array(
										'web/uploads',
										'web/images/upload',
										'web/docs',
										'web/img/lastminutes',
										'web/img/logos',
										'web/img/categories',
										'web/invoices',
										'web/clieop',
										//'web/podcasting',
										//'web/reclame',
								   ),
			'target_specific_files' => array(
			 'apps/frontend/config/app.yml',
			 'apps/mobile/config/app.yml',
			 'config/databases.yml',
			 'config/propel.ini',
			 'web/apisoap.wsdl'
			),					   
			'datadir_patcher'	=> 'plugins/bbCodebasePlugin/lib/deploy/datadir-patcher.php',
		);

		if ($target_specific_files = sfConfig::get('app_deployer_target_specific_files')) {
			$deployment_options['target_specific_files'] = $target_specific_files;

			// uitzoeken welke database configfile er moet worden gebruikt
			foreach ($target_specific_files as $filepath) {
				if (basename($filepath) == 'databases.yml') {
					$ext = pathinfo($filepath, PATHINFO_EXTENSION);

					$target_filepath = str_replace(".$ext", ".{$this->arguments['target']}.$ext", $filepath);
					break;
				}
			}
		}

		if (!isset($target_filepath))
			$target_filepath = 'config/databases.yml';

		// de naam van de database uit de configfile parsen
		$database_config = sfYaml::load(sfConfig::get('sf_root_dir') . DIRECTORY_SEPARATOR . $target_filepath);
		$dsn = $database_config['all']['propel']['param']['dsn'];

		if (preg_match('/dbname=([^;]+)/', $dsn, $matches))
			$deployment_options['database_name'] = $matches[1];

		if ($database_extra_dirs = sfConfig::get('app_deployer_database_extra_dirs'))
			$deployment_options['database_dirs'] = array_merge($deployment_options['database_dirs'], $database_extra_dirs);

		if ($database_host = sfConfig::get('app_deployer_database_host'))
			$deployment_options['database_host'] = $database_host;

		return parent::__construct($deployment_options);
	}

	/**
	 * Een remote-cc uitvoeren nadat een project is geupdate
	 */
	protected function postDeploy($remote_host, $remote_dir, $target_dir)
	{
        parent::postDeploy($remote_host, $remote_dir, $target_dir);

		if ($xhprof_settings = sfConfig::get('app_deployer_xhprof')) {
			if ($xhprof_settings['enabled'] && ($path = $xhprof_settings['frontcontroller_path']))
			{
				$xhprof_links = array(
					'kahlan' => 'kahlan-xhprof.bugbyte.nl',
					'cara' => 'cara-xhprof.bugbyte.nl',
					'monster' => 'monster-xhprof.bugbyte.nl',
					'dutchcowboys' => 'dutchcowboys-xhprof.bugbyte.nl'
				);

				foreach ($xhprof_links as $server_key => $link)
				{
					if (strpos($remote_host, $server_key) !== false)
					{
						$output = array();
						$return = null;

						$this->sshExec($remote_host, "cd $remote_dir/{$target_dir}/; sed -i '' 's/#xhprof_link#/{$link}/' {$path}", $output, $return);
					}
				}
			}
		}

		$task = new bbRemoteCCTask($this->dispatcher, $this->formatter);
		$task->run(array('server' => $this->arguments['target'], 'dir' => $this->remote_dir .'/'. $this->remote_target_dir));

    $output = array();
    $return = 0;
		$this->sshExec($remote_host, "echo \"<?php define('DEPLOY_TIMESTAMP', ". date('YmdHis', $this->timestamp) .");\" > {$this->remote_dir}/deploy_timestamp.php", $output, $return);
		
	}

	/**
	 * Een remote-cc uitvoeren nadat een project is gerollbacked
	 */
	protected function preRollback($remote_host, $remote_dir, $target_dir)
	{
        parent::preRollback($remote_host, $remote_dir, $target_dir);

		$task = new bbRemoteCCTask($this->dispatcher, $this->formatter);
		$task->run(array('server' => $this->arguments['target'], 'dir' => $this->remote_dir .'/'. $this->previous_remote_target_dir));
	}

	protected function postRollback($remote_host, $remote_dir, $target_dir)
	{
        parent::postRollback($remote_host, $remote_dir, $target_dir);

        $output = array();
        $return = 0;
        $this->sshExec($remote_host, "echo \"<?php define('DEPLOY_TIMESTAMP', ". date('YmdHis', $this->previous_timestamp) .");\" > {$this->remote_dir}/deploy_timestamp.php", $output, $return);
	}
}
