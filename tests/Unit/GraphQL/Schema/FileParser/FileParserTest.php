<?php

declare(strict_types=1);

namespace DmitryRechkin\Tests\Unit\GraphQL\Schema\FileParser;

use DmitryRechkin\GraphQL\Schema\FileParser\CacheFilePathBuilder;
use DmitryRechkin\GraphQL\Schema\FileParser\DocumentNodeStorage;
use DmitryRechkin\GraphQL\Schema\FileParser\FileModificationTimeComparator;
use DmitryRechkin\GraphQL\Schema\FileParser\FileParser;
use GraphQL\Language\AST\DocumentNode;
use PHPUnit\Framework\TestCase;

class FileParserTest extends TestCase
{
	/**
	 * @var string
	 */
	private $schemaFile;

	/**
	 * @return void
	 */
	public function setUp(): void
	{
		$this->schemaFile = $this->createSchemaFile();
	}

	/**
	 * @return void
	 */
	public function tearDown(): void
	{
		unlink($this->schemaFile);
	}

	/**
	 * @return void
	 */
	public function testParseReturnsInstanceOfDocumentNodeForNewAndCacheExistingFile(): void
	{
		$fileParser = new FileParser();

		$this->assertInstanceOf(DocumentNode::class, $fileParser->parse($this->schemaFile));
		$this->assertInstanceOf(DocumentNode::class, $fileParser->parse($this->schemaFile));
	}

	/**
	 * @return void
	 */
	public function testParseReturnsNullForNonExistingFile(): void
	{
		$fileParser = new FileParser();
		$this->assertNull($fileParser->parse('non-existing-file'));
	}

	/**
	 * @return void
	 */
	public function testParseWillNotCallAnythingForNonExistingFile(): void
	{
		$cacheFilePathBuilderMock = $this->createMock(CacheFilePathBuilder::class);

		$cacheFilePathBuilderMock
			->expects($this->exactly(0))
			->method('withSourceFilePath');

		$cacheFilePathBuilderMock
			->expects($this->exactly(0))
			->method('build');

		$fileModificationTimeComparatorMock = $this->createMock(FileModificationTimeComparator::class);

		$fileModificationTimeComparatorMock
			->expects($this->exactly(0))
			->method('forFile');

		$fileModificationTimeComparatorMock
			->expects($this->exactly(0))
			->method('isNewerThanFile');

		$documentNodeStorageMock = $this->createMock(DocumentNodeStorage::class);

		$documentNodeStorageMock
			->expects($this->exactly(0))
			->method('save');

		$documentNodeStorageMock
			->expects($this->exactly(0))
			->method('load');

		$fileParser = new FileParser(
			$cacheFilePathBuilderMock,
			$fileModificationTimeComparatorMock,
			$documentNodeStorageMock
		);

		$fileParser->parse('non-existing-file');
	}

	/**
	 * @return void
	 */
	public function testParseSavesNewlyParseDocumentNode(): void
	{
		$documentNodeStorageMock = $this->createMock(DocumentNodeStorage::class);
		$documentNodeStorageMock
			->expects($this->once())
			->method('save');

		$fileParser = new FileParser(
			$this->createCacheFilePathBuilderMock(),
			$this->createFileModificationTimeComparatorMock(false),
			$documentNodeStorageMock
		);

		$fileParser->parse($this->schemaFile);
	}

	/**
	 * @return void
	 */
	public function testParseLoadsPreviouslySavedDocumentNode(): void
	{
		$documentNodeStorageMock = $this->createMock(DocumentNodeStorage::class);
		$documentNodeStorageMock
			->expects($this->once())
			->method('load');

		$fileParser = new FileParser(
			$this->createCacheFilePathBuilderMock(),
			$this->createFileModificationTimeComparatorMock(true),
			$documentNodeStorageMock
		);

		$fileParser->parse($this->schemaFile);
	}

	/**
	 * @param bool $isNewerThanFile
	 * @return FileModificationTimeComparator
	 */
	private function createFileModificationTimeComparatorMock(bool $isNewerThanFile): FileModificationTimeComparator
	{
		$fileModificationTimeComparatorMock = $this->createMock(FileModificationTimeComparator::class);
		$fileModificationTimeComparatorMock
			->expects($this->once())
			->method('forFile')
			->with((new CacheFilePathBuilder())->withSourceFilePath($this->schemaFile)->build())
			->willReturn($fileModificationTimeComparatorMock);

		$fileModificationTimeComparatorMock
			->expects($this->once())
			->method('isNewerThanFile')
			->with($this->schemaFile)
			->willReturn($isNewerThanFile);

		return $fileModificationTimeComparatorMock;
	}

	/**
	 * @return CacheFilePathBuilder
	 */
	private function createCacheFilePathBuilderMock(): CacheFilePathBuilder
	{
		$cacheFilePathBuilderMock = $this->createMock(CacheFilePathBuilder::class);
		$cacheFilePathBuilderMock
			->expects($this->once())
			->method('withSourceFilePath')
			->with($this->schemaFile)
			->willReturn($cacheFilePathBuilderMock);

		$cacheFilePathBuilderMock
			->expects($this->once())
			->method('build')
			->willReturn((new CacheFilePathBuilder())->withSourceFilePath($this->schemaFile)->build());

		return $cacheFilePathBuilderMock;
	}

	/**
	 * @return string
	 */
	private function createSchemaFile(): string
	{
		$filePath = tempnam(sys_get_temp_dir(), 'Schema');
		$contents = <<<SCHEMA
		query HeroNameQuery {
			hero {
				name
			}
		}
		SCHEMA;

		file_put_contents($filePath, $contents);

		return $filePath;
	}
}
