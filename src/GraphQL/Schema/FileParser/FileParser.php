<?php

declare(strict_types=1);

namespace DmitryRechkin\GraphQL\Schema\FileParser;

use GraphQL\Language\AST\DocumentNode;
use GraphQL\Language\Parser as LanguageParser;

class FileParser
{
	/**
	 * @var CacheFilePathBuilder
	 */
	private $cacheFilePathBuilder;

	/**
	 * @var FileModificationTimeComparator
	 */
	private $fileModificationTimeComparator;

	/**
	 * @var DocumentNodeStorage
	 */
	private $documentNodeStorage;

	/**
	 * constructor
	 *
	 * @param CacheFilePathBuilder $cacheFilePathBuilder
	 * @param FileModificationTimeComparator $fileModificationTimeComparator
	 * @param DocumentNodeStorage $documentNodeStorage
	 */
	public function __construct(
		CacheFilePathBuilder $cacheFilePathBuilder = null,
		FileModificationTimeComparator $fileModificationTimeComparator = null,
		DocumentNodeStorage $documentNodeStorage = null
	) {
		$this->cacheFilePathBuilder = $cacheFilePathBuilder ?? new CacheFilePathBuilder();
		$this->fileModificationTimeComparator = $fileModificationTimeComparator ?? new FileModificationTimeComparator();
		$this->documentNodeStorage = $documentNodeStorage ?? new DocumentNodeStorage();
	}

	/**
	 * parses a given GraphQL schema file returns and DocumentNode,
	 * or loads and returns previously parsed and then cached DocumentNode
	 *
	 * @param string $filePath
	 * @return DocumentNode
	 */
	public function parse(string $filePath): ?DocumentNode
	{
		if (false === file_exists($filePath)) {
			return null;
		}

		$documentNode = null;
		$cacheFilePath = $this->cacheFilePathBuilder->withSourceFilePath($filePath)->build();

		if ($this->fileModificationTimeComparator->forFile($cacheFilePath)->isNewerThanFile($filePath)) {
			$documentNode = $this->documentNodeStorage->load($cacheFilePath);
		} else {
			$documentNode = LanguageParser::parse(file_get_contents($filePath));

			$this->documentNodeStorage->save($cacheFilePath, $documentNode);
		}

		return $documentNode;
	}
}
