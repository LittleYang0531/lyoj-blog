-- MySQL dump 10.19  Distrib 10.3.34-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: 3yshbbnym7dz.ap-northeast-2.psdb.cloud    Database: typecho
-- ------------------------------------------------------
-- Server version	8.0.23-vitess

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `typecho_comments`
--

DROP TABLE IF EXISTS `typecho_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `typecho_comments` (
  `coid` int unsigned NOT NULL AUTO_INCREMENT,
  `cid` int unsigned DEFAULT '0',
  `created` int unsigned DEFAULT '0',
  `author` varchar(150) DEFAULT NULL,
  `authorId` int unsigned DEFAULT '0',
  `ownerId` int unsigned DEFAULT '0',
  `mail` varchar(150) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `ip` varchar(64) DEFAULT NULL,
  `agent` varchar(511) DEFAULT NULL,
  `text` text,
  `type` varchar(16) DEFAULT 'comment',
  `status` varchar(16) DEFAULT 'approved',
  `parent` int unsigned DEFAULT '0',
  PRIMARY KEY (`coid`),
  KEY `cid` (`cid`),
  KEY `created` (`created`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `typecho_comments`
--

LOCK TABLES `typecho_comments` WRITE;
/*!40000 ALTER TABLE `typecho_comments` DISABLE KEYS */;
/*!40000 ALTER TABLE `typecho_comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `typecho_contents`
--

DROP TABLE IF EXISTS `typecho_contents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `typecho_contents` (
  `cid` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(150) DEFAULT NULL,
  `slug` varchar(150) DEFAULT NULL,
  `created` int unsigned DEFAULT '0',
  `modified` int unsigned DEFAULT '0',
  `text` longtext,
  `order` int unsigned DEFAULT '0',
  `authorId` int unsigned DEFAULT '0',
  `template` varchar(32) DEFAULT NULL,
  `type` varchar(16) DEFAULT 'post',
  `status` varchar(16) DEFAULT 'publish',
  `password` varchar(32) DEFAULT NULL,
  `commentsNum` int unsigned DEFAULT '0',
  `allowComment` char(1) DEFAULT '0',
  `allowPing` char(1) DEFAULT '0',
  `allowFeed` char(1) DEFAULT '0',
  `parent` int unsigned DEFAULT '0',
  `views` int DEFAULT '0',
  PRIMARY KEY (`cid`),
  UNIQUE KEY `slug` (`slug`),
  KEY `created` (`created`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `typecho_contents`
--

LOCK TABLES `typecho_contents` WRITE;
/*!40000 ALTER TABLE `typecho_contents` DISABLE KEYS */;
INSERT INTO `typecho_contents` VALUES (2,'关于','about',1659961020,1659961068,'<!--markdown-->LittleYang OnlineJudge',0,1,NULL,'page','publish',NULL,0,'1','1','1',0,1),(4,'lyoj v1.0.0 pre-release','4',1659963060,1659963104,'<!--markdown-->Released on https://github.com/lyoj-dev/lyoj\r\n\r\n<!--more-->\r\n\r\n#### 2022.8.7\r\n\r\n1. [ New Feature ] 完成首次使用的设置界面\r\n2. [ New Feature ] 在 Admin 界面中加入了 Config 选项\r\n\r\n#### 2022.8.3\r\n\r\n1. [ Bug Fixed ] 修复比赛排行榜里不会出现自己名字的情况\r\n2. [ Bug Fixed ] 修复比赛进行时可以看到各测试点反馈信息的情况\r\n3. [ Bug Fixed ] 修复比赛进行时点击评测记录里的题目会出现 Permission Denied 的问题\r\n4. [ Bug Fixed ] 修复点击 \"我的提交\" 时会出现数据库错误的问题\r\n5. [ Bug Fixed ] 修复赛时能够通过下载排行榜的形式查看各测试点反馈信息的错误\r\n6. [ Bug Fixed ] 修复下载排行榜时评测记录一直是第一次提交记录的错误\r\n7. [ Bug Fixed ] 修复下载排行榜时成绩总览板块内背景颜色与得分不符的情况\r\n\r\n#### 2022.8.2\r\n\r\n1. [ Bug Fixed ] 修复由于题目名字太长导致各界面爆炸的问题\r\n2. [ Bug Fixed ] 修复题目名字中带单引号导致题目不显示的问题\r\n\r\n#### 2022.7.19\r\n\r\n1. [ Bug Fixed ] 修复新建题目时无法使用单引号的问题\r\n\r\n#### 2022.7.16\r\n\r\n1. [ Bug Fixed ] 修复首次打开页面时显示语言与实际语言不同步的情况\r\n\r\n#### 2022.7.10\r\n\r\n1. [ New Feature ] 新增密码修改界面 (最后一个界面了)\r\n2. [ Bug Fixed ] 修复管理员添加用户时密码保存错误的情况\r\n3. [ New Feature ] 新增自定义题目编号功能\r\n4. [ Bug Fixed ] 修复查看用户已通过题目时显示的并非自定义题目编号的错误\r\n5. [ New Feature ] 新增管理员可以修改所有人信息的功能\r\n\r\n#### 2022.6.28\r\n\r\n1. [ Bug Fixed ] 写完了之前所有被覆盖的代码\r\n2. [ Bug Fixed ] 将所有链接形式从 `location.href` 改为 `<a></a>`\r\n3. [ New Feature ] 新增一键安装脚本\r\n4. [ New Feature ] 新增管理员用户管理功能\r\n\r\n#### 2022.6.23\r\n\r\n1. [ New Feature ] 发布 LYOJ 日语版本\r\n\r\n#### 2022.6.22\r\n\r\n1. [ New Feature ] 发布 LYOJ 中文版本\r\n2. [ New Feature ] 发布 LYOJ 英文版本\r\n3. [ New Feature ] 新增多语言功能\r\n\r\n#### 2022.6.21\r\n\r\n1. [ Bug Fixed ] 重构比赛系统中的排名界面和定时任务中的排名计算\r\n\r\n#### 2022.6.20\r\n\r\n1. [ Bug Fixed ] 修改 rp 机制为 rating 机制\r\n2. [ New Feature ] 新增用户界面比赛记录功能\r\n3. [ Bug Fixed ] 更新上方的工具栏样式\r\n\r\n#### 2022.5.29\r\n\r\n1. [ Bug Fixed ] 延长动画播放时间\r\n\r\n#### 2022.5.28\r\n\r\n1. [ New Feature ] 新增对 testlib 的支持\r\n\r\n#### 2022.5.27\r\n\r\n1. [ New Feature ] 新增管理员比赛管理界面\r\n2. [ Bug Fixed ] 修复刷新页面时会自动下滑的问题\r\n\r\n#### 2022.5.26\r\n\r\n1. [ Bug Fixed ] 修改部分 API 参数与返回值标准\r\n2. [ New Feature ] 新增一道题能够被添加到多个比赛的功能，废弃一道题只能被添加到一个比赛的功能\r\n\r\n#### 2022.5.24\r\n\r\n1. [ Bug Fixed ] 修改 Markdown 中 Latex 解析后的字体大小问题\r\n2. [ Bug Fixed ] 解决某些 Latex 解析后出现特殊字符被 HTML 转义的问题\r\n\r\n#### 2022.5.19\r\n\r\n1. [ Bug Fixed ] 修改评测机处理输入文件的方式由复制变为建立链接以减少由于处理输入文件导致的卡顿\r\n\r\n#### 2022.5.18\r\n\r\n1. [ New Feature ] 新增评测机子任务及子任务依赖功能\r\n2. [ Bug Fixed ] 修改 status 及 admin 界面的 GUI\r\n\r\n#### 2022.5.14\r\n\r\n1. [ New Feature ] 完成管理员题目管理界面\r\n\r\n#### 2022.5.11\r\n\r\n1. [ New Feature ] 新增管理员能够查看到所有题目信息的功能\r\n\r\n#### 2022.5.4\r\n\r\n1. [ Bug Fixed ] 修复评测结果页面中无结果时产生的程序错误问题\r\n2. [ Bug Fixed ] 修复比赛结果对应测评结果跳转链接有误产生的问题\r\n\r\n#### 2022.5.2\r\n\r\n1. [ Bug Fixed ] 修复无数据时时间限制和空间限制显示有误的问题\r\n2. [ Bug Fixed ] 修复无样例数据时依然显示的问题\r\n\r\n#### 2022.5.1\r\n\r\n1. [ New Feature ] 完成对用户设置界面的编写\r\n2. [ Bug Fixed ] 修改第三方组件 Editor.md 无法加载 Plugins 以及无法显示 Emoji 的问题\r\n3. [ Bug Fixed ] 修改 Editor.md 默认开启所有功能\r\n\r\n#### 2022.4.30\r\n\r\n1. [ New Feature ] 完成对用户个人信息界面的编写\r\n2. [ New Feature ] 创建针对于 rp 的计算方式($rp=round(\\sin(\\frac{n_1}{2000}\\times\\frac{\\pi}{2})\\times 1000)+\\sum \\lfloor 50\\times k^{rank}\\rfloor,k=50^{-2/n_2}$，其中 $n_1$ 代表当前已通过的题数，$n_2$ 代表当前比赛参加人数，$rank$ 代表当前已参加的比赛的相同分数的最大排名，第二个式子中的每一项能加的前提条件为某一项比赛 rated 了)\r\n3. [ Bug Fixed ] 调整 url_rewrite 的格式\r\n4. [ Bug Fixed ] 修改了页面动画的播放时间，使页面动画更加流畅\r\n\r\n#### 2022.4.28\r\n\r\n1. [ New Feature ] 新增跳至第一页/最后一页的功能\r\n2. [ Bug Fixed ] 调整提交记录中最小化的 html 代码样式\r\n\r\n#### 2022.4.21\r\n\r\n1. [ New Feature ] 新增了比赛报告下载功能 (LemonLime result.html 文件风格)\r\n\r\n#### 2022.4.13\r\n\r\n1. [ New Feature ] 修复了评测机为扩大栈空间而使用脚本运行方式时所产生的无法获取程序运行内存问题\r\n2. [ New Feature ] 新增了**标准输入输出**测评功能\r\n\r\n#### 2022.4.11\r\n\r\n1. [ New Feature ] 新增了超时直接跳过测试的功能\r\n\r\n#### 2022.4.8\r\n\r\n1. [ Bug Fixed ] 修改了所有除 footer 以外的链接均为页内链接\r\n2. [ Bug Fixed ] 修改了比赛类型为 OI 时的时间显示\r\n3. [ Bug Fixed ] 修改了比赛结束后提交比赛试题依然显示在比赛内的问题\r\n4. [ New Feature ] 新增重测功能(如有需要，请找管理员进行重测)\r\n5. [ Bug Fixed ] 修改了动画播放时间\r\n6. [ New Feature ] 新增评测信息用户名搜索功能\r\n7. [ New Feature ] 新增页面计算时间显示\r\n\r\n#### 2022.4.7\r\n\r\n1. [ New Feature ] 新增了快捷查看特定题目用户提交的功能\r\n2. [ Bug Fixed ] 修改了提交记录界面中的代码显示样式\r\n\r\n#### 2022.4.6\r\n\r\n1. [ Bug Fixed ] 修复了由于 DFS 搜索层数过深导致的栈空间爆炸从而导致的 Runtime Error.\r\n2. [ Bug Fixed ] 更新了提交记录中测试时间，空间，代码信息的显示\r\n\r\n#### 2022.4.1\r\n\r\n1. [ Bug Fixed ] 修复了题目内部数学公式显示有时会出现 `<em>` 或 `</em>` 的错误。\r\n2. [ Bug Fixed ] 修复了题目内部表格/列表内数学公式不解析的错误。\r\n',0,1,NULL,'post','publish',NULL,0,'1','1','1',0,2);
/*!40000 ALTER TABLE `typecho_contents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `typecho_fields`
--

DROP TABLE IF EXISTS `typecho_fields`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `typecho_fields` (
  `cid` int unsigned NOT NULL,
  `name` varchar(150) NOT NULL,
  `type` varchar(8) DEFAULT 'str',
  `str_value` text,
  `int_value` int DEFAULT '0',
  `float_value` float DEFAULT '0',
  PRIMARY KEY (`cid`,`name`),
  KEY `int_value` (`int_value`),
  KEY `float_value` (`float_value`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `typecho_fields`
--

LOCK TABLES `typecho_fields` WRITE;
/*!40000 ALTER TABLE `typecho_fields` DISABLE KEYS */;
INSERT INTO `typecho_fields` VALUES (2,'thumb','str','',0,0),(4,'thumb','str','',0,0);
/*!40000 ALTER TABLE `typecho_fields` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `typecho_metas`
--

DROP TABLE IF EXISTS `typecho_metas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `typecho_metas` (
  `mid` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) DEFAULT NULL,
  `slug` varchar(150) DEFAULT NULL,
  `type` varchar(32) NOT NULL,
  `description` varchar(150) DEFAULT NULL,
  `count` int unsigned DEFAULT '0',
  `order` int unsigned DEFAULT '0',
  `parent` int unsigned DEFAULT '0',
  PRIMARY KEY (`mid`),
  KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `typecho_metas`
--

LOCK TABLES `typecho_metas` WRITE;
/*!40000 ALTER TABLE `typecho_metas` DISABLE KEYS */;
INSERT INTO `typecho_metas` VALUES (2,'更新日志','release','category','LittleYang OnlineJudge 版本更新日志',1,1,0),(3,'开发指南','dev','category','LittleYang OnlineJudge 二次开发指南',0,2,0),(4,'帮助信息','help','category','LittleYang OnlineJudge 帮助信息集合',0,3,0);
/*!40000 ALTER TABLE `typecho_metas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `typecho_options`
--

DROP TABLE IF EXISTS `typecho_options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `typecho_options` (
  `name` varchar(32) NOT NULL,
  `user` int unsigned NOT NULL DEFAULT '0',
  `value` text,
  PRIMARY KEY (`name`,`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `typecho_options`
--

LOCK TABLES `typecho_options` WRITE;
/*!40000 ALTER TABLE `typecho_options` DISABLE KEYS */;
INSERT INTO `typecho_options` VALUES ('actionTable',0,'a:0:{}'),('allowRegister',0,'0'),('allowXmlRpc',0,'2'),('attachmentTypes',0,'@image@'),('autoSave',0,'0'),('charset',0,'UTF-8'),('commentDateFormat',0,'F jS, Y \\a\\t h:i a'),('commentsAntiSpam',0,'1'),('commentsAutoClose',0,'0'),('commentsAvatar',0,'1'),('commentsAvatarRating',0,'G'),('commentsCheckReferer',0,'1'),('commentsHTMLTagAllowed',0,NULL),('commentsListSize',0,'10'),('commentsMarkdown',0,'0'),('commentsMaxNestingLevels',0,'5'),('commentsOrder',0,'ASC'),('commentsPageBreak',0,'0'),('commentsPageDisplay',0,'last'),('commentsPageSize',0,'20'),('commentsPostInterval',0,'60'),('commentsPostIntervalEnable',0,'1'),('commentsPostTimeout',0,'2592000'),('commentsRequireMail',0,'1'),('commentsRequireModeration',0,'0'),('commentsRequireURL',0,'0'),('commentsShowCommentOnly',0,'0'),('commentsShowUrl',0,'1'),('commentsThreaded',0,'1'),('commentsUrlNofollow',0,'1'),('commentsWhitelist',0,'0'),('contentType',0,'text/html'),('defaultAllowComment',0,'1'),('defaultAllowFeed',0,'1'),('defaultAllowPing',0,'1'),('defaultCategory',0,'1'),('description',0,'LittleYang OnlineJudge Development Team'),('editorSize',0,'350'),('feedFullText',0,'0'),('frontArchive',0,'0'),('frontPage',0,'recent'),('generator',0,'Typecho 1.2.0'),('gzip',0,'0'),('installed',0,'1'),('keywords',0,'typecho,php,blog'),('lang',0,NULL),('markdown',0,'1'),('pageSize',0,'5'),('panelTable',0,'a:0:{}'),('plugins',0,'a:0:{}'),('postDateFormat',0,'Y-m-d'),('postsListSize',0,'10'),('rewrite',0,'1'),('routingTable',0,'a:26:{i:0;a:25:{s:5:\"index\";a:6:{s:3:\"url\";s:1:\"/\";s:6:\"widget\";s:15:\"\\Widget\\Archive\";s:6:\"action\";s:6:\"render\";s:4:\"regx\";s:8:\"|^[/]?$|\";s:6:\"format\";s:1:\"/\";s:6:\"params\";a:0:{}}s:7:\"archive\";a:6:{s:3:\"url\";s:6:\"/blog/\";s:6:\"widget\";s:15:\"\\Widget\\Archive\";s:6:\"action\";s:6:\"render\";s:4:\"regx\";s:13:\"|^/blog[/]?$|\";s:6:\"format\";s:6:\"/blog/\";s:6:\"params\";a:0:{}}s:2:\"do\";a:6:{s:3:\"url\";s:22:\"/action/[action:alpha]\";s:6:\"widget\";s:14:\"\\Widget\\Action\";s:6:\"action\";s:6:\"action\";s:4:\"regx\";s:32:\"|^/action/([_0-9a-zA-Z-]+)[/]?$|\";s:6:\"format\";s:10:\"/action/%s\";s:6:\"params\";a:1:{i:0;s:6:\"action\";}}s:4:\"post\";a:6:{s:3:\"url\";s:24:\"/archives/[cid:digital]/\";s:6:\"widget\";s:15:\"\\Widget\\Archive\";s:6:\"action\";s:6:\"render\";s:4:\"regx\";s:26:\"|^/archives/([0-9]+)[/]?$|\";s:6:\"format\";s:13:\"/archives/%s/\";s:6:\"params\";a:1:{i:0;s:3:\"cid\";}}s:10:\"attachment\";a:6:{s:3:\"url\";s:26:\"/attachment/[cid:digital]/\";s:6:\"widget\";s:15:\"\\Widget\\Archive\";s:6:\"action\";s:6:\"render\";s:4:\"regx\";s:28:\"|^/attachment/([0-9]+)[/]?$|\";s:6:\"format\";s:15:\"/attachment/%s/\";s:6:\"params\";a:1:{i:0;s:3:\"cid\";}}s:8:\"category\";a:6:{s:3:\"url\";s:17:\"/category/[slug]/\";s:6:\"widget\";s:15:\"\\Widget\\Archive\";s:6:\"action\";s:6:\"render\";s:4:\"regx\";s:25:\"|^/category/([^/]+)[/]?$|\";s:6:\"format\";s:13:\"/category/%s/\";s:6:\"params\";a:1:{i:0;s:4:\"slug\";}}s:3:\"tag\";a:6:{s:3:\"url\";s:12:\"/tag/[slug]/\";s:6:\"widget\";s:15:\"\\Widget\\Archive\";s:6:\"action\";s:6:\"render\";s:4:\"regx\";s:20:\"|^/tag/([^/]+)[/]?$|\";s:6:\"format\";s:8:\"/tag/%s/\";s:6:\"params\";a:1:{i:0;s:4:\"slug\";}}s:6:\"author\";a:6:{s:3:\"url\";s:22:\"/author/[uid:digital]/\";s:6:\"widget\";s:15:\"\\Widget\\Archive\";s:6:\"action\";s:6:\"render\";s:4:\"regx\";s:24:\"|^/author/([0-9]+)[/]?$|\";s:6:\"format\";s:11:\"/author/%s/\";s:6:\"params\";a:1:{i:0;s:3:\"uid\";}}s:6:\"search\";a:6:{s:3:\"url\";s:19:\"/search/[keywords]/\";s:6:\"widget\";s:15:\"\\Widget\\Archive\";s:6:\"action\";s:6:\"render\";s:4:\"regx\";s:23:\"|^/search/([^/]+)[/]?$|\";s:6:\"format\";s:11:\"/search/%s/\";s:6:\"params\";a:1:{i:0;s:8:\"keywords\";}}s:10:\"index_page\";a:6:{s:3:\"url\";s:21:\"/page/[page:digital]/\";s:6:\"widget\";s:15:\"\\Widget\\Archive\";s:6:\"action\";s:6:\"render\";s:4:\"regx\";s:22:\"|^/page/([0-9]+)[/]?$|\";s:6:\"format\";s:9:\"/page/%s/\";s:6:\"params\";a:1:{i:0;s:4:\"page\";}}s:12:\"archive_page\";a:6:{s:3:\"url\";s:26:\"/blog/page/[page:digital]/\";s:6:\"widget\";s:15:\"\\Widget\\Archive\";s:6:\"action\";s:6:\"render\";s:4:\"regx\";s:27:\"|^/blog/page/([0-9]+)[/]?$|\";s:6:\"format\";s:14:\"/blog/page/%s/\";s:6:\"params\";a:1:{i:0;s:4:\"page\";}}s:13:\"category_page\";a:6:{s:3:\"url\";s:32:\"/category/[slug]/[page:digital]/\";s:6:\"widget\";s:15:\"\\Widget\\Archive\";s:6:\"action\";s:6:\"render\";s:4:\"regx\";s:34:\"|^/category/([^/]+)/([0-9]+)[/]?$|\";s:6:\"format\";s:16:\"/category/%s/%s/\";s:6:\"params\";a:2:{i:0;s:4:\"slug\";i:1;s:4:\"page\";}}s:8:\"tag_page\";a:6:{s:3:\"url\";s:27:\"/tag/[slug]/[page:digital]/\";s:6:\"widget\";s:15:\"\\Widget\\Archive\";s:6:\"action\";s:6:\"render\";s:4:\"regx\";s:29:\"|^/tag/([^/]+)/([0-9]+)[/]?$|\";s:6:\"format\";s:11:\"/tag/%s/%s/\";s:6:\"params\";a:2:{i:0;s:4:\"slug\";i:1;s:4:\"page\";}}s:11:\"author_page\";a:6:{s:3:\"url\";s:37:\"/author/[uid:digital]/[page:digital]/\";s:6:\"widget\";s:15:\"\\Widget\\Archive\";s:6:\"action\";s:6:\"render\";s:4:\"regx\";s:33:\"|^/author/([0-9]+)/([0-9]+)[/]?$|\";s:6:\"format\";s:14:\"/author/%s/%s/\";s:6:\"params\";a:2:{i:0;s:3:\"uid\";i:1;s:4:\"page\";}}s:11:\"search_page\";a:6:{s:3:\"url\";s:34:\"/search/[keywords]/[page:digital]/\";s:6:\"widget\";s:15:\"\\Widget\\Archive\";s:6:\"action\";s:6:\"render\";s:4:\"regx\";s:32:\"|^/search/([^/]+)/([0-9]+)[/]?$|\";s:6:\"format\";s:14:\"/search/%s/%s/\";s:6:\"params\";a:2:{i:0;s:8:\"keywords\";i:1;s:4:\"page\";}}s:12:\"archive_year\";a:6:{s:3:\"url\";s:18:\"/[year:digital:4]/\";s:6:\"widget\";s:15:\"\\Widget\\Archive\";s:6:\"action\";s:6:\"render\";s:4:\"regx\";s:19:\"|^/([0-9]{4})[/]?$|\";s:6:\"format\";s:4:\"/%s/\";s:6:\"params\";a:1:{i:0;s:4:\"year\";}}s:13:\"archive_month\";a:6:{s:3:\"url\";s:36:\"/[year:digital:4]/[month:digital:2]/\";s:6:\"widget\";s:15:\"\\Widget\\Archive\";s:6:\"action\";s:6:\"render\";s:4:\"regx\";s:30:\"|^/([0-9]{4})/([0-9]{2})[/]?$|\";s:6:\"format\";s:7:\"/%s/%s/\";s:6:\"params\";a:2:{i:0;s:4:\"year\";i:1;s:5:\"month\";}}s:11:\"archive_day\";a:6:{s:3:\"url\";s:52:\"/[year:digital:4]/[month:digital:2]/[day:digital:2]/\";s:6:\"widget\";s:15:\"\\Widget\\Archive\";s:6:\"action\";s:6:\"render\";s:4:\"regx\";s:41:\"|^/([0-9]{4})/([0-9]{2})/([0-9]{2})[/]?$|\";s:6:\"format\";s:10:\"/%s/%s/%s/\";s:6:\"params\";a:3:{i:0;s:4:\"year\";i:1;s:5:\"month\";i:2;s:3:\"day\";}}s:17:\"archive_year_page\";a:6:{s:3:\"url\";s:38:\"/[year:digital:4]/page/[page:digital]/\";s:6:\"widget\";s:15:\"\\Widget\\Archive\";s:6:\"action\";s:6:\"render\";s:4:\"regx\";s:33:\"|^/([0-9]{4})/page/([0-9]+)[/]?$|\";s:6:\"format\";s:12:\"/%s/page/%s/\";s:6:\"params\";a:2:{i:0;s:4:\"year\";i:1;s:4:\"page\";}}s:18:\"archive_month_page\";a:6:{s:3:\"url\";s:56:\"/[year:digital:4]/[month:digital:2]/page/[page:digital]/\";s:6:\"widget\";s:15:\"\\Widget\\Archive\";s:6:\"action\";s:6:\"render\";s:4:\"regx\";s:44:\"|^/([0-9]{4})/([0-9]{2})/page/([0-9]+)[/]?$|\";s:6:\"format\";s:15:\"/%s/%s/page/%s/\";s:6:\"params\";a:3:{i:0;s:4:\"year\";i:1;s:5:\"month\";i:2;s:4:\"page\";}}s:16:\"archive_day_page\";a:6:{s:3:\"url\";s:72:\"/[year:digital:4]/[month:digital:2]/[day:digital:2]/page/[page:digital]/\";s:6:\"widget\";s:15:\"\\Widget\\Archive\";s:6:\"action\";s:6:\"render\";s:4:\"regx\";s:55:\"|^/([0-9]{4})/([0-9]{2})/([0-9]{2})/page/([0-9]+)[/]?$|\";s:6:\"format\";s:18:\"/%s/%s/%s/page/%s/\";s:6:\"params\";a:4:{i:0;s:4:\"year\";i:1;s:5:\"month\";i:2;s:3:\"day\";i:3;s:4:\"page\";}}s:12:\"comment_page\";a:6:{s:3:\"url\";s:53:\"[permalink:string]/comment-page-[commentPage:digital]\";s:6:\"widget\";s:15:\"\\Widget\\Archive\";s:6:\"action\";s:6:\"render\";s:4:\"regx\";s:36:\"|^(.+)/comment\\-page\\-([0-9]+)[/]?$|\";s:6:\"format\";s:18:\"%s/comment-page-%s\";s:6:\"params\";a:2:{i:0;s:9:\"permalink\";i:1;s:11:\"commentPage\";}}s:4:\"feed\";a:6:{s:3:\"url\";s:20:\"/feed[feed:string:0]\";s:6:\"widget\";s:15:\"\\Widget\\Archive\";s:6:\"action\";s:4:\"feed\";s:4:\"regx\";s:17:\"|^/feed(.*)[/]?$|\";s:6:\"format\";s:7:\"/feed%s\";s:6:\"params\";a:1:{i:0;s:4:\"feed\";}}s:8:\"feedback\";a:6:{s:3:\"url\";s:31:\"[permalink:string]/[type:alpha]\";s:6:\"widget\";s:16:\"\\Widget\\Feedback\";s:6:\"action\";s:6:\"action\";s:4:\"regx\";s:29:\"|^(.+)/([_0-9a-zA-Z-]+)[/]?$|\";s:6:\"format\";s:5:\"%s/%s\";s:6:\"params\";a:2:{i:0;s:9:\"permalink\";i:1;s:4:\"type\";}}s:4:\"page\";a:6:{s:3:\"url\";s:12:\"/[slug].html\";s:6:\"widget\";s:15:\"\\Widget\\Archive\";s:6:\"action\";s:6:\"render\";s:4:\"regx\";s:22:\"|^/([^/]+)\\.html[/]?$|\";s:6:\"format\";s:8:\"/%s.html\";s:6:\"params\";a:1:{i:0;s:4:\"slug\";}}}s:5:\"index\";a:3:{s:3:\"url\";s:1:\"/\";s:6:\"widget\";s:15:\"\\Widget\\Archive\";s:6:\"action\";s:6:\"render\";}s:7:\"archive\";a:3:{s:3:\"url\";s:6:\"/blog/\";s:6:\"widget\";s:15:\"\\Widget\\Archive\";s:6:\"action\";s:6:\"render\";}s:2:\"do\";a:3:{s:3:\"url\";s:22:\"/action/[action:alpha]\";s:6:\"widget\";s:14:\"\\Widget\\Action\";s:6:\"action\";s:6:\"action\";}s:4:\"post\";a:3:{s:3:\"url\";s:24:\"/archives/[cid:digital]/\";s:6:\"widget\";s:15:\"\\Widget\\Archive\";s:6:\"action\";s:6:\"render\";}s:10:\"attachment\";a:3:{s:3:\"url\";s:26:\"/attachment/[cid:digital]/\";s:6:\"widget\";s:15:\"\\Widget\\Archive\";s:6:\"action\";s:6:\"render\";}s:8:\"category\";a:3:{s:3:\"url\";s:17:\"/category/[slug]/\";s:6:\"widget\";s:15:\"\\Widget\\Archive\";s:6:\"action\";s:6:\"render\";}s:3:\"tag\";a:3:{s:3:\"url\";s:12:\"/tag/[slug]/\";s:6:\"widget\";s:15:\"\\Widget\\Archive\";s:6:\"action\";s:6:\"render\";}s:6:\"author\";a:3:{s:3:\"url\";s:22:\"/author/[uid:digital]/\";s:6:\"widget\";s:15:\"\\Widget\\Archive\";s:6:\"action\";s:6:\"render\";}s:6:\"search\";a:3:{s:3:\"url\";s:19:\"/search/[keywords]/\";s:6:\"widget\";s:15:\"\\Widget\\Archive\";s:6:\"action\";s:6:\"render\";}s:10:\"index_page\";a:3:{s:3:\"url\";s:21:\"/page/[page:digital]/\";s:6:\"widget\";s:15:\"\\Widget\\Archive\";s:6:\"action\";s:6:\"render\";}s:12:\"archive_page\";a:3:{s:3:\"url\";s:26:\"/blog/page/[page:digital]/\";s:6:\"widget\";s:15:\"\\Widget\\Archive\";s:6:\"action\";s:6:\"render\";}s:13:\"category_page\";a:3:{s:3:\"url\";s:32:\"/category/[slug]/[page:digital]/\";s:6:\"widget\";s:15:\"\\Widget\\Archive\";s:6:\"action\";s:6:\"render\";}s:8:\"tag_page\";a:3:{s:3:\"url\";s:27:\"/tag/[slug]/[page:digital]/\";s:6:\"widget\";s:15:\"\\Widget\\Archive\";s:6:\"action\";s:6:\"render\";}s:11:\"author_page\";a:3:{s:3:\"url\";s:37:\"/author/[uid:digital]/[page:digital]/\";s:6:\"widget\";s:15:\"\\Widget\\Archive\";s:6:\"action\";s:6:\"render\";}s:11:\"search_page\";a:3:{s:3:\"url\";s:34:\"/search/[keywords]/[page:digital]/\";s:6:\"widget\";s:15:\"\\Widget\\Archive\";s:6:\"action\";s:6:\"render\";}s:12:\"archive_year\";a:3:{s:3:\"url\";s:18:\"/[year:digital:4]/\";s:6:\"widget\";s:15:\"\\Widget\\Archive\";s:6:\"action\";s:6:\"render\";}s:13:\"archive_month\";a:3:{s:3:\"url\";s:36:\"/[year:digital:4]/[month:digital:2]/\";s:6:\"widget\";s:15:\"\\Widget\\Archive\";s:6:\"action\";s:6:\"render\";}s:11:\"archive_day\";a:3:{s:3:\"url\";s:52:\"/[year:digital:4]/[month:digital:2]/[day:digital:2]/\";s:6:\"widget\";s:15:\"\\Widget\\Archive\";s:6:\"action\";s:6:\"render\";}s:17:\"archive_year_page\";a:3:{s:3:\"url\";s:38:\"/[year:digital:4]/page/[page:digital]/\";s:6:\"widget\";s:15:\"\\Widget\\Archive\";s:6:\"action\";s:6:\"render\";}s:18:\"archive_month_page\";a:3:{s:3:\"url\";s:56:\"/[year:digital:4]/[month:digital:2]/page/[page:digital]/\";s:6:\"widget\";s:15:\"\\Widget\\Archive\";s:6:\"action\";s:6:\"render\";}s:16:\"archive_day_page\";a:3:{s:3:\"url\";s:72:\"/[year:digital:4]/[month:digital:2]/[day:digital:2]/page/[page:digital]/\";s:6:\"widget\";s:15:\"\\Widget\\Archive\";s:6:\"action\";s:6:\"render\";}s:12:\"comment_page\";a:3:{s:3:\"url\";s:53:\"[permalink:string]/comment-page-[commentPage:digital]\";s:6:\"widget\";s:15:\"\\Widget\\Archive\";s:6:\"action\";s:6:\"render\";}s:4:\"feed\";a:3:{s:3:\"url\";s:20:\"/feed[feed:string:0]\";s:6:\"widget\";s:15:\"\\Widget\\Archive\";s:6:\"action\";s:4:\"feed\";}s:8:\"feedback\";a:3:{s:3:\"url\";s:31:\"[permalink:string]/[type:alpha]\";s:6:\"widget\";s:16:\"\\Widget\\Feedback\";s:6:\"action\";s:6:\"action\";}s:4:\"page\";a:3:{s:3:\"url\";s:12:\"/[slug].html\";s:6:\"widget\";s:15:\"\\Widget\\Archive\";s:6:\"action\";s:6:\"render\";}}'),('secret',0,'83s25ILi0LXZzr4RUITYnGqolHd(5SO4'),('siteUrl',0,'https://blog.lyoj.ml'),('theme',0,'wanna'),('theme:wanna',0,'a:8:{s:5:\"bgUrl\";s:0:\"\";s:5:\"hIcon\";s:102:\"https://avatars.githubusercontent.com/u/107755161?s=400&u=4ab0a4518b4ca85e11695ca2d2b150bac8f31b1b&v=4\";s:4:\"logo\";s:102:\"https://avatars.githubusercontent.com/u/107755161?s=400&u=4ab0a4518b4ca85e11695ca2d2b150bac8f31b1b&v=4\";s:4:\"icon\";s:102:\"https://avatars.githubusercontent.com/u/107755161?s=400&u=4ab0a4518b4ca85e11695ca2d2b150bac8f31b1b&v=4\";s:10:\"start_time\";s:19:\"2022-08-08 00:00:00\";s:9:\"copyright\";s:0:\"\";s:6:\"comNum\";s:1:\"6\";s:5:\"slimg\";s:6:\"showon\";}'),('timezone',0,'28800'),('title',0,'lyoj-dev'),('xmlrpcMarkdown',0,'0');
/*!40000 ALTER TABLE `typecho_options` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `typecho_relationships`
--

DROP TABLE IF EXISTS `typecho_relationships`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `typecho_relationships` (
  `cid` int unsigned NOT NULL,
  `mid` int unsigned NOT NULL,
  PRIMARY KEY (`cid`,`mid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `typecho_relationships`
--

LOCK TABLES `typecho_relationships` WRITE;
/*!40000 ALTER TABLE `typecho_relationships` DISABLE KEYS */;
INSERT INTO `typecho_relationships` VALUES (4,2);
/*!40000 ALTER TABLE `typecho_relationships` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `typecho_users`
--

DROP TABLE IF EXISTS `typecho_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `typecho_users` (
  `uid` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) DEFAULT NULL,
  `password` varchar(64) DEFAULT NULL,
  `mail` varchar(150) DEFAULT NULL,
  `url` varchar(150) DEFAULT NULL,
  `screenName` varchar(32) DEFAULT NULL,
  `created` int unsigned DEFAULT '0',
  `activated` int unsigned DEFAULT '0',
  `logged` int unsigned DEFAULT '0',
  `group` varchar(16) DEFAULT 'visitor',
  `authCode` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`uid`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `mail` (`mail`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `typecho_users`
--

LOCK TABLES `typecho_users` WRITE;
/*!40000 ALTER TABLE `typecho_users` DISABLE KEYS */;
INSERT INTO `typecho_users` VALUES (1,'LittleYang0531','$P$BlYsiQ7P9Mty3hzDO2QonI3B6XNL74/','littleyang0531@foxmail.com','https://blog.littleyang.ml','admin',1659930300,1659963116,1659962637,'administrator','26346ff476e23108c1d5648d027e1824');
/*!40000 ALTER TABLE `typecho_users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2022-08-08 13:02:20
