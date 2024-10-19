<?php // phpcs:ignore WordPress.Files.FileName
/**
 * Grammar interface.
 *
 * @package   Sematico\Baselibs\Schema
 * @author    Alessandro Tesoro <alessandro.tesoro@icloud.com>
 * @copyright 2024 Sematico
 * @license   GPL-3.0-or-later
 */

namespace Sematico\Baselibs\Schema\Grammar;

use Sematico\Baselibs\Schema\Schema;

interface GrammarInterface {
	/**
	 * Compile the given schema into a string representation.
	 *
	 * @param Schema $schema The schema to compile.
	 * @return string The compiled schema as a string.
	 */
	public function compile( Schema $schema ): string;
}
