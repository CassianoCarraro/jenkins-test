<?php

class AutoLoader {
	static public $classNames = array();

	public static function registerDirectory($name) {
		$dirI = new DirectoryIterator($name);

		foreach ($dirI as $file) {
			if ($file->isDir() && ! $file->isLink() && ! $file->isDot()) {
				self::registerDirectory($file->getPathName());
			} else if ($file->getExtension() === "php") {
				$classesNames = AutoLoader::getFileClasses($file->getPathName());
				foreach ($classesNames as $className) {
					AutoLoader::registerClass($className, $file->getPathName());
				}
			}
		}
	}

	public static function registerClass($className, $fileName) {
		AutoLoader::$classNames[$className] = $fileName;
	}

	public static function loadClass($className) {
		if (isset(AutoLoader::$classNames[$className])) {
			require_once(AutoLoader::$classNames[$className]);
		}
	}

	private static function getFileClasses($filePath) {
		$code = file_get_contents($filePath);
		$classes = AutoLoader::getPHPClasses($code);

		return $classes;
	}

	private static function getPHPClasses($phpCode) {
		$classes = array();
		$tokens = token_get_all($phpCode);
		$namespace = '';
		$namespacing = false;

		for ($i = 2; $i < count($tokens); $i++) {
			if ($tokens[$i - 1][0] === T_NAMESPACE) {
				$namespacing = true;
			}

			if ($tokens[$i] === ';' && $namespacing) {
				$namespace .= '\\';
				$namespacing = false;
			}

			if ($namespacing && $tokens[$i][0] !== T_WHITESPACE) {
				$namespace .= $tokens[$i][1];
			}

			if (($tokens[$i - 2][0] === T_CLASS || $tokens[$i - 2][0] === T_INTERFACE) && $tokens[$i - 1][0] === T_WHITESPACE && $tokens[$i][0] === T_STRING){
				$className = $tokens[$i][1];
				$classes[] = $namespace . $className;
			}
		}

		return $classes;
	}
}

spl_autoload_extensions('.php');
spl_autoload_register(array('AutoLoader', 'loadClass'));