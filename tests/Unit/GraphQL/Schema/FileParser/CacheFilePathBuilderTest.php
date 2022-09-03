<?php

declare(strict_types=1);

namespace DmitryRechkin\Tests\Unit\GraphQL\Schema\FileParser;

use DmitryRechkin\GraphQL\Schema\FileParser\CacheFilePathBuilder;
use PHPUnit\Framework\TestCase;

class CacheFilePathBuilderTest extends TestCase
{
	/**
	 * @return void
	 */
	public function testWithTargetDirectoryReturnsSelf(): void
	{
		$cacheFilePathBuilder = new CacheFilePathBuilder();
		$this->assertSame($cacheFilePathBuilder, $cacheFilePathBuilder->withTargetDirectoryPath(__DIR__));
	}

	/**
	 * @return void
	 */
	public function testWithSourceFilePathReturnsSelf(): void
	{
		$cacheFilePathBuilder = new CacheFilePathBuilder();
		$this->assertSame($cacheFilePathBuilder, $cacheFilePathBuilder->withSourceFilePath('somefile.php'));
	}

	/**
	 * @return void
	 */
	public function testBuildReturnsExpectedPath(): void
	{
		$targetDirectoryPath = __DIR__;
		$sourceFilePath = __DIR__ . '/test.php';
		$expectedPath = $targetDirectoryPath . '/' . md5($sourceFilePath);

		$cacheFilePathBuilder = new CacheFilePathBuilder();
		$cacheFilePathBuilder->withTargetDirectoryPath($targetDirectoryPath);
		$cacheFilePathBuilder->withSourceFilePath($sourceFilePath);

		$this->assertSame($expectedPath, $cacheFilePathBuilder->build());
	}
}
