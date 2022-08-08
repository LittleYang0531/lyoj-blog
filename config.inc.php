<?php
// site root path
define('__TYPECHO_ROOT_DIR__', dirname(__FILE__));

// plugin directory (relative path)
define('__TYPECHO_PLUGIN_DIR__', '/usr/plugins');

// theme directory (relative path)
define('__TYPECHO_THEME_DIR__', '/usr/themes');

// admin directory (relative path)
define('__TYPECHO_ADMIN_DIR__', '/admin/');

// register autoload
require_once __TYPECHO_ROOT_DIR__ . '/var/Typecho/Common.php';

// init
\Typecho\Common::init();

// config db
$db = new \Typecho\Db('Mysqli', 'typecho_');
// $db->addServer(array (
//   'host' => '127.0.0.1',
//   'port' => 3306,
//   'user' => 'root',
//   'password' => 'yangweihao20060531',
//   'charset' => 'utf8mb4',
//   'database' => 'typecho',
//   'engine' => 'InnoDB',
// ), \Typecho\Db::READ | \Typecho\Db::WRITE);
$db->addServer(array (
  'host' => '3yshbbnym7dz.ap-northeast-2.psdb.cloud',
  'port' => 3306,
  'user' => 'rvl6dv3akeqi',
  'password' => 'pscale_pw_rxXPgxgnHY6bpVgVRRiN2msdX-M2AZIZZv33SdRtkw0',
  'charset' => 'utf8mb4',
  'database' => 'typecho',
  'engine' => 'InnoDB',
), \Typecho\Db::READ | \Typecho\Db::WRITE);
\Typecho\Db::set($db);
