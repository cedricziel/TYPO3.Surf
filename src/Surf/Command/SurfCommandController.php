<?php
namespace TYPO3\Surf\Command;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Symfony\Component\Console\Command\Command;
use TYPO3\Surf\Domain\Service\DeploymentService;
use TYPO3\Surf\Log\Backend\AnsiConsoleBackend;

/**
 * Surf command controller
 */
class SurfCommandController extends Command {

	/**
	 * @var \TYPO3\Surf\Domain\Service\DeploymentService
	 */
	protected $deploymentService;

	public function __construct() {
		parent::__construct();
		$this->deploymentService = new DeploymentService();
	}

	/**
	 * Create a default logger with console and file backend
	 *
	 * @param string $deploymentName
	 * @param integer $severityThreshold
	 * @param boolean $disableAnsi
	 * @param boolean $addFileBackend
	 * @return \Monolog\Logger
	 */
	public function createDefaultLogger($deploymentName, $severityThreshold, $disableAnsi = FALSE, $addFileBackend = TRUE) {
		$logger = new \Monolog\Logger('Surf');
		$console = new AnsiConsoleBackend(array(
			'severityThreshold' => $severityThreshold,
			'disableAnsi' => $disableAnsi
		));
		$logger->pushHandler($console);
		if ($addFileBackend) {
			$logger->pushHandler(new StreamHandler(__DIR__ . '/logs/'. $deploymentName . '.log', Logger::DEBUG));
		}
		return $logger;
	}

	/**
	 * Simulate a deployment
	 *
	 * @param string $deploymentName The deployment name
	 * @param boolean $verbose In verbose mode, the log output of the default logger will contain debug messages
	 * @param boolean $disableAnsi Disable ANSI formatting of output
	 * @param string $configurationPath Path for deployment configuration files
	 * @return void
	 */
	public function simulateCommand($deploymentName, $verbose = FALSE, $disableAnsi = FALSE, $configurationPath = NULL) {
		$deployment = $this->deploymentService->getDeployment($deploymentName, $configurationPath);
		if ($deployment->getLogger() === NULL) {
			$logger = $this->createDefaultLogger($deploymentName, $verbose ? LOG_DEBUG : LOG_INFO, $disableAnsi, FALSE);
			$deployment->setLogger($logger);
		}
		$deployment->initialize();

		$deployment->simulate();
	}

}
