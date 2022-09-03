<?php

declare(strict_types=1);

namespace DmitryRechkin\GraphQL\Schema\FileParser;

class CacheFilePathBuilder
{
	/**
	 * @var string
	 */
	private $targetDirectoryPath;

	/**
	 * @var string
	 */
	private $sourceFilePath;

	/**
	 * constructor
	 */
	public function __construct()
	{
		$this->targetDirectoryPath = sys_get_temp_dir();
		$this->sourceFilePath = '';
	}

	/**
	 * sets target directory where cache file should be located
	 *
	 * @param string $targetDirectoryPath
	 * @return self
	 */
	public function withTargetDirectoryPath(string $targetDirectoryPath): self
	{
		$this->targetDirectoryPath = rtrim($targetDirectoryPath, PATH_SEPARATOR);
		return $this;
	}

	/**
	 * sets source file path
	 *
	 * @param string $sourceFilePath
	 * @return self
	 */
	public function withSourceFilePath(string $sourceFilePath): self
	{
		$this->sourceFilePath = rtrim($sourceFilePath, PATH_SEPARATOR);
		return $this;
	}

	/**
	 * returns path to a cache file
	 *
	 * @return string
	 */
	public function build(): string
	{
		return $this->targetDirectoryPath . '/' . md5($this->sourceFilePath);
	}
}
