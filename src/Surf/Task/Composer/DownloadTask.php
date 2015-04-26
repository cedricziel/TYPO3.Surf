<?php
namespace TYPO3\Surf\Task\Composer;

/*                                                                        *
 * This script belongs to the package "TYPO3.Surf".                       *
 *                                                                        *
 *                                                                        */

use TYPO3\Surf\Domain\Model\Node;
use TYPO3\Surf\Domain\Model\Application;
use TYPO3\Surf\Domain\Model\Deployment;
use TYPO3\Surf\Domain\Model\Task;
use TYPO3\Surf\Domain\Service\ShellCommandService;

/**
 * Downloads composer into the current releasePath.
 */
class DownloadTask extends Task {

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Surf\Domain\Service\ShellCommandService
	 */
	protected $shell;

	public function __construct() {
		$this->shell = new ShellCommandService();
	}

	/**
	 * @param \TYPO3\Surf\Domain\Model\Node $node
	 * @param \TYPO3\Surf\Domain\Model\Application $application
	 * @param \TYPO3\Surf\Domain\Model\Deployment $deployment
	 * @param array $options
	 * @throws \TYPO3\Surf\Exception\TaskExecutionException
	 * @return void
	 */
	public function execute(Node $node, Application $application, Deployment $deployment, array $options = array()) {
		$applicationReleasePath = $deployment->getApplicationReleasePath($application);

		if (isset($options['composerDownloadCommand'])) {
			$composerDownloadCommand = $options['composerDownloadCommand'];
		} else {
			$composerDownloadCommand = 'curl -s https://getcomposer.org/installer | php';
		}

		$command = sprintf('cd %s && %s', escapeshellarg($applicationReleasePath), $composerDownloadCommand);
		$this->shell->executeOrSimulate($command, $node, $deployment);
	}
}
