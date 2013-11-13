<?php

date_default_timezone_set("Europe/Paris");
/**
 * the directory separator used for this instance
 */
define('DEBUG', true);
define('LOG_VERBOSITY', 5);
/**
 * the directory separator used for this instance
 */
define('DS', DIRECTORY_SEPARATOR);
/**
 * the extention file used in this instance
 */
define('EXT', '.' . pathinfo(__FILE__, PATHINFO_EXTENSION));
/**
 * the root path of this application instance
 */
define('BASEPATH', pathinfo(pathinfo(__FILE__, PATHINFO_DIRNAME), PATHINFO_DIRNAME) . DS);
/**
 * the lib path for loading kernel components and core features
 */
define('KERNPATH', BASEPATH . 'kernel' . DS);
/**
 * the lib path for loading MVC components
 */
define('LIBPATH', BASEPATH . 'lib' . DS);
/**
 * the lib path for loading external projects (php-ga,google-api-php-client)
 */
define('LIBPATHEXT', BASEPATH . 'lib-ext' . DS);

include_once(KERNPATH . 'toolkit' . EXT);
include_once(KERNPATH . 'interfaces' . EXT);
include_once(KERNPATH . 'configurable' . EXT);
include_once(KERNPATH . 'event' . EXT);

/**
 * Function used for automatic loading of classes based on camelCase suffix.
 *
 * If class end with Resource, Model, Exception, Store, Output or Input then look into the appropriate directory.
 * If Resource is used, autoload search for a subpackage with pre-suffix founded.
 *
 * @param the classname to search for
 * @return boolean if ok. Throw an exception if not
 */
function autoloader($classname) {
    $loadingPath = "";
    $arr = preg_split('/(?=[A-Z])/', $classname);
    $suffix = array_pop($arr);
    switch ($suffix) {
        case 'Resource':
            if (count($arr) == 3)
                $loadingPath = LIBPATH . 'resources' . DS . $arr[2] .DS . $arr[1] . DS . $classname . EXT;
            elseif (count($arr) == 2)
                $loadingPath = LIBPATH . 'resources' . DS . $arr[1] . DS . $classname . EXT;
            else
                $loadingPath = LIBPATH . 'resources' . DS . $classname . EXT;
            break;
        case 'Model':
            $loadingPath = LIBPATH . 'models' . DS . $classname . EXT;
            break;
        case 'Exception':
            $loadingPath = LIBPATH . 'exception' . DS . $classname . EXT;
            break;
        case 'Store':
            $loadingPath = LIBPATH . 'stores' . DS . $classname . EXT;
            break;
        case 'Output':
            $loadingPath = LIBPATH . 'output' . DS . $classname . EXT;
            break;
        case 'Input':
            $loadingPath = LIBPATH . 'input' . DS . $classname . EXT;
            break;
        case 'Plugin':
            $loadingPath = LIBPATH . 'plugins' . DS . $classname . EXT;
            break;
        default:
            break;
    }
    if ((@include_once $loadingPath) == false)
//        throw new Exception("could not load $classname Class. File " . $loadingPath . " not found");
        return false;
    else
        return true;
}
spl_autoload_register('autoloader');

include_once(KERNPATH . 'api' . EXT);
?>