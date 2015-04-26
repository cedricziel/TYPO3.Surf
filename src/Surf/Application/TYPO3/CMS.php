<?php
namespace TYPO3\Surf\Application\TYPO3;

/*                                                                        *
 * This script belongs to the package "TYPO3.Surf".                       *
 *                                                                        *
 *                                                                        */
use TYPO3\Surf\Application\BaseApplication;

/**
 * A TYPO3 CMS application template
 */
class CMS extends BaseApplication {

	/**
	 * Constructor
	 *
	 * @param string $name
	 */
	public function __construct($name = 'TYPO3 CMS') {
		parent::__construct($name);
	}

}
