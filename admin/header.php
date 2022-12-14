<?php
if (!defined('__TYPECHO_ADMIN__')) {
    exit;
}

$header = '<link rel="stylesheet" href="' . $options->adminStaticUrl('css', 'normalize.css', true) . '">
<link rel="stylesheet" href="' . $options->adminStaticUrl('css', 'grid.css', true) . '">
<link rel="stylesheet" href="' . $options->adminStaticUrl('css', 'style.css', true) . '">';

/** 注册一个初始化插件 */
$header = \Typecho\Plugin::factory('admin/header.php')->header($header);

global $db;
$db = \Typecho\Db::get();

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

?><!DOCTYPE HTML>
<html>
    <head>
        <meta charset="<?php $options->charset(); ?>">
        <meta name="renderer" content="webkit">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        <title><?php _e('%s - %s - Powered by Typecho', $menu->title, $options->title); ?></title>
        <meta name="robots" content="noindex, nofollow">
        <style>
            body {
                background-repeat:no-repeat!important;
                background-size:cover!important;
                background-position:center!important;
                background-attachment:fixed!important;
                background-image:url(<?php echo __TYPECHO_ADMIN_DIR__ . "83560199_p0.jpg"; ?>)!important;
            }
        </style>
        <script>
            setTimeout(function(){
                var e = document.getElementsByClassName("container")[0];
                e.style.backgroundColor = "rgba(255, 255, 255, 0.7)";
                e.style.marginTop = "30px";
                e.style.paddingTop = "10px";
                e.style.paddingLeft = "30px";
                e.style.paddingRight = "30px";
                e.style.paddingBottom = "30px";
            }, 100);
        </script>
        <?php echo $header; ?>
    </head>
    <body <?php if (isset($bodyClass)) {echo ' class="' . $bodyClass . '"';} ?>>
