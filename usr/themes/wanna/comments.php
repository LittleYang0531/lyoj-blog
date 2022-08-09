<?php

use IPQuery as GlobalIPQuery;

 if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php
if(is_array($_GET)&&count($_GET)>0)
{
    if(isset($_GET["replyTo"]))
    {
        $ac = $_GET["replyTo"];
        }
} ?>

<?php
class IPQuery
{
    private $fh;        // IP数据库文件句柄
    private $first;     // 第一条索引
    private $last;      // 最后一条索引
    private $total;     // 索引总数
    private $dbFile = __DIR__ . DIRECTORY_SEPARATOR . 'qqwry.dat';      // 纯真 IP 数据库文件存放路径
    private $dbExpires = 0;

    // 构造函数
    function __construct()
    {
        // IP 数据库文件不存在或已过期，则自动获取
        if (!file_exists($this->dbFile) || ($this->dbExpires && ((time() - filemtime($this->dbFile)) > $this->dbExpires))) {
            $this->update();
        }
    }

    // 忽略超时
    private function ignore_timeout()
    {
        @ignore_user_abort(true);
        @ini_set('max_execution_time', 48 * 60 * 60);
        @set_time_limit(48 * 60 * 60);    // set_time_limit(0)  2day
        @ini_set('memory_limit', '4000M'); // 4G;
    }

    // 读取little-endian编码的4个字节转化为长整型数
    private function getLong4()
    {
        $result = unpack('Vlong', fread($this->fh, 4));
        return $result['long'];
    }

    // 读取little-endian编码的3个字节转化为长整型数
    private function getLong3()
    {
        $result = unpack('Vlong', fread($this->fh, 3) . chr(0));
        return $result['long'];
    }

    // 查询位置信息
    private function getPos($data = '')
    {
        $char = fread($this->fh, 1);
        while (ord($char) != 0) {   // 地区信息以 0 结束
            $data .= $char;
            $char = fread($this->fh, 1);
        }
        return $data;
    }

    // 查询运营商
    private function getISP()
    {
        $byte = fread($this->fh, 1);    // 标志字节
        switch (ord($byte)) {
            case 0:
                $area = '';
                break;  // 没有相关信息
            case 1: // 被重定向
                fseek($this->fh, $this->getLong3());
                $area = $this->getPos();
                break;
            case 2: // 被重定向
                fseek($this->fh, $this->getLong3());
                $area = $this->getPos();
                break;
            default:
                $area = $this->getPos($byte);
                break;     // 没有被重定向
        }
        return $area;
    }

    // 检查 IP 格式是否正确
    public function checkIp($ip)
    {
        $arr = explode('.', $ip);
        if (count($arr) != 4) return false;
        for ($i = 0; $i < 4; $i++) {
            if ($arr[$i] < '0' || $arr[$i] > '255') {
                return false;
            }
        }
        return true;
    }

    // 查询 IP 地址
    public function query($ip)
    {
        // 判断域名
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            $ip = gethostbyname($ip);
        }
        if (!$this->checkIp($ip)) {
            return false;
        }

        $this->fh    = fopen($this->dbFile, 'rb');
        $this->first = $this->getLong4();
        $this->last  = $this->getLong4();
        $this->total = ($this->last - $this->first) / 7;    // 每条索引7字节

        $ip = pack('N', intval(ip2long($ip)));

        // 二分查找 IP 位置
        $l = 0;
        $r = $this->total;
        while ($l <= $r) {
            $m = floor(($l + $r) / 2);     // 计算中间索引
            fseek($this->fh, $this->first + $m * 7);
            $beginip = strrev(fread($this->fh, 4)); // 中间索引的开始IP地址
            fseek($this->fh, $this->getLong3());
            $endip = strrev(fread($this->fh, 4));   // 中间索引的结束IP地址

            if ($ip < $beginip) {   // 用户的IP小于中间索引的开始IP地址时
                $r = $m - 1;
            } else {
                if ($ip > $endip) { // 用户的IP大于中间索引的结束IP地址时
                    $l = $m + 1;
                } else {            // 用户IP在中间索引的IP范围内时
                    $findip = $this->first + $m * 7;
                    break;
                }
            }
        }

        // 查找 IP 地址段
        fseek($this->fh, $findip);
        $location['beginip'] = long2ip($this->getLong4()); // 用户IP所在范围的开始地址
        $offset = $this->getlong3();
        fseek($this->fh, $offset);
        $location['endip'] = long2ip($this->getLong4()); // 用户IP所在范围的结束地址

        // 查找 IP 信息
        $byte = fread($this->fh, 1); // 标志字节
        switch (ord($byte)) {
            case 1:  // 都被重定向
                $countryOffset = $this->getLong3(); // 重定向地址
                fseek($this->fh, $countryOffset);
                $byte = fread($this->fh, 1); // 标志字节
                switch (ord($byte)) {
                    case 2: // 信息被二次重定向
                        fseek($this->fh, $this->getLong3());
                        $location['pos'] = $this->getPos();
                        fseek($this->fh, $countryOffset + 4);
                        $location['isp'] = $this->getISP();
                        break;
                    default: // 信息没有被二次重定向
                        $location['pos'] = $this->getPos($byte);
                        $location['isp'] = $this->getISP();
                        break;
                }
                break;

            case 2: // 信息被重定向
                fseek($this->fh, $this->getLong3());
                $location['pos'] = $this->getPos();
                fseek($this->fh, $offset + 8);
                $location['isp'] = $this->getISP();
                break;

            default: // 信息没有被重定向
                $location['pos'] = $this->getPos($byte);
                $location['isp'] = $this->getISP();
                break;
        }

        // 信息转码处理
        foreach ($location as $k => $v) {
            $location[$k] = iconv('gb2312', 'utf-8', $v);
            $location[$k] = preg_replace(array('/^.*CZ88\.NET.*$/isU', '/^.*纯真.*$/isU', '/^.*日IP数据/'), '', $location[$k]);
            $location[$k] = htmlspecialchars($location[$k]);
        }

        return $location;
    }

    // 更新数据库 https://www.22vd.com/40035.html
    public function update()
    {
        $this->ignore_timeout();
        $copywrite = file_get_contents('http://update.cz88.net/ip/copywrite.rar');
        $qqwry     = file_get_contents('http://update.cz88.net/ip/qqwry.rar');
        $key       = unpack('V6', $copywrite)[6];
        for ($i = 0; $i < 0x200; $i++) {
            $key *= 0x805;
            $key++;
            $key = $key & 0xFF;
            $qqwry[$i] = chr(ord($qqwry[$i]) ^ $key);
        }
        $qqwry = gzuncompress($qqwry);
        file_put_contents($this->dbFile, $qqwry);
    }

    // 析构函数
    function __destruct()
    {
        if ($this->fh) {
            fclose($this->fh);
        }
        $this->fp = null;
    }
}
?>

<script>
    $(document).ready(function () {
        replyId = $('#comment-'+"<?php echo $ac; ?>");
        replyName =  replyId.find('.name:first > a').text();
        replyCon =  replyId.find('.userBB-Content:first > p').text();
        if (replyId !== ''){
            $('.replyId').fadeIn();
            $('.reply-name').text(replyName);
            $('.replyCon').text('" '+replyCon+' "');
        }else {
            $('.replyId').css({
                display : 'none'
            })
        }
    });
</script>
<div class="sibi borR5px shadow-2 mdui-typo">
    <?php $this->comments()->to($comments); ?>
    <?php if($this->allow('comment')): ?>
    <div class="pageHead" id="<?php $this->respondId(); ?>">
        <h4><?php $this->commentsNum(_t('暂无评论'), _t('仅有一条评论'), _t('已有 %d 条评论')); ?></h4>
        <div class="mdui-divider" style="margin: 15px 0"></div>
    </div>
    <div class="newBB mdui-row">
        <div class="replyId smallSize" id="replyId">正在回复给 <span class="reply-name"></span>&nbsp<span class="replyCon"></span></div>
        <div class="mdui-row">
            <form method="post" action="<?php $this->commentUrl() ?>" style="width: 100%" role="form" id="comment_form">
                <?php if($this->user->hasLogin()): ?>
                    <p><?php _e('登录身份: '); ?><a href="<?php $this->options->profileUrl(); ?>"><?php $this->user->screenName(); ?></a>. <a href="<?php $this->options->logoutUrl(); ?>" title="Logout"><?php _e('退出'); ?> &raquo; </a></p>
                <?php else: ?>
                    <a class="smallSize" href="<?php $this->options->adminUrl(); ?>">去登录?</a>
                    <div class="userIC">
                        <div class="mdui-col-xs-12 mdui-col-md-3 getData-input" id="userName">
                            <input type="text" placeholder="昵称" name="author" value="<?php $this->remember('author'); ?>" required />
                        </div>
                        <div class="mdui-col-xs-12 mdui-col-md-4 getData-input" id="mail">
                            <input type="email" placeholder="邮箱" name="mail" value="<?php $this->remember('mail'); ?>"<?php if ($this->options->commentsRequireMail): ?> required<?php endif; ?> />
                        </div>
                        <div class="mdui-col-xs-12 mdui-col-md-4 getData-input" id="urls">
                            <input type="text" name="url" id="urls" placeholder="头像地址, 以 http(s):// 开头" value="<?php $this->remember('url'); ?>"<?php if ($this->options->commentsRequireURL): ?> required<?php endif; ?>/>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="mdui-col-xs-12 mdui-col-md-12 getData-input" id="content">
                    <textarea name="text" id="textarea" class="textarea" placeholder="评论内容" required ><?php $this->remember('text'); ?></textarea>
                </div>
                <div class="mdui-col-xs-12 mdui-col-md-2" id="subBtn">
                    <button class="mdui-ripple" type="submit">提交评论</button>
                </div>
            </form>
        </div>
        <?php else: ?>
            <h3><?php _e('评论已关闭'); ?></h3>
        <?php endif; ?> <!-- 判断是否允许评论 -->
    </div>
    <div class="mdui-row comFiled">
        <div class="mdui-col-md-8" style="width:100%">
            <?php $comments->listComments(); ?>
        </div>
        <!-- <div class="mdui-col-md-4 comTool">
            <h4>标签云</h4>
            <div class="tags">
                <?php $this->widget('Widget_Metas_Tag_Cloud', 'sort=mid&ignoreZeroCount=1&desc=0&limit=30')->to($tags); ?>
                <?php if($tags->have()): ?>
                <ul class="tags-list">
                    <?php while ($tags->next()): ?>
                        <li class="mdui-ripple"><a href="<?php $tags->permalink(); ?>" rel="tag" class="size-<?php $tags->split(5, 10, 20, 30); ?>" title="<?php $tags->count(); ?> 个话题"><?php $tags->name(); ?></a></li>
                    <?php endwhile; ?>
                    <?php else: ?>
                        <li><?php _e('没有任何标签'); ?></li>
                    <?php endif; ?>
                </ul>
            </div>
            <h4>文章留名</h4>
            <div class="visitor">
                <?php while($comments->next()): ?>
                <div style="display: inline-block">
                    <?php $comments->gravatar('45', ''); ?>
                    <div class="visitorName">
                        <?php $comments->author(); ?>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
            <script>
                $('.visitor > div').mouseover(function () {
                    $(this).attr('title',$(this).find('.visitorName').text());
                })
            </script>
        </div> -->
    </div>
    <?php 
    global $db; $db = \Typecho\Db::get();
    function threadedComments($comments, $options) {
        $commentClass = ''; global $db;
        if ($comments->authorId) {
            if ($comments->authorId == $comments->ownerId) {
                $commentClass .= ' comment-by-author';
            } else {
                $commentClass .= ' comment-by-user';
            }
        }
        $commentLevelClass = $comments->_levels > 0 ? ' comment-child' : ' comment-parent';
        ?>

        <div class="userBB" id="<?php $comments->theId(); ?>">
            <div class="replyTools">
                    <button class="mdui-btn mdui-btn-icon mdui-ripple replyBtn">
                        <i class="mdui-icon material-icons">reply</i>
                        <?php $comments->reply(''); ?>
                    </button>
            </div>
            <div class="userData">
                <div class="userIcon">
                    <?php
                        $id = $comments -> coid;
                        $res = $db -> Query("SELECT url FROM typecho_comments WHERE coid = $id");
                        $res = $db -> fetchAll($res);
                        echo "<img width='60px' height='60px' src='".$res[0]["url"]."'/>";
                    ?>
                </div>
                <div class="userName">
                    <div class="name" style="display:flex; display:--webkit-flex;">
                        <?php echo "<a style='cursor:pointer'>" . $comments->author . "</a>"; ?>
                    </div>
                    <div class="Jurisdiction"><?php 
                        $where = "Unknown"; $ipq = new IPQuery;
                        if ($comments->ip != "unknown") $where = $ipq -> query($comments->ip)["pos"];
                        echo "IP 属地: " . $where; 
                    ?></div>
                    <div class="Jurisdiction"><?php $comments->date('Y-m-d H:i:s'); ?></div>
                </div>
            </div>
            <div class="userBB-Content">
                <?php $comments->content(); ?>
            </div>
            <?php if ($comments->children) { ?>
                <div class="comment-children">
                    <?php $comments->threadedComments($options); ?>
                </div>
            <?php } ?>
        </div>
        <div style="clear: both"></div>

    <?php } ?>
</div>
