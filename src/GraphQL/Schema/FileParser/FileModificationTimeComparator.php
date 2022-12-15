<?php

declare(strict_types=1);

namespace DmitryRechkin\GraphQL\Schema\FileParser;

class FileModificationTimeComparator
{
	/**
	 * @var string
	 */
	private $filePath;

	/**
	 * constructor
	 */
	public function __construct()
	{
		$this->filePath = '';
	}

	/**
	 * sets file we will be comparing against
	 *
	 * @param string $filePath
	 * @return FileModificationTimeComparator
	 */
	public function forFile(string $filePath): FileModificationTimeComparator
	{
		$this->filePath = $filePath;
		return $this;
	}

	/**
	 * returns true when our file is newer than a given file
	 *
	 * @param string $compareToFilePath
	 * @return boolean
	 */
	public function isNewerThanFile(string $compareToFilePath): bool
	{
		if (false === file_exists($this->filePath)) {
			return false;
		}

		if (false === file_exists($compareToFilePath)) {
			return true;
		}

		return filemtime($this->filePath) > filemtime($compareToFilePath);
	}
}
