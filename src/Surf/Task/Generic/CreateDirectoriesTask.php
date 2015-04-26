<?php
namespace TYPO3\Surf\Task\Generic;

/*                                                                        *
 * This script belongs to the package "TYPO3.Surf".                       *
 *                                                                        *
 *                                                                        */

use TYPO3\Surf\Domain\Model\Node;
use TYPO3\Surf\Domain\Model\Application;
use TYPO3\Surf\Domain\Model\Deployment;
use TYPO3\Surf\Domain\Service\ShellCommandService;

/**
 * A task to create given directories for a release
 */
class CreateDirectoriesTask extends \TYPO3\Surf\Domain\Model\Task {

	/**
	 * @var \TYPO3\Surf\Domain\Service\ShellCommandService
	 */
	protected $shell;

	protected function __construct() {
		$this->shell = new ShellCommandService();
	}

	/**
	 * Execute this task
	 *
	 * @param \TYPO3\Surf\Domain\Model\Node $node
	 * @param \TYPO3\Surf\Domain\Model\Application $application
	 * @param \TYPO3\Surf\Domain\Model\Deployment $deployment
	 * @param array $options
	 * @return void
	 */
	public function execute(Node $node, Application $application, Deployment $deployment, array $options = array()) {
		if (!isset($options['directories']) || !is_array($options['directories']) || $options['directories'] === array()) {
			return;
		}

		$deploymentPath = $application->getDeploymentPath();
		$commands = array();
		foreach ($options['directories'] as $path) {
			$commands[] = 'mkdir -p ' . $deploymentPath . '/' . $path;
		}

		$this->shell->executeOrSimulate($commands, $node, $deployment);
	}

	/**
	 * Simulate this task
	 *
	 * @param \TYPO3\Surf\Domain\Model\Node $node
	 * @param \TYPO3\Surf\Domain\Model\Application $application
	 * @param \TYPO3\Surf\Domain\Model\Deployment $deployment
	 * @param array $options
	 * @return void
	 */
	public function simulate(Node $node, Application $application, Deployment $deployment, array $options = array()) {
		$this->execute($node, $application, $deployment, $options);
	}

}
