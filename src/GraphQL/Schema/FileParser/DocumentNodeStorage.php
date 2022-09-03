<?php

declare(strict_types=1);

namespace DmitryRechkin\GraphQL\Schema\FileParser;

use GraphQL\Language\AST\DocumentNode;
use GraphQL\Utils\AST;

class DocumentNodeStorage
{
	/**
	 * loads a document node from a give file
	 *
	 * @param string $filePath
	 * @return DocumentNode
	 */
	public function load(string $filePath): ?DocumentNode
	{
		if (false === file_exists($filePath)) {
			return null;
		}

		return AST::fromArray(require $filePath);
	}

	/**
	 * saves to a given file a given document node
	 *
	 * @param string $filePath
	 * @param DocumentNode $documentNode
	 * @return boolean
	 */
	public function save(string $filePath, DocumentNode $documentNode): bool
	{
		$contents = "<?php\nreturn " . var_export(AST::toArray($documentNode), true) . ";\n";

		return false !== file_put_contents($filePath, $contents);
	}
}
