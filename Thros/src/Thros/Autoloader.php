<?php
namespace Thros;

class Autoloader {

	private $_directory;

	private $_prefix;

	private $_prefix_length;

	/**
	 *
	 * @param string	$baseDirectory	base directory where the source files are located
	 */
	public function __construct($baseDirectory = __DIR__) {
		$this->_directory = $baseDirectory;
		$this->_prefix = __NAMESPACE__ . '\\';
		$this->_prefix_length = strlen($this->_prefix);
	}

	/**
	 * Registers the autoloader class with the PHP SPL autoloader.
	 *
	 * @param bool $prepend	prepend the autoloader on the stack instead of appending it.
	 */
	public static function register($prepend = false) {
		spl_autoload_register(
			array(
				new self(),
				'autoload'
			),
			true,
			$prepend
		);
	}

	/**
	 * Loads a class from a file using its fully qualified name.
	 *
	 * @param string $className
	 *        	Fully qualified name of a class.
	 */
	public function autoload($className) {
		if(0 === strpos($className, $this->_prefix)) {
			$parts = explode('\\', substr($className, $this->_prefix_length));
			$filepath = $this->_directory . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $parts) . '.php';

			if(is_file($filepath)) {
				require ($filepath);
			}
		}
	}

}