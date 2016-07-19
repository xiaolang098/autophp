<?php

/**
 * @author ricolau<ricolau@qq.com>
 * @version 2016-04-29
 * @desc autophp plugin tool
 *
 */
final class plugin {

    private static $_plugins = array();
    private static $_pluginsHasRun = array();
    private static $_allPlugins = array();

    const type_before_run = 'before_run';
//    const type_after_run = 'after_run';
    const type_after_run = 'shutdown';

    /**
     * add a plugin for run
     * @param str $pluginName
     * @param str $type
     */
    public static function add($pluginName, $type = 'before_run') {
        self::$_plugins[$type][] = $pluginName;
        self::$_allPlugins[$type][] = $pluginName;
    }

    /**
     * run plugins of this type
     * @param type $type
     */
    public static function run($type, plugin_context &$ptx) {
        if (isset(self::$_plugins[$type]) && is_array(self::$_plugins[$type])) {
            while (self::$_plugins[$type]) {
                $plugin = array_shift(self::$_plugins[$type]);
                $_debugMicrotime = microtime(true);
                auto::isDebug() && auto::debugMsg(__METHOD__ . " ('$plugin') ", 'start ---->>>>');
                self::_execPlugin($plugin, $ptx);
                ($timeCost = microtime(true) - $_debugMicrotime) && auto::performance(__METHOD__, $timeCost, array('plugin'=>$plugin)) && auto::isDebug() && auto::debugMsg(__METHOD__ . " ('$plugin') ", 'end,<<<<---- cost ' . $timeCost . 's');
                self::$_pluginsHasRun[] = $plugin;
            }
        }
    }

    /**
     * run a plugin
     * @param type $plugin
     */
    private static function _execPlugin($className, &$ptx) {
        if (!class_exists($className)){
            throw new exception_base('class not exist:' . $className, -1);
        }

        $class = new ReflectionClass($className);
        if ($class->isAbstract()) {
            throw new exception_base('can not run abstract class: ' . $className, -1);
        }
        if (!$class->isSubclassOf('plugin_abstract')) {
            throw new exception_base('plugin '.$className .'must extends of plugin_abstract', -1);
        }
        $method = $class->getMethod('main');
        if (!$method || !$method->isPublic()) {
            throw new exception_base('no public method main exist in:' . $className, -1);
        }
        $method->invoke($class->newInstance(), $ptx);
    }


    public static function getAllPlugins() {
        return self::$_allPlugins;
    }

    public static function getHasRunPlugin($type) {
        if (!$type) {
            return;
        }
        return self::$_pluginsHasRun[$type];

    }

    public static function getHasNotRunPlugin($type) {
        if (!$type) {
            return;
        }
        return self::$_plugins[$type];

    }

}