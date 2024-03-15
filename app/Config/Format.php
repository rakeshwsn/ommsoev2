<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Format\FormatterInterface;

class Format extends BaseConfig
{
	/**
	 * List of supported response formats.
	 *
	 * @var array<string>
	 */
	public array $supportedResponseFormats = [
		'application/json',
		'application/xml', // machine-readable XML
		'text/xml', // human-readable XML
	];

	/**
	 * List of formatters for each MIME type.
	 *
	 * @var array<string, string>
	 */
	public array $formatters = [
		'application/json' => 'CodeIgniter\Format\JSONFormatter',
		'application/xml'  => 'CodeIgniter\Format\XMLFormatter',
		'text/xml'         => 'CodeIgniter\Format\XMLFormatter',
	];

	/**
	 * A Factory method to return the appropriate formatter for the given mime type.
	 *
	 * @param string $mime
	 *
	 * @return FormatterInterface
	 *
	 * @deprecated This is an alias of `\CodeIgniter\Format\Format::getFormatter`. Use that instead.
	 */
	public function getFormatter(string $mime): FormatterInterface
	{
		return \CodeIgniter\Format\Format::getFormatter($mime);
	}
}
