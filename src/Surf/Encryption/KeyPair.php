<?php
namespace TYPO3\Surf\Encryption;

/*                                                                        *
 * This script belongs to the package "TYPO3.Surf".                       *
 *                                                                        *
 *                                                                        */

/**
 * Key pair
 *
 * A key pair consists of a public key and an open or encrypted private key.
 */
class KeyPair {

	/**
	 * @var string
	 */
	protected $privateKey;

	/**
	 * @var string
	 */
	protected $publicKey;

	/**
	 * @var boolean
	 */
	protected $encrypted;

	/**
	 * Constructor
	 *
	 * @param string $privateKey A PEM encoded private key
	 * @param string $publicKey A PEM encoded public key
	 * @param boolean $encrypted Pass TRUE if the private key is encrypted
	 */
	public function __construct($privateKey, $publicKey, $encrypted = FALSE) {
		$this->privateKey = $privateKey;
		$this->publicKey = $publicKey;
		$this->encrypted = $encrypted;
	}

	/**
	 * @return string
	 */
	public function getPrivateKey() {
		return $this->privateKey;
	}

	/**
	 * @return string
	 */
	public function getPublicKey() {
		return $this->publicKey;
	}

	/**
	 * @return boolean
	 */
	public function isEncrypted() {
		return $this->encrypted;
	}

}
