<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('header.php'); ?>
<?php
function geturl($url, $cookie) {
	$headerArray = array("User-Agent: typecho");
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headerArray);
	curl_setopt($ch, CURLOPT_COOKIE, $cookie);
	$output = curl_exec($ch);
	curl_close($ch);
	return $output;
}

global $name; global $prefix;
$api = "https://api.github.com/repos/LittleYang0531/image/contents/github";
$prefix = "https://github-raw.littleyang.ml/LittleYang0531/image/main/github/";
$name = array(); $json = geturl($api, "");
$arr = json_decode($json, true);
for ($i = 0; $arr != null && $i < count($arr); $i++) $name[] = $arr[$i]["name"];
function random_picture() {
    global $name; global $prefix;
    if ($name == null) return ""; 
    $num = rand(0, count($name) - 1);
    return $prefix . $name[$num];
}
?>
<div class="mdui-container mdui-typo searchData" role="main">
    <h3><?php $this->archiveTitle(array(
            'category'  =>  _t('%s 下的文章'),
            'search'    =>  _t('包含关键字 %s 的文章'),
            'tag'       =>  _t('标签 %s 下的文章'),
            'author'    =>  _t('%s 发布的文章')
        ), '', ''); ?></h3>
    <?php if ($this->have()): ?>
        <div class="mdui-row">
        <?php while($this->next()): ?>
            <article class="s-data-card mdui-col-md-4">
                <div class="s-data-card-con shadow-2">
                    <div class="colorHi">
                        <?php if($this->options->slimg && 'guanbi'==$this->options->slimg): ?>
                        <?php else: ?>
                            <?php if($this->options->slimg && 'showoff'==$this->options->slimg): ?><a href="<?php $this->permalink() ?>" ><?php showThumbnail($this); ?></a>
                            <?php else: ?>
                                <div class="cardImage-img" style="background-image: url('<?php echo random_picture(); ?>')"></div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                    <h5><a href="<?php $this->permalink(); ?>"><?php $this->title() ?></a></h5>
                    <p>
                        <?php _e('作者: '); ?><a itemprop="name" href="<?php $this->author->permalink(); ?>" rel="author"><?php $this->author(); ?></a>
                        &nbsp<?php _e('时间: '); ?><time datetime="<?php $this->date('c'); ?>" itemprop="datePublished"><?php $this->date(); ?></time>
                    </p>
                    <p>
                        <a href="<?php $this->permalink() ?>">
                            阅读更多
                        </a>
                    </p>
                    <div class="mdui-divider" style="margin-bottom: 10px"></div>
                    <p>
                        <?php $this->excerpt(50,'...'); ?>
                    </p>
                </div>
            </article>
        <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div>呀，没有找到您想要的东西,一定是姿势不对不如试试搜索</div>
        <form method="post" class="findMore-404 getData-input">
            <input placeholder="搜索内容( Enter确认 )" type="text" name="s" autofocus />
        </form>
    <?php endif; ?>
    <div class="mdui-row" id="search-list-nav">
        <div class="nav mdui-col-md-4 mdui-col-xs-10 mdui-col-offset-xs-1">
            <?php $this->pageNav('<i class="mdui-icon material-icons">keyboard_arrow_left</i>', '<i class="mdui-icon material-icons">&#xe315;</i>',0, '...', 'wrapTag=ul&wrapClass=page-navigator&itemTag=li&textTag=span&tClass=current&prevClass=prev&nextClass=next'); ?>
        </div>
    </div>
</div><!-- end #main -->

<?php $this->need('footer.php'); ?>
