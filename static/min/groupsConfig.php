<?php
/**
 * Groups configuration for default Minify implementation
 * @package Minify
 */

/** 
 * You may wish to use the Minify URI Builder app to suggest
 * changes. http://yourdomain/min/builder/
 **/

return array(
    'za-libjs' => array(
	'//za/jss/script.aculo/js/resizable.js',
	'//za/jss/script.aculo/js/menu.js',
	'//za/jss/script.aculo/js/accordion.js',
	'//za/jss/prototip/js/prototip.js',
	'//za/jss/JSCal2-1.0/js/jscal2.js',
	'//za/jss/JSCal2-1.0/js/lang/fr.js',
	'//za/jss/defaut/tooltip.js'),
    'za-libcss' => array(
	'//za/jss/prototip/css/prototip.css',
	'//za/jss/JSCal2-1.0/css/jscal2.css',
	'//za/jss/JSCal2-1.0/css/border-radius.css',
	'//za/jss/JSCal2-1.0/css/reduce-spacing.css',
	'//za/jss/defaut/init.css'),

    'zm-libjs' => array(
	'//zm/jss/script.aculo/js/resizable.js',
	'//zm/jss/script.aculo/js/menu.js',
	'//zm/jss/script.aculo/js/accordion.js',
	'//zm/jss/prototip/js/prototip.js',
	'//zm/jss/JSCal2-1.0/js/jscal2.js',
	'//zm/jss/JSCal2-1.0/js/lang/fr.js',
	'//zm/jss/defaut/tooltip.js'),
    'zm-libcss' => array(
	'//zm/jss/prototip/css/prototip.css',
	'//zm/jss/JSCal2-1.0/css/jscal2.css',
	'//zm/jss/JSCal2-1.0/css/border-radius.css',
	'//zm/jss/JSCal2-1.0/css/reduce-spacing.css',
	'//zm/jss/defaut/init.css'),

    'zs-libjs' => array(
	'//zs/jss/script.aculo/js/resizable.js',
	'//zs/jss/script.aculo/js/menu.js',
	'//zs/jss/script.aculo/js/accordion.js',
	'//zs/jss/prototip/js/prototip.js',
	'//zs/jss/JSCal2-1.0/js/jscal2.js',
	'//zs/jss/JSCal2-1.0/js/lang/fr.js',
	'//zs/jss/defaut/tooltip.js'),
    'zs-libcss' => array(
	'//zs/jss/prototip/css/prototip.css',
	'//zs/jss/JSCal2-1.0/css/jscal2.css',
	'//zs/jss/JSCal2-1.0/css/border-radius.css',
	'//zs/jss/JSCal2-1.0/css/reduce-spacing.css',
	'//zs/jss/defaut/init.css')
    // 'js' => array('//js/file1.js', '//js/file2.js'),
    // 'css' => array('//css/file1.css', '//css/file2.css'),

    // custom source example
    /*'js2' => array(
        dirname(__FILE__) . '/../min_unit_tests/_test_files/js/before.js',
        // do NOT process this file
        new Minify_Source(array(
            'filepath' => dirname(__FILE__) . '/../min_unit_tests/_test_files/js/before.js',
            'minifier' => create_function('$a', 'return $a;')
        ))
    ),//*/

    /*'js3' => array(
        dirname(__FILE__) . '/../min_unit_tests/_test_files/js/before.js',
        // do NOT process this file
        new Minify_Source(array(
            'filepath' => dirname(__FILE__) . '/../min_unit_tests/_test_files/js/before.js',
            'minifier' => array('Minify_Packer', 'minify')
        ))
    ),//*/
);