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

/**
 * A symlink task for linking a shared Production configuration
 *
 * Note: this might cause problems with concurrent access due to the cached configuration
 * inside this directory.
 *
 *
 * TODO Fix problem with include cached configuration
 */
class SymlinkConfigurationTask extends Task {

	/**
	 * @var \TYPO3\Surf\Domain\Service\ShellCommandService
	 */
	protected $shell;

	public function __construct() {
		$this->shell = new ShellCommandService();
	}

	/**
	 * Executes this task
	 *
	 * @param \TYPO3\Surf\Domain\Model\Node $node
	 * @param \TYPO3\Surf\Domain\Model\Application $application
	 * @param \TYPO3\Surf\Domain\Model\Deployment $deployment
	 * @param array $options
	 * @return void
	 */
	public function execute(Node $node, Application $application, Deployment $deployment, array $options = array()) {
		$targetReleasePath = $deployment->getApplicationReleasePath($application);

		if ($application instanceof Flow) {
			$context = $application->getContext();
		} else {
			$context = 'Production';
		}

		$commands = array(
			"cd {$targetReleasePath}/Configuration",
			"if [ -d {$context} ]; then rm -Rf {$context}; fi",
			"mkdir -p ../../../shared/Configuration/{$context}"
		);

		if (strpos($context, '/') !== FALSE) {
			$baseContext = dirname($context);
			$commands[] = "mkdir -p {$baseContext}";
			$commands[] = "ln -snf ../../../../shared/Configuration/{$context} {$context}";
		} else {
			$commands[] = "ln -snf ../../../shared/Configuration/{$context} {$context}";
		}


		$this->shell->executeOrSimulate($commands, $node, $deployment);
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

}
