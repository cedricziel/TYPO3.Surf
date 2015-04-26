<?php
namespace TYPO3\Surf\Command;

/* * *************************************************************
 *  Copyright notice
 *
 *  (c) 2015 Ingo Pfennigstorf <pfennigstorf@sub-goettingen.de>
 *      Goettingen State Library
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 * ************************************************************* */
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\Surf\Domain\Service\DeploymentService;

/**
 * List Command controller
 */
class ListCommand extends Command {

	/**
	 * @var \TYPO3\Surf\Domain\Service\DeploymentService
	 */
	protected $deploymentService;

	public function __construct() {
		parent::__construct();
		$this->deploymentService = new DeploymentService();
	}

	protected function configure() {
		$this
				->setName('surf:list')
				->setDescription('List available deployments that can be deployed with the surf:deploy command.')
				->addArgument(
						'configurationPath',
						null,
						InputOption::VALUE_NONE,
						'If set, the task will yell in uppercase letters'
				);
	}

	/**
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 *
	 * @return void
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {

		$input->getArgument('configurationPath') ? $configurationPath = $input->getArgument('configurationPath') : $configurationPath = NULL;

		$deploymentNames = $this->deploymentService->getDeploymentNames($configurationPath);

		$text = '';

		if (!$input->hasArgument('quiet')) {
			$text = '<info>' . count($deploymentNames) . ' Deployments</info>:' . PHP_EOL;
		}

		foreach ($deploymentNames as $deploymentName) {
			$line = $deploymentName;
			if (!$input->hasOption('quiet')) {
				$line = '  ' . $line;
			}
			$text .= $line;
		}


		$output->writeln($text);
	}

}
