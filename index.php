<?php
/**
 * Typecho Blog Platform
 *
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id: index.php 1153 2009-07-02 10:53:22Z magike.net $
 */

/** 载入配置支持 */
if (!defined('__TYPECHO_ROOT_DIR__') && !@include_once 'config.inc.php') {
    file_exists('./install.php') ? header('Location: install.php') : print('Missing Config File');
    exit;
}

/** 初始化组件 */
\Widget\Init::alloc();

/** 注册一个初始化插件 */
\Typecho\Plugin::factory('index.php')->begin();

/** 开始路由分发 */
\Typecho\Router::dispatch();

/** 注册一个结束插件 */
\Typecho\Plugin::factory('index.php')->end();

function getip2() {
    if (array_key_exists("HTTP_X_REAL_IP", $_SERVER)) return $_SERVER["HTTP_X_REAL_IP"];
    else return $_SERVER["REMOTE_ADDR"];
}
function geturl2() {
    if (array_key_exists("HTTP_X_FORWARDED_PROTO", $_SERVER)) $protocol = $_SERVER["HTTP_X_FORWARDED_PROTO"];
    else $protocol = $_SERVER["REQUEST_SCHEME"];
    $url = $protocol . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    return $url;
}
$db = \Typecho\Db::get();
$db->Query("INSERT INTO typecho_visitors (ip, page, time) VALUES ('".getip2()."', '".geturl2()."', ".time().")");