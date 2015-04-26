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
 * Describe Command controller
 */
class DescribeCommand extends Command {

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
				->setName('surf:describe')
				->setDescription('Describe a deployment.')
				->addArgument(
						'deploymentName',
						null
				)
				->addArgument(
						'configurationPath',
						null,
						InputOption::VALUE_NONE,
						'Path for deployment configuration files'
				);
	}

	/**
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 *
	 * @return void
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {


		$deployment = $this->deploymentService->getDeployment($input->getArgument('deploymentName'), $input->getArgument('configurationPath'));

		$deployment->initialize();

		$text = '<em> Deployment <b>' . $deployment->getName() . ' </b></em>';

		$text .= '<u>Workflow</u>: ' . $deployment->getWorkflow()->getName() . PHP_EOL;
		$text .= '<u>Nodes</u>:' . PHP_EOL;
		foreach ($deployment->getNodes() as $node) {
			$text .= '  <b>' . $node->getName() . '</b> (' . $node->getHostname() . ')';
		}
		$text .= PHP_EOL . '<u>Applications</u>:' . PHP_EOL;
		foreach ($deployment->getApplications() as $application) {
			$text .= '  <b>' . $application->getName() . '</b>' . PHP_EOL;
			$text .= '    <u>Deployment path</u>: ' . $application->getDeploymentPath();
			$text .= '    <u>Options</u>: ';
			foreach ($application->getOptions() as $key => $value) {
				$text .= '      ' . $key . ' => ' . $value;
			}
			$text .= '    <u>Nodes</u>: ' . implode(', ', $application->getNodes());
		}

		$output->writeln($text);
	}

}
