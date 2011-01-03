<?php

/**
 * A tool for mapping class and interface names to files.  For use with
 * autoloading
 *
 * @category  Zend
 * @package   Polaris
 * @author    Bill Shupp <hostmaster@shupp.org>
 * @copyright 2010 Bill Shupp
 * @license   http://www.opensource.org/licenses/bsd-license.php FreeBSD
 * @link      http://github.com/shupp/polaris
 */
class Polaris_ClassMapGenerator
{
    /**
     * The find paths to use with the find command when used.  Defaults to "."
     *
     * @var string
     */
    public $findPaths = ".";

    /**
     * Storage of the complete file list to parse
     *
     * @var string
     */
    public $fileList  = null;

    /**
     * Map of class names to filenames.
     *
     * @var array
     */
    public $classMap = array();

    /**
     * Sets $fileList, indicating which files to parse
     *
     * @param string $file  Optional filename from which to get the file list
     * @param string $paths Optional string of paths to use with the "find" command
     *
     * @return void
     */
    public function __construct($file = null, $paths = null)
    {
        if ($paths != null) {
            $this->findPaths = $paths;
        }

        if ($file != null) {
            $this->fileList = file($file);
        } else {
            $list = shell_exec(
                'find ' . $this->findPaths . ' -name "*.php"'
            );
            $this->fileList = explode("\n", $list);
        }
    }

    /**
     * Generates the map and returns a serialized array map
     *
     * @return string
     */
    public function generateMap()
    {
        $this->populateClassList();
        return serialize($this->classMap);
    }

    /**
     * Iterats over the file list to populate them
     *
     * @return void
     */
    public function populateClassList()
    {
        foreach ($this->fileList as $file) {
            $this->mapClasses(trim($file));
        }
    }

    /**
     * Tokenizes a file and maps the classes and interfaces
     *
     * @param string $file The file to parse
     *
     * @return void
     */
    public function mapClasses($file)
    {
        $source = file_get_contents($file);
        $tokens = token_get_all($source);

        $haveTClass      = false;
        $haveTWhitespace = false;

        foreach ($tokens as $token) {
            if (!is_array($token) || !isset($token[1])) {
                continue;
            }
            $t = $token[0];

            // Look for T_CLASS or T_INTERFACE and mark it
            if ($t == T_CLASS || $t == T_INTERFACE) {
                $haveTClass = true;
                continue;
            }

            // Look for whitespace and mark it
            if ($t == T_WHITESPACE) {
                $haveTWhitespace = true;
                continue;
            }

            // Look for class/interface name string and map it,
            // then reset markers
            if ($haveTClass && $haveTWhitespace && $t == T_STRING) {
                $this->classMap[$token[1]] = $file;

                $haveTClass      = false;
                $haveTWhitespace = false;
                continue;
            }
        }
    }
}
