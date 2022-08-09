<?php
include 'common.php';
include 'header.php';
include 'menu.php';

$stat = \Widget\Stat::alloc();
$comments = \Widget\Comments\Admin::alloc();
$isAllComments = ('on' == $request->get('__typecho_all_comments') || 'on' == \Typecho\Cookie::get('__typecho_all_comments'));
?>

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

<div class="main">
    <div class="body container">
        <?php include 'page-title.php'; ?>
        <div class="row typecho-page-main" role="main">
            <div class="col-mb-12 typecho-list">
                <div class="clearfix">
                    <ul class="typecho-option-tabs right">
                    <?php if($user->pass('editor', true) && !isset($request->cid)): ?>
                        <li class="<?php if($isAllComments): ?> current<?php endif; ?>"><a href="<?php echo $request->makeUriByRequest('__typecho_all_comments=on'); ?>"><?php _e('所有'); ?></a></li>
                        <li class="<?php if(!$isAllComments): ?> current<?php endif; ?>"><a href="<?php echo $request->makeUriByRequest('__typecho_all_comments=off'); ?>"><?php _e('我的'); ?></a></li>
                    <?php endif; ?>
                    </ul>
                    <ul class="typecho-option-tabs">
                        <li<?php if(!isset($request->status) || 'approved' == $request->get('status')): ?> class="current"<?php endif; ?>><a href="<?php $options->adminUrl('manage-comments.php'
                        . (isset($request->cid) ? '?cid=' . $request->cid : '')); ?>"><?php _e('已通过'); ?></a></li>
                        <li<?php if('waiting' == $request->get('status')): ?> class="current"<?php endif; ?>><a href="<?php $options->adminUrl('manage-comments.php?status=waiting'
                        . (isset($request->cid) ? '&cid=' . $request->cid : '')); ?>"><?php _e('待审核'); ?>
                        <?php if(!$isAllComments && $stat->myWaitingCommentsNum > 0 && !isset($request->cid)): ?> 
                            <span class="balloon"><?php $stat->myWaitingCommentsNum(); ?></span>
                        <?php elseif($isAllComments && $stat->waitingCommentsNum > 0 && !isset($request->cid)): ?>
                            <span class="balloon"><?php $stat->waitingCommentsNum(); ?></span>
                        <?php elseif(isset($request->cid) && $stat->currentWaitingCommentsNum > 0): ?>
                            <span class="balloon"><?php $stat->currentWaitingCommentsNum(); ?></span>
                        <?php endif; ?>
                        </a></li>
                        <li<?php if('spam' == $request->get('status')): ?> class="current"<?php endif; ?>><a href="<?php $options->adminUrl('manage-comments.php?status=spam'
                        . (isset($request->cid) ? '&cid=' . $request->cid : '')); ?>"><?php _e('垃圾'); ?>
                        <?php if(!$isAllComments && $stat->mySpamCommentsNum > 0 && !isset($request->cid)): ?> 
                            <span class="balloon"><?php $stat->mySpamCommentsNum(); ?></span>
                        <?php elseif($isAllComments && $stat->spamCommentsNum > 0 && !isset($request->cid)): ?>
                            <span class="balloon"><?php $stat->spamCommentsNum(); ?></span>
                        <?php elseif(isset($request->cid) && $stat->currentSpamCommentsNum > 0): ?>
                            <span class="balloon"><?php $stat->currentSpamCommentsNum(); ?></span>
                        <?php endif; ?>
                        </a></li>
                    </ul>
                </div>
            
                <div class="typecho-list-operate clearfix">
                    <form method="get">
                        <div class="operate">
                            <label><i class="sr-only"><?php _e('全选'); ?></i><input type="checkbox" class="typecho-table-select-all" /></label>
                            <div class="btn-group btn-drop">
                            <button class="btn dropdown-toggle btn-s" type="button"><i class="sr-only"><?php _e('操作'); ?></i><?php _e('选中项'); ?> <i class="i-caret-down"></i></button>
                            <ul class="dropdown-menu">
                                <li><a href="<?php $security->index('/action/comments-edit?do=approved'); ?>"><?php _e('通过'); ?></a></li>
                                <li><a href="<?php $security->index('/action/comments-edit?do=waiting'); ?>"><?php _e('待审核'); ?></a></li>
                                <li><a href="<?php $security->index('/action/comments-edit?do=spam'); ?>"><?php _e('标记垃圾'); ?></a></li>
                                <li><a lang="<?php _e('你确认要删除这些评论吗?'); ?>" href="<?php $security->index('/action/comments-edit?do=delete'); ?>"><?php _e('删除'); ?></a></li>
                            </ul>
                            <?php if('spam' == $request->get('status')): ?>
                                <button lang="<?php _e('你确认要删除所有垃圾评论吗?'); ?>" class="btn btn-s btn-warn btn-operate" href="<?php $security->index('/action/comments-edit?do=delete-spam'); ?>"><?php _e('删除所有垃圾评论'); ?></button>
                            <?php endif; ?>
                            </div>
                        </div>
                        <div class="search" role="search">
                            <?php if ('' != $request->keywords || '' != $request->category): ?>
                            <a href="<?php $options->adminUrl('manage-comments.php' 
                            . (isset($request->status) || isset($request->cid) ? '?' .
                            (isset($request->status) ? 'status=' . htmlspecialchars($request->get('status')) : '') .
                            (isset($request->cid) ? (isset($request->status) ? '&' : '') . 'cid=' . htmlspecialchars($request->get('cid')) : '') : '')); ?>"><?php _e('&laquo; 取消筛选'); ?></a>
                            <?php endif; ?>
                            <input type="text" class="text-s" placeholder="<?php _e('请输入关键字'); ?>" value="<?php echo htmlspecialchars($request->keywords ?? ''); ?>"<?php if ('' == $request->keywords): ?> onclick="value='';name='keywords';" <?php else: ?> name="keywords"<?php endif; ?>/>
                            <?php if(isset($request->status)): ?>
                                <input type="hidden" value="<?php echo htmlspecialchars($request->get('status')); ?>" name="status" />
                            <?php endif; ?>
                            <?php if(isset($request->cid)): ?>
                                <input type="hidden" value="<?php echo htmlspecialchars($request->get('cid')); ?>" name="cid" />
                            <?php endif; ?>
                            <button type="submit" class="btn btn-s"><?php _e('筛选'); ?></button>
                        </div>
                    </form>
                </div><!-- end .typecho-list-operate -->
                
                <form method="post" name="manage_comments" class="operate-form">
                <div class="typecho-table-wrap">
                    <table class="typecho-list-table">
                        <colgroup>
                            <col width="3%" class="kit-hidden-mb"/>
                            <col width="6%" class="kit-hidden-mb" />
                            <col width="20%"/>
                            <col width="71%"/>
                        </colgroup>
                        <thead>
                            <tr>
                                <th class="kit-hidden-mb"> </th>
                                <th><?php _e('作者'); ?></th>
                                <th class="kit-hidden-mb"> </th>
                                <th><?php _e('内容'); ?></th>
                            </tr>
                        </thead>
                        <tbody>

                        <?php if($comments->have()): ?>
                        <?php while($comments->next()): ?>
                        <tr id="<?php $comments->theId(); ?>" data-comment="<?php
                        $comment = array(
                            'author'    =>  $comments->author,
                            'mail'      =>  $comments->mail,
                            'url'       =>  $comments->url,
                            'ip'        =>  $comments->ip,
                            'type'        =>  $comments->type,
                            'text'      =>  $comments->text
                        );

                        echo htmlspecialchars(json_encode($comment));
                        ?>">
                            <td valign="top" class="kit-hidden-mb">
                                <input type="checkbox" value="<?php $comments->coid(); ?>" name="coid[]"/>
                            </td>
                            <td valign="top" class="kit-hidden-mb">
                                <div class="comment-avatar">
                                    <?php
                                        global $db;
                                        $id = $comments -> coid;
                                        $res = $db -> Query("SELECT url FROM typecho_comments WHERE coid = $id");
                                        $res = $db -> fetchAll($res);
                                        echo "<img width='60px' height='60px' src='".$res[0]["url"]."'/>";
                                    ?>
                                </div>
                            </td>
                            <td valign="top" class="comment-head">
                                <div class="comment-meta">
                                    <strong class="comment-author"><?php $comments->author(false); ?></strong>
                                    <?php if($comments->mail): ?>
                                    <br /><span><a href="mailto:<?php $comments->mail(); ?>"><?php $comments->mail(); ?></a></span>
                                    <?php endif; ?>
                                    <?php if($comments->ip): ?>
                                    <br /><span><?php $comments->ip(); ?></span>
                                    <?php endif; ?>
                                    <?php if($comments->ip): ?>
                                    <br /><span><?php 
                                        $ipq = new IPQuery();
                                        $wh = "Unknown";
                                        if ($comments->ip != "unknown") $wh = $ipq->Query($comments->ip)["pos"];
                                        echo "IP 属地: $wh";
                                    ?></span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td valign="top" class="comment-body">
                                <div class="comment-date"><?php $comments->dateWord(); ?> 于 <a href="<?php $comments->permalink(); ?>"><?php $comments->title(); ?></a></div>
                                <div class="comment-content">
                                    <?php $comments->content(); ?>
                                </div> 
                                <div class="comment-action hidden-by-mouse">
                                    <?php if('approved' == $comments->status): ?>
                                    <span class="weak"><?php _e('通过'); ?></span>
                                    <?php else: ?>
                                    <a href="<?php $security->index('/action/comments-edit?do=approved&coid=' . $comments->coid); ?>" class="operate-approved"><?php _e('通过'); ?></a>
                                    <?php endif; ?>
                                    
                                    <?php if('waiting' == $comments->status): ?>
                                    <span class="weak"><?php _e('待审核'); ?></span>
                                    <?php else: ?>
                                    <a href="<?php $security->index('/action/comments-edit?do=waiting&coid=' . $comments->coid); ?>" class="operate-waiting"><?php _e('待审核'); ?></a>
                                    <?php endif; ?>
                                    
                                    <?php if('spam' == $comments->status): ?>
                                    <span class="weak"><?php _e('垃圾'); ?></span>
                                    <?php else: ?>
                                    <a href="<?php $security->index('/action/comments-edit?do=spam&coid=' . $comments->coid); ?>" class="operate-spam"><?php _e('垃圾'); ?></a>
                                    <?php endif; ?>
                                    
                                    <a href="#<?php $comments->theId(); ?>" rel="<?php $security->index('/action/comments-edit?do=edit&coid=' . $comments->coid); ?>" class="operate-edit"><?php _e('编辑'); ?></a>

                                    <?php if('approved' == $comments->status && 'comment' == $comments->type): ?>
                                    <a href="#<?php $comments->theId(); ?>" rel="<?php $security->index('/action/comments-edit?do=reply&coid=' . $comments->coid); ?>" class="operate-reply"><?php _e('回复'); ?></a>
                                    <?php endif; ?>
                                    
                                    <a lang="<?php _e('你确认要删除%s的评论吗?', htmlspecialchars($comments->author)); ?>" href="<?php $security->index('/action/comments-edit?do=delete&coid=' . $comments->coid); ?>" class="operate-delete"><?php _e('删除'); ?></a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        <?php else: ?>
                        <tr>
                            <td colspan="4"><h6 class="typecho-list-table-title"><?php _e('没有评论') ?></h6></td>
                        </tr>
                        <?php endif; ?>
                        </tbody>
                    </table><!-- end .typecho-list-table -->
                </div><!-- end .typecho-table-wrap -->

                <?php if(isset($request->cid)): ?>
                <input type="hidden" value="<?php echo htmlspecialchars($request->get('cid')); ?>" name="cid" />
                <?php endif; ?>
                </form><!-- end .operate-form -->

                <div class="typecho-list-operate clearfix">
                    <form method="get">
                        <div class="operate">
                            <label><i class="sr-only"><?php _e('全选'); ?></i><input type="checkbox" class="typecho-table-select-all" /></label>
                            <div class="btn-group btn-drop">
                            <button class="btn dropdown-toggle btn-s" type="button"><i class="sr-only"><?php _e('操作'); ?></i><?php _e('选中项'); ?> <i class="i-caret-down"></i></button>
                            <ul class="dropdown-menu">
                                <li><a href="<?php $security->index('/action/comments-edit?do=approved'); ?>"><?php _e('通过'); ?></a></li>
                                <li><a href="<?php $security->index('/action/comments-edit?do=waiting'); ?>"><?php _e('待审核'); ?></a></li>
                                <li><a href="<?php $security->index('/action/comments-edit?do=spam'); ?>"><?php _e('标记垃圾'); ?></a></li>
                                <li><a lang="<?php _e('你确认要删除这些评论吗?'); ?>" href="<?php $security->index('/action/comments-edit?do=delete'); ?>"><?php _e('删除'); ?></a></li>
                            </ul>
                            <?php if('spam' == $request->get('status')): ?>
                                <button lang="<?php _e('你确认要删除所有垃圾评论吗?'); ?>" class="btn btn-s btn-warn btn-operate" href="<?php $security->index('/action/comments-edit?do=delete-spam'); ?>"><?php _e('删除所有垃圾评论'); ?></button>
                            <?php endif; ?>
                            </div>
                        </div>
                        <?php if($comments->have()): ?>
                        <ul class="typecho-pager">
                            <?php $comments->pageNav(); ?>
                        </ul>
                        <?php endif; ?>
                    </form>
                </div><!-- end .typecho-list-operate -->
            </div><!-- end .typecho-list -->
        </div><!-- end .typecho-page-main -->
    </div>
</div>
<?php
include 'copyright.php';
include 'common-js.php';
include 'table-js.php';
?>
<script type="text/javascript">
$(document).ready(function () {
    // 记住滚动条
    function rememberScroll () {
        $(window).bind('beforeunload', function () {
            $.cookie('__typecho_comments_scroll', $('body').scrollTop());
        });
    }

    // 自动滚动
    (function () {
        var scroll = $.cookie('__typecho_comments_scroll');

        if (scroll) {
            $.cookie('__typecho_comments_scroll', null);
            $('html, body').scrollTop(scroll);
        }
    })();

    $('.operate-delete').click(function () {
        var t = $(this), href = t.attr('href'), tr = t.parents('tr');

        if (confirm(t.attr('lang'))) {
            tr.fadeOut(function () {
                rememberScroll();
                window.location.href = href;
            });
        }

        return false;
    });

    $('.operate-approved, .operate-waiting, .operate-spam').click(function () {
        rememberScroll();
        window.location.href = $(this).attr('href');
        return false;
    });

    $('.operate-reply').click(function () {
        var td = $(this).parents('td'), t = $(this);

        if ($('.comment-reply', td).length > 0) {
            $('.comment-reply').remove();
        } else {
            var form = $('<form method="post" action="'
                + t.attr('rel') + '" class="comment-reply">'
                + '<p><label for="text" class="sr-only"><?php _e('内容'); ?></label><textarea id="text" name="text" class="w-90 mono" rows="3"></textarea></p>'
                + '<p><button type="submit" class="btn btn-s primary"><?php _e('回复'); ?></button> <button type="button" class="btn btn-s cancel"><?php _e('取消'); ?></button></p>'
                + '</form>').insertBefore($('.comment-action', td));

            $('.cancel', form).click(function () {
                $(this).parents('.comment-reply').remove();
            });

            var textarea = $('textarea', form).focus();

            form.submit(function () {
                var t = $(this), tr = t.parents('tr'), 
                    reply = $('<div class="comment-reply-content"></div>').insertAfter($('.comment-content', tr));
                
                reply.html('<p>' + textarea.val() + '</p>');
                $.post(t.attr('action'), t.serialize(), function (o) {
                    reply.html(o.comment.content)
                        .effect('highlight');
                }, 'json');

                t.remove();
                return false;
            });
        }

        return false;
    });

    $('.operate-edit').click(function () {
        var tr = $(this).parents('tr'), t = $(this), id = tr.attr('id'), comment = tr.data('comment');
        tr.hide();

        var edit = $('<tr class="comment-edit"><td> </td>'
                        + '<td colspan="2" valign="top"><form method="post" action="'
                        + t.attr('rel') + '" class="comment-edit-info">'
                        + '<p><label for="' + id + '-author"><?php _e('用户名'); ?></label><input class="text-s w-100" id="'
                        + id + '-author" name="author" type="text"></p>'
                        + '<p><label for="' + id + '-mail"><?php _e('电子邮箱'); ?></label>'
                        + '<input class="text-s w-100" type="email" name="mail" id="' + id + '-mail"></p>'
                        + '<p><label for="' + id + '-url"><?php _e('个人主页'); ?></label>'
                        + '<input class="text-s w-100" type="text" name="url" id="' + id + '-url"></p></form></td>'
                        + '<td valign="top"><form method="post" action="'
                        + t.attr('rel') + '" class="comment-edit-content"><p><label for="' + id + '-text"><?php _e('内容'); ?></label>'
                        + '<textarea name="text" id="' + id + '-text" rows="6" class="w-90 mono"></textarea></p>'
                        + '<p><button type="submit" class="btn btn-s primary"><?php _e('提交'); ?></button> '
                        + '<button type="button" class="btn btn-s cancel"><?php _e('取消'); ?></button></p></form></td></tr>')
                        .data('id', id).data('comment', comment).insertAfter(tr);

        $('input[name=author]', edit).val(comment.author);
        $('input[name=mail]', edit).val(comment.mail);
        $('input[name=url]', edit).val(comment.url);
        $('textarea[name=text]', edit).val(comment.text).focus();

        $('.cancel', edit).click(function () {
            var tr = $(this).parents('tr');

            $('#' + tr.data('id')).show();
            tr.remove();
        });

        $('form', edit).submit(function () {
            var t = $(this), tr = t.parents('tr'),
                oldTr = $('#' + tr.data('id')),
                comment = oldTr.data('comment');

            $('form', tr).each(function () {
                var items  = $(this).serializeArray();

                for (var i = 0; i < items.length; i ++) {
                    var item = items[i];
                    comment[item.name] = item.value;
                }
            });

            var html = '<strong class="comment-author">'
                + (comment.url ? '<a target="_blank" href="' + comment.url + '">'
                + comment.author + '</a>' : comment.author) + '</strong>'
                + ('comment' != comment.type ? '<small><?php _e('引用'); ?></small>' : '')
                + (comment.mail ? '<br /><span><a href="mailto:' + comment.mail + '">'
                + comment.mail + '</a></span>' : '')
                + (comment.ip ? '<br /><span>' + comment.ip + '</span>' : '');

            $('.comment-meta', oldTr).html(html)
                .effect('highlight');
            $('.comment-content', oldTr).html('<p>' + comment.text + '</p>');
            oldTr.data('comment', comment);

            $.post(t.attr('action'), comment, function (o) {
                $('.comment-content', oldTr).html(o.comment.content)
                    .effect('highlight');
            }, 'json');
            
            oldTr.show();
            tr.remove();

            return false;
        });

        return false;
    });
});
</script>
<?php
include 'footer.php';
?>
