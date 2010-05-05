<?php

/**
 * Class Singleton is a generic implementation of the singleton design pattern.
 *
 * Extending this class allows to make a single instance easily accessible by
 * many other objects.
 *
 * @author     Quentin Berlemont <quentinberlemont@gmail.com>
 */
abstract class Singleton
{
    protected static $_instance; // Pour php < 5.3

    /**
     * Prevents direct creation of object.
     *
     * @param  void
     * @return void
     */
    private function __construct() {}

    /**
     * Prevents to clone the instance.
     *
     * @param  void
     * @return void
     */
    final private function __clone() {}

    /**
     * Gets a single instance of the class the static method is called in.
     *
     * See the {@link http://php.net/lsb Late Static Bindings} feature for more
     * information. Only for PHP 5.3
     * 
     * @param  void
     * @return object Returns a single instance of the class.
     */
    /*final static public function getInstance()
    {
        static $instance = null;
	$firstCall = is_null($instance);
	$ins = $instance ?: $instance = new static;
	if($firstCall and method_exists($instance,'initSingleton'))
	    $ins->initSingleton();
	return $ins;
    }*/

    /**
     * Renvoi de l'instance et initialisation si nécessaire.
     * Pour php < 5.3
     */
    public static function getInstance () {
        if (!(self::$_instance instanceof self))
            self::$_instance = new self();

        return self::$_instance;
    }

}


?>