<?php
namespace TYPO3\Surf\Utility;

/* * *************************************************************
 *  Copyright notice
 *
 *  (c) 2015 Ingo Pfennigstorf
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 * ************************************************************* */
use TYPO3\Surf\Exception;

/**
 * File utilities
 */
class Files {

	static public function concatenatePaths(array $paths) {
		$resultingPath = '';
		foreach ($paths as $index => $path) {
			$path = self::getUnixStylePath($path);
			if ($index === 0) {
				$path = rtrim($path, '/');
			} else {
				$path = trim($path, '/');
			}
			if ($path !== '') {
				$resultingPath .= $path . '/';
			}
		}
		return rtrim($resultingPath, '/');
	}


	/**
	 * Returns all filenames from the specified directory. Filters hidden files and
	 * directories.
	 *
	 * @param string $path Path to the directory which shall be read
	 * @param string $suffix If specified, only filenames with this extension are returned (eg. ".php" or "foo.bar")
	 * @param boolean $returnRealPath If turned on, all paths are resolved by calling realpath()
	 * @param boolean $returnDotFiles If turned on, also files beginning with a dot will be returned
	 * @param array $filenames Internally used for the recursion - don't specify!
	 * @return array Filenames including full path
	 * @throws Exception
	 * @api
	 */
	static public function readDirectoryRecursively($path, $suffix = NULL, $returnRealPath = FALSE, $returnDotFiles = FALSE, &$filenames = array()) {
		if (!is_dir($path)) {
			throw new Exception('"' . $path . '" is no directory.', 1207253462);
		}

		$directoryIterator = new \DirectoryIterator($path);
		$suffixLength = strlen($suffix);

		foreach ($directoryIterator as $fileInfo) {
			$filename = $fileInfo->getFilename();
			if ($filename === '.' || $filename === '..' || ($returnDotFiles === FALSE && $filename[0] === '.')) {
				continue;
			}
			if ($fileInfo->isFile() && ($suffix === NULL || substr($filename, -$suffixLength) === $suffix)) {
				$filenames[] = self::getUnixStylePath(($returnRealPath === TRUE ? realpath($fileInfo->getPathname()) : $fileInfo->getPathname()));
			}
			if ($fileInfo->isDir()) {
				self::readDirectoryRecursively($fileInfo->getPathname(), $suffix, $returnRealPath, $returnDotFiles, $filenames);
			}
		}
		return $filenames;
	}

	/**
	 * Creates a directory specified by $path. If the parent directories
	 * don't exist yet, they will be created as well.
	 *
	 * @param string $path Path to the directory which shall be created
	 * @return void
	 * @throws Exception
	 * @todo Make mode configurable / make umask configurable
	 * @api
	 */
	static public function createDirectoryRecursively($path) {
		if (substr($path, -2) === '/.') {
			$path = substr($path, 0, -1);
		}
		if (is_file($path)) {
			throw new Exception('Could not create directory "' . $path . '", because a file with that name exists!', 1349340620);
		}
		if (!is_dir($path) && $path !== '') {
			$oldMask = umask(000);
			mkdir($path, 0777, TRUE);
			umask($oldMask);
			if (!is_dir($path)) {
				throw new Exception('Could not create directory "' . $path . '"!', 1170251400);
			}
		}
	}

	/**
	 * Replacing backslashes and double slashes to slashes.
	 * It's needed to compare paths (especially on windows).
	 *
	 * @param string $path Path which should transformed to the Unix Style.
	 * @return string
	 * @api
	 */
	static public function getUnixStylePath($path) {
		if (strpos($path, ':') === FALSE) {
			return str_replace(array('//', '\\'), '/', $path);
		} else {
			return preg_replace('/^([a-z]{2,}):\//', '$1://', str_replace(array('//', '\\'), '/', $path));
		}

	}
}
