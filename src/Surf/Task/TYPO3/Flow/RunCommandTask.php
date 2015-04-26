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
 * Task for running arbitrary TYPO3 Flow commands
 *
 */
class RunCommandTask extends Task {

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
	 * @return void
	 * @throws \TYPO3\Surf\Exception\InvalidConfigurationException
	 */
	public function execute(Node $node, Application $application, Deployment $deployment, array $options = array()) {
		if (!$application instanceof Flow) {
			throw new InvalidConfigurationException(sprintf('Flow application needed for RunCommandTask, got "%s"', get_class($application)), 1358863336);
		}
		if (!isset($options['command'])) {
			throw new InvalidConfigurationException('Missing option "command" for RunCommandTask', 1319201396);
		}

		$targetPath = $deployment->getApplicationReleasePath($application);
		$arguments = $this->buildCommandArguments($options);
		$this->shell->executeOrSimulate('cd ' . $targetPath . ' && FLOW_CONTEXT=' . $application->getContext() . ' ./' . $application->getFlowScriptName() . ' ' . $options['command'] . $arguments, $node, $deployment);
	}

	/**
	 * Simulate this task
	 *
	 * @param Node $node
	 * @param Application $application
	 * @param Deployment $deployment
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
		// TODO Implement rollback
	}

	/**
	 * @param array $options The command options
	 * @return string The escaped arguments string
	 */
	protected function buildCommandArguments(array $options) {
		$arguments = '';
		if (isset($options['arguments'])) {
			if (!is_array($options['arguments'])) {
				$options['arguments'] = array($options['arguments']);
			}

			$options['arguments'] = array_map(function ($value) {
				return escapeshellarg($value);
			}, $options['arguments']);

			$arguments = ' ' . implode(' ', $options['arguments']);
		}
		return $arguments;
	}

}
