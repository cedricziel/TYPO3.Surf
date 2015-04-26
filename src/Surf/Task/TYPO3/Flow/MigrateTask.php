<?php
namespace TYPO3\Surf\Task\TYPO3\Flow;

/*                                                                        *
 * This script belongs to the package "TYPO3.Surf".                       *
 *                                                                        *
 *                                                                        */

use TYPO3\Surf\Application\TYPO3\Flow;
use TYPO3\Surf\Domain\Model\Node;
use TYPO3\Surf\Domain\Model\Application;
use TYPO3\Surf\Domain\Model\Deployment;
use TYPO3\Surf\Domain\Model\Task;
use TYPO3\Surf\Domain\Service\ShellCommandService;
use TYPO3\Surf\Exception\InvalidConfigurationException;

/**
 * A TYPO3 Flow migration task
 *
 */
class MigrateTask extends Task {

	/**
	 * @var \TYPO3\Surf\Domain\Service\ShellCommandService
	 */
	protected $shell;

	public function __construct() {
		$this->shell = new ShellCommandService();
	}

	/**
	 * Execute this task
	 *
	 * @param \TYPO3\Surf\Domain\Model\Node $node
	 * @param \TYPO3\Surf\Domain\Model\Application $application
	 * @param \TYPO3\Surf\Domain\Model\Deployment $deployment
	 * @param array $options
	 * @throws InvalidConfigurationException
	 * @return void
	 */
	public function execute(Node $node, Application $application, Deployment $deployment, array $options = array()) {
		if (!$application instanceof Flow) {
			throw new InvalidConfigurationException(sprintf('Flow application needed for MigrateTask, got "%s"', get_class($application)), 1358863288);
		}

		$commandPackageKey = 'typo3.flow';
		if ($application->getVersion() < '2.0') {
			$commandPackageKey = 'typo3.flow3';
		}

		$targetPath = $deployment->getApplicationReleasePath($application);
		$this->shell->executeOrSimulate('cd ' . $targetPath . ' && FLOW_CONTEXT=' . $application->getContext() . ' ./' . $application->getFlowScriptName() . ' ' . $commandPackageKey . ':doctrine:migrate', $node, $deployment);
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

	/**
	 * Rollback the task
	 *
	 * @param \TYPO3\Surf\Domain\Model\Node $node
	 * @param \TYPO3\Surf\Domain\Model\Application $application
	 * @param \TYPO3\Surf\Domain\Model\Deployment $deployment
	 * @param array $options
	 * @return void
	 */
	public function rollback(Node $node, Application $application, Deployment $deployment, array $options = array()) {
		// TODO Implement rollback of Doctrine migration
	}

}
