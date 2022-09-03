<?php

declare(strict_types=1);

namespace DmitryRechkin\Tests\Unit\GraphQL\Schema\FileParser;

use DmitryRechkin\GraphQL\Schema\FileParser\FileModificationTimeComparator;
use PHPUnit\Framework\TestCase;

class FileModificationTimeComparatorTest extends TestCase
{
	/**
	 * @return void
	 */
	public function testIsNewerThanFileForNonExistingForFileReturnsFalse(): void
	{
		$fileModificationTimeComparator = new FileModificationTimeComparator();
		$this->assertFalse($fileModificationTimeComparator->isNewerThanFile(__FILE__));
	}

	/**
	 * @return void
	 */
	public function testIsNewerThanFileForNonExistingTargetFileReturnsTrue(): void
	{
		$fileModificationTimeComparator = new FileModificationTimeComparator();
		$fileModificationTimeComparator->forFile(__FILE__);
		$this->assertTrue($fileModificationTimeComparator->isNewerThanFile('non-existing-file'));
	}

	/**
	 * @return void
	 */
	public function testIsNewerThanFileForReturnsFalseWhenForFileIsOlder(): void
	{
		$fileModificationTimeComparator = new FileModificationTimeComparator();
		$fileModificationTimeComparator->forFile(__FILE__);

		$filePath = tempnam(sys_get_temp_dir(), 'foo');
		touch($filePath);

		$this->assertFalse($fileModificationTimeComparator->isNewerThanFile($filePath));

		unlink($filePath);
	}

	/**
	 * @return void
	 */
	public function testIsNewerThanFileForReturnsTrueWhenForFileIsNewer(): void
	{
		$fileModificationTimeComparator = new FileModificationTimeComparator();

		$filePath = tempnam(sys_get_temp_dir(), 'foo');
		touch($filePath);

		$fileModificationTimeComparator->forFile($filePath);
		$this->assertTrue($fileModificationTimeComparator->isNewerThanFile(__FILE__));

		unlink($filePath);
	}
}
