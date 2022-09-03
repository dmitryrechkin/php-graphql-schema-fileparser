<?php

declare(strict_types=1);

namespace DmitryRechkin\Tests\Unit\GraphQL\Schema\FileParser;

use DmitryRechkin\GraphQL\Schema\FileParser\DocumentNodeStorage;
use GraphQL\Language\AST\DocumentNode;
use PHPUnit\Framework\TestCase;

class DocumentNodeStorageTest extends TestCase
{
	/**
	 * @return void
	 */
	public function testSaveFollowedByLoadReturnsDocumentNode(): void
	{
		$filePath = sys_get_temp_dir() . '/document-node-storage-test.php';

		$documentNodeStorage = new DocumentNodeStorage();
		$this->assertTrue($documentNodeStorage->save($filePath, new DocumentNode([])));
		$this->assertInstanceOf(DocumentNode::class, $documentNodeStorage->load($filePath));

		unlink($filePath);
	}
}
