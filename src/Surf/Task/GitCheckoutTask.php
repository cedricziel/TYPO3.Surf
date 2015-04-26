<?php
namespace TYPO3\Surf\Task;

/*                                                                        *
 * This script belongs to the package "TYPO3.Surf".                       *
 *                                                                        *
 *                                                                        */

use TYPO3\Surf\Domain\Model\Node;
use TYPO3\Surf\Domain\Model\Application;
use TYPO3\Surf\Domain\Model\Deployment;
use TYPO3\Surf\Exception\InvalidConfigurationException;
use TYPO3\Surf\Task\Git\AbstractCheckoutTask;
use TYPO3\Surf\Utility\Files;

/**
 * A Git checkout task
 *
 */
class GitCheckoutTask extends AbstractCheckoutTask {

	/**
	 * Execute this task
	 *
	 * @param \TYPO3\Surf\Domain\Model\Node $node
	 * @param \TYPO3\Surf\Domain\Model\Application $application
	 * @param \TYPO3\Surf\Domain\Model\Deployment $deployment
	 * @param array $options
	 * @return void
	 * @throws \TYPO3\Surf\Exception\InvalidConfigurationException
	 * @throws \TYPO3\Surf\Exception\TaskExecutionException
	 */
	public function execute(Node $node, Application $application, Deployment $deployment, array $options = array()) {
		if (!isset($options['repositoryUrl'])) {
			throw new InvalidConfigurationException(sprintf('Missing "repositoryUrl" option for application "%s"', $application->getName()), 1335974764);
		}

		$releasePath = $deployment->getApplicationReleasePath($application);
		$deploymentPath = $application->getDeploymentPath();
		$checkoutPath = Files::concatenatePaths(array($deploymentPath, 'cache', 'transfer'));

		if (!isset($options['hardClean'])) {
			$options['hardClean'] = TRUE;
		}

		$sha1 = $this->executeOrSimulateGitCloneOrUpdate($checkoutPath, $node, $deployment, $options);

		$command = strtr("
			cp -RPp $checkoutPath/. $releasePath
				&& (echo $sha1 > $releasePath" . "REVISION)
			", "\t\n", "  ");

		$this->shell->executeOrSimulate($command, $node, $deployment);

		$this->executeOrSimulatePostGitCheckoutCommands($releasePath, $sha1, $node, $deployment, $options);
	}

	/**
	 * Rollback this task by removing the revision file
	 *
	 * @param \TYPO3\Surf\Domain\Model\Node $node
	 * @param \TYPO3\Surf\Domain\Model\Application $application
	 * @param \TYPO3\Surf\Domain\Model\Deployment $deployment
	 * @param array $options
	 * @return void
	 */
	public function rollback(Node $node, Application $application, Deployment $deployment, array $options = array()) {
		$releasePath = $deployment->getApplicationReleasePath($application);
		$this->shell->execute('rm -f ' . $releasePath . 'REVISION', $node, $deployment, TRUE);
	}

}
