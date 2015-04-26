<?php
namespace TYPO3\Surf\Task\Php;

/*                                                                        *
 * This script belongs to the package "TYPO3.Surf".                       *
 *                                                                        *
 *                                                                        */

use TYPO3\Surf\Domain\Model\Node;
use TYPO3\Surf\Domain\Model\Application;
use TYPO3\Surf\Domain\Model\Deployment;
use TYPO3\Surf\Domain\Model\Task;
use TYPO3\Surf\Exception\InvalidConfigurationException;

/**
 * A task to reset the PHP opcache by executing a prepared script with an HTTP request
 */
class WebOpcacheResetExecuteTask extends Task {

	/**
	 * Execute this task
	 *
	 * @param \TYPO3\Surf\Domain\Model\Node $node
	 * @param \TYPO3\Surf\Domain\Model\Application $application
	 * @param \TYPO3\Surf\Domain\Model\Deployment $deployment
	 * @param array $options Supported options: "baseUrl" (required) and "scriptIdentifier" (is passed by the create script task)
	 * @return void
	 * @throws \TYPO3\Surf\Exception\InvalidConfigurationException
	 * @throws \TYPO3\Surf\Exception\TaskExecutionException
	 */
	public function execute(Node $node, Application $application, Deployment $deployment, array $options = array()) {
		if (!isset($options['baseUrl'])) {
			throw new InvalidConfigurationException('No "baseUrl" option provided for WebOpcacheResetExecuteTask', 1421932609);
		}
		if (!isset($options['scriptIdentifier'])) {
			throw new InvalidConfigurationException('No "scriptIdentifier" option provided for WebOpcacheResetExecuteTask, make sure to execute "typo3.surf:php:webopcacheresetcreatescript" before this task or pass one explicitly', 1421932610);
		}

		$scriptIdentifier = $options['scriptIdentifier'];
		$scriptUrl = rtrim($options['baseUrl'], '/') . '/surf-opcache-reset-' . $scriptIdentifier . '.php';

		$result = file_get_contents($scriptUrl);
		if ($result !== 'success') {
			$deployment->getLogger()->log('Executing PHP opcache reset script at "' . $scriptUrl . '" did not return expected result', LOG_WARNING);
		}
	}

}
