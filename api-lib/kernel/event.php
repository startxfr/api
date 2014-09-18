<?php
/**
 * Simple class for handling event in php
 *
 * ex: Event::bind('input.preprocess', function($args = array()) { ... });\n
 * ex: Event::trigger('input.preprocess', $data);
 *
 * @class Event
 * @author dev@startx.fr
 */
class Event {

    public static $events = array();

    public static function trigger($event, $args = array()) {
        if (isset(self::$events[$event])) {
            foreach (self::$events[$event] as $func) {
                call_user_func($func, $args);
            }
        }
    }

    public static function bind($event, $func) {
        self::$events[$event][] = $func;
    }

}
?>