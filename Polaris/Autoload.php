<?php

/**
 * Autoloader to work with a map of classes to filename, with an
 * optional prefix
 * 
 * @category  Zend
 * @package   Polaris
 * @author    Bill Shupp <hostmaster@shupp.org> 
 * @copyright 2010 Bill Shupp
 * @license   http://www.opensource.org/licenses/bsd-license.php FreeBSD
 * @link      http://github.com/shupp/polaris
 */
class Polaris_Autoload
{
    /**
     * The map of class names (keys) to filenames (values)
     * 
     * @var array
     */
    public $map = array();

    /**
     * An optional prefix for file paths.  Useful if you want to use an
     * application in multiple locations with a pre-generated map.
     * 
     * @var string
     */
    public $prefix = null;

    /**
     * Loads up the maps and sets the prefix
     * 
     * @param mixed $mapFile The file to load the maps from (serialized array)
     * @param mixed $prefix  The optional prefix, defaults to null
     * 
     * @return void
     */
    public function __construct($mapFile, $prefix = null)
    {
        $mapContents  = file_get_contents($mapFile);
        $this->map    = unserialize($mapContents);
        $this->prefix = $prefix;
    }

    /**
     * Loads the file if the class key exists in the map.  Fails silently to
     * allow other autoloaders to run.
     * 
     * @param string $class The class to load
     * 
     * @return void
     */
    public function autoload($class)
    {
        if (isset($this->map[$class])) {
            require_once $this->prefix . $this->map[$class];
        }
    }
}
