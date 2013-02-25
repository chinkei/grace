<?php
include 'Output.php';
$o = Grace_IO_Output::getInstance();
//$o->statusHeader(200);
$o->setContentType('text/html');
//ob_start(array($o, 'compress'));
$a = '陈佳faf';
$o->setBody($a, 1);
$o->send();
echo'bagag';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>如何在PHP开启gzip页面压缩实例_PHP教程_编程技术</title>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<meta name="keywords" content="PHP gzip页面压缩" />
<meta name="description" content="PHP开启gzip页面压缩实例 PHP开启gzip页面压缩实例" />
<link rel="stylesheet" type="text/css" href="http://www.alixixi.com/skins/style.css">
<link rel="stylesheet" type="text/css" href="http://www.alixixi.com/skins/program.css">
<link rel="icon" type="image/x-icon" href="http://www.alixixi.com/favicon.ico" />
<link rel="shortcut icon" type="image/x-icon" href="http://www.alixixi.com/favicon.ico" />
<script src="http://www.alixixi.com/script/dhtml.js" type="text/javascript"></script>
</head>
<body id="body_13">
<div class="topbg"> <a href="http://www.alixixi.com" title="网页制作教程,web开发之家" class="logo" onFocus="this.blur()"></a>
  <div class="topsearch">
    <form action="http://www.alixixi.com/search.asp" method="get">
      <div class="toplink"><span class="sof">
        <input name="m" type="radio" value="1" checked="checked" />
        文章&nbsp;
        <input name="m" type="radio" value="3" />
        下载</span><span class="lk"><a href="http://www.alixixi.com/about/ad.asp" target="_blank">广告合作</a> | <a href="http://www.alixixi.cn/dvd/" target="_blank">素材光盘</a> | <a href="http://tool.alixixi.com" target="_blank">站长工具</a></span></div>
      <input class="wd" name="wd" type="text"  value="请输入关键字"  onmouseover="this.focus()" onBlur="if (this.value =='') this.value='请输入关键字'" onFocus="this.select()" onClick="if (this.value=='请输入关键字') this.value=''" />
      <input name="s" type="submit" value="站内搜索" class="s" />
    </form>
  </div>
</div>
<div id="header">
  <div class="topNav"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="http://www.alixixi.com/web/c/html_1.shtml" target="_blank">网页基础</a>|<a href="http://www.alixixi.com/web/c/xhtml_1.shtml" target="_blank">网站重构</a>|<a href="http://www.alixixi.com/web/c/css_1.shtml" target="_blank">样式表</a>|<a href="http://www.alixixi.com/web/c/script_1.shtml" target="_blank">脚本</a>|<a href="http://www.alixixi.com/web/c/sheji_1.shtml" target="_blank">软件</a>|<a href="http://www.alixixi.com/zz/" target="_blank">站长文章</a>&nbsp;<span></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="http://down.alixixi.com/" target="_blank">网站源码</a>|<a href="http://book.alixixi.com/" target="_blank">电子书</a>|<a href="http://mb.alixixi.com/c/fonts_1.shtml" target="_blank">字体库</a>|<a href="http://down.alixixi.com/c/software_1.shtml" target="_blank">软件</a>&nbsp;<span></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="http://js.alixixi.com/c/guanggao_1.shtml" target="_blank">广告代码</a>|<a href="http://js.alixixi.com/c/menu_1.shtml" target="_blank">菜单特效</a>|<a href="http://js.alixixi.com/c/datetime_1.shtml" target="_blank">日期特效</a>|<a href="http://js.alixixi.com/c/picture_1.shtml" target="_blank">图像</a>|<a href="http://js.alixixi.com/c/text_1.shtml" target="_blank">文字</a>|<a href="http://js.alixixi.com/c/example_1.shtml" target="_blank">实例</a>&nbsp;<span></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="http://cool.alixixi.com/class-172.shtml" target="_blank">欧美酷站</a>|<a href="http://cool.alixixi.com/class-171.shtml" target="_blank">韩国酷站</a>|<a href="http://cool.alixixi.com/class-173.shtml" target="_blank">中国站</a><br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="http://www.alixixi.com/web/" target="_blank">前端技术</a>|<a href="http://www.alixixi.com/program/" target="_blank">编程开发</a>|<a href="http://www.alixixi.com/program/c/database_1.shtml" target="_blank">数据库</a>|<a href="http://www.alixixi.com/web/c/donghua_1.shtml" target="_blank">动画</a>|<a href="http://www.alixixi.com/web/c/kaifawendang_1.shtml" target="_blank">文档</a>|<a href="http://www.alixixi.com/zz/c/experience_1.shtml" target="_blank">网站运营</a>&nbsp;<span></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="http://mb.alixixi.com" target="_blank">网页模板</a>|<a href="http://mb.alixixi.com/c/sheji_1.shtml" target="_blank">素材库</a>|<a href="http://down.alixixi.com/s/serv_1.shtml" target="_blank">服务器</a>|<a href="http://mb.alixixi.com/c/tubiao_1.shtml" target="_blank">图标</a>&nbsp;<span></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="http://js.alixixi.com/s/pfad_1.shtml" target="_blank">漂浮特效</a>|<a href="http://js.alixixi.com/c/table_1.shtml" target="_blank">图层特效</a>|<a href="http://js.alixixi.com/c/form_1.shtml" target="_blank">表单特效</a>|<a href="http://js.alixixi.com/c/mouse_1.shtml" target="_blank">鼠标</a>|<a href="http://js.alixixi.com/c/color_1.shtml" target="_blank">色彩</a>|<a href="http://js.alixixi.com/c/window_1.shtml" target="_blank">窗口</a>&nbsp;<span></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="http://cool.alixixi.com/color-4.shtml" target="_blank" style="color:red;">红-</a>|<a href="http://cool.alixixi.com/color-6.shtml" target="_blank" style="color:#FFCC00">黄-</a>|<a href="http://cool.alixixi.com/color-7.shtml" target="_blank" style="color:green">绿-</a>|<a href="http://cool.alixixi.com/color-9.shtml" target="_blank" style="color:blue">蓝-</a>|<a href="http://cool.alixixi.com/color-10.shtml" target="_blank" style="color:#6600FF">紫-</a>|<a href="http://cool.alixixi.com/color-5.shtml" target="_blank" style="color:#FF6600">橙-</a>|<a href="http://cool.alixixi.com/color-1.shtml" target="_blank" style="color:#FF00FF">炫</a></div>
</div>
</div>
<div id="topad"><script src="http://www.alixixi.com/adsview/adimg.js"></script></div>
<div id="txtad">您的位置：<a href="/">阿里西西</a> &gt; <a href="/program/">编程技术</a> &gt; <a href="/program/c/php_1.shtml">PHP教程</a> &gt; 如何在PHP开启gzip页面压缩实例<span></span></div>

<div class="main">
	<div class="showbox"><div class="listinfo">
          <div id="title">
            <div class="face"><a href="javascript:void(0);" onclick="digg(59536);" title="好文章，顶上去！"><span id="digg">-</span>顶</a></div>
            <div class="info">
              <h1 class="tt" title="PHP开启gzip页面压缩实例">如何在PHP开启gzip页面压缩实例</h1>
              <span class="tm">
              <input type="hidden" name="articleid" id="articleid" value="59536">
              &nbsp;&nbsp;互联网 &nbsp;&nbsp;Alixixi &nbsp;&nbsp;2010-03-16 &nbsp;&nbsp;点击：
              <label id="hits">1</label>
              &nbsp;&nbsp;<a href="#replay" >我要评论</a></span> </div>
            <div class="clear mb5"></div>
          </div>
          <div id="context">
           <div class="listintro"><span id="sp"></span>PHP开启gzip页面压缩实例</div>
             <div style="width:340px;height:285px;float:left;">
<script type="text/javascript"> /*新站|教程.正文顶部*/ var cpro_id = 'u260834';</script>
<script type="text/javascript" src="http://cpro.baidu.com/cpro/ui/c.js"></script>
</div>

            <p><strong>示例一（用php的内置压缩函数）：</strong></p>
<p>
<table border="0" cellspacing="0" cellpadding="6" width="95%" align="center" style="border-bottom: #0099cc 1px solid; border-left: #0099cc 1px solid; table-layout: fixed; border-top: #0099cc 1px solid; border-right: #0099cc 1px solid">
    <tbody>
        <tr>
            <td bgcolor="#ddedfb" style="word-wrap: break-word">&lt;?PHP <br/>
            if(Extension_Loaded('zlib')) Ob_Start('ob_gzhandler'); <br/>
            Header(&quot;Content-type: text/html&quot;); <br/>
            ?&gt; <br/>
            &lt;!DOCTYPE html PUBLIC &quot;-//W3C//DTD XHTML 1.0 Transitional//EN&quot; &quot;http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd&quot;&gt; <br/>
            &lt;html xmlns=&quot;http://www.w3.org/1999/xhtml&quot;&gt; <br/>
            &lt;head&gt; <br/>
            &lt;meta http-equiv=&quot;Content-Type&quot; content=&quot;text/html; charset=gb2312&quot; /&gt; <br/>
            &lt;title&gt;无标题文档&lt;/title&gt; <br/>
            &lt;/head&gt; <br/>
            &lt;body&gt; <br/>
            &lt;?php <br/>
            for($i=0;$i&lt;10000;$i++){ <br/>
            echo 'Hello World!'; <br/>
            } <br/>
            ?&gt; <br/>
            &lt;/body&gt; <br/>
            &lt;/html&gt; <br/>
            &lt;?PHP <br/>
            if(Extension_Loaded('zlib')) Ob_End_Flush(); <br/>
            ?&gt;</td>
        </tr>
    </tbody>
</table>
</p>
<p><strong>示例二（自写函数）：</strong></p>
<p>
<table border="0" cellspacing="0" cellpadding="6" width="95%" align="center" style="border-bottom: #0099cc 1px solid; border-left: #0099cc 1px solid; table-layout: fixed; border-top: #0099cc 1px solid; border-right: #0099cc 1px solid">
    <tbody>
        <tr>
            <td bgcolor="#ddedfb" style="word-wrap: break-word">&lt;?php ob_start('ob_gzip'); ?&gt; <br/>
            <br/>
            &lt;!DOCTYPE html PUBLIC &quot;-//W3C//DTD XHTML 1.0 Transitional//EN&quot; &quot;http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd&quot;&gt; <br/>
            &lt;html xmlns=&quot;http://www.w3.org/1999/xhtml&quot;&gt; <br/>
            &lt;head&gt; <br/>
            &lt;meta http-equiv=&quot;Content-Type&quot; content=&quot;text/html; charset=gb2312&quot; /&gt; <br/>
            &lt;title&gt;无标题文档&lt;/title&gt; <br/>
            &lt;/head&gt; <br/>
            <br/>
            &lt;body&gt; <br/>
            &lt;/body&gt; <br/>
            &lt;/html&gt; <br/>
            <br/>
            &lt;?php <br/>
            ob_end_flush(); <br/>
            //压缩函数 <br/>
            function ob_gzip($content){ <br/>
            if(!headers_sent()&&extension_loaded(&quot;zlib&quot;)&&strstr($_SERVER[&quot;HTTP_ACCEPT_ENCODING&quot;],&quot;gzip&quot;)){ <br/>
            $content = gzencode($content,9); <br/>
            header(&quot;Content-Encoding: gzip&quot;); <br/>
            header(&quot;Vary: Accept-Encoding&quot;); <br/>
            header(&quot;Content-Length: &quot;.strlen($content)); <br/>
            } <br/>
            return $content; <br/>
            } <br/>
            ?&gt;</td>
        </tr>
    </tbody>
</table>
</p>
            <div style="width:100%;text-align:center;margin:10px 0">
<script type="text/javascript"><!--
google_ad_client = "ca-pub-3553298197128602";
/* 468x60, 创建于 10-9-9 */
google_ad_slot = "3842766146";
google_ad_width = 468;
google_ad_height = 60;
//-->
</script>
<script type="text/javascript"
src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script></div>

<br />
<br />

<div style="width:100%;text-align:center;margin:10px 0">
<script type="text/javascript">
/*XX文字*/
var cpro_id = "u1103383";
</script>
<script src="http://cpro.baidustatic.com/cpro/ui/c.js" type="text/javascript"></script>
</div>
            <div class="clear"></div>

            <div class="tags">更多关于 <a href="/program/tag.asp?name=PHP" target="_blank">PHP</a> <a href="/program/tag.asp?name=gzip%D2%B3%C3%E6%D1%B9%CB%F5" target="_blank">gzip页面压缩</a>  的文章<span></span></div>
                      <div id="nextpage"><b>·上一篇：</b><a href="/program/a/2010031659535.shtml">通过索引优化含ORDER BY的MySQL语句</a><br/>
<b>·下一篇：</b><a href="/program/a/2010031659537.shtml">动态程序防采集的新方法</a><br/></div></div>

          <iframe id="moodiframe" width="630" scrolling="no" height="105" marginheight="0" marginwidth="0"  src="/plus/mood.html" frameborder="0" style="margin:5px 0;"></iframe>
          <div class="bbox ads">
          <div class="box_caption">相关阅读</div>
          <ul>
          <li><a href="/program/a/2011092874083.shtml" title="PHP.ini配置文件（中文）">PHP.ini配置文件（中文）</a><span>(2011-09-28 01:43:44)</span></li>
<li><a href="/program/a/2011092874081.shtml" title="PHP安全配置详解">PHP安全配置详解</a><span>(2011-09-28 01:43:41)</span></li>
<li><a href="/program/a/2011090773795.shtml" title="PHPUnZip：在线解压缩PHP的工具">PHPUnZip：在线解压缩PHP的工具</a><span>(2011-09-07 21:22:48)</span></li>
<li><a href="/program/a/2011090773793.shtml" title="PHP获取MAC地址">PHP获取MAC地址</a><span>(2011-09-07 21:22:29)</span></li>
<li><a href="/program/a/2011090773792.shtml" title="PHP内核介绍及扩展开发指南—基础知识">PHP内核介绍及扩展开发指南—基础知...</a><span>(2011-09-07 21:22:24)</span></li>
<li><a href="/program/a/2011090773791.shtml" title="php各种编码集详解和在什么情况下进行使用">php各种编码集详解和在什么情况下进...</a><span>(2011-09-07 21:22:21)</span></li>
<li><a href="/program/a/2011081973308.shtml" title="PHP数组交集的优化">PHP数组交集的优化</a><span>(2011-08-19 12:52:40)</span></li>
<li><a href="/program/a/2011081573213.shtml" title="PHP 5.3的新增魔术方法 __invoke">PHP 5.3的新增魔术方法 __invoke</a><span>(2011-08-15 13:33:12)</span></li>

        </ul>
            <div class="clear"></div>
          </div>
          <div id="commentpost">
          <a name="replay"></a>
          <div class="box_caption">会员评论<a href="/program/comment.asp?id=59536" class="more" target="_blank">所有会员评论</a></div>
          
          <div class="clear mb5"></div>
          			<a name="comment"></a><br>
			<iframe style="display:none;" name="_blankframe"></iframe>
<form id="apost" name="commentform" method="post" action="/public/comment.asp" target="_blankframe">
				<input type="hidden" name="postid" value="59536" />
				<input type="hidden" name="channelid" value="11" />
				<input type="hidden" name="modules" value="1" />
				<input type="hidden" name="action" value="save" />
				<input type="hidden" name="history" value="1" />
				<p><textarea name="content" cols="70" rows="5" class="acom"></textarea></p>
	<p><input type="submit" name="submit_button" value="提交" class="button" /> &nbsp;&nbsp;</p>
</form>
        </div>
</div>
    </div>
    <div class="rshow">
    	  <div id="rad1"><script src="http://www.alixixi.com/adsview/article_r1.js" type="text/javascript"></script></div>
    	  <div><div id="NewsTop">
			<div id="NewsTop_tit">
				<p class="topTit" style="width:80px">相关排行总榜</p>
				<p class="topC0">ASP教程</p>
				<p class="topC0">PHP教程</p>
				<p class="topC0">.NET教程</p>
			</div>
			<div id="NewsTop_cnt"> 
				<span title="Don't delete me"></span>
				<span>
					<a href="http://www.alixixi.com/program/a/2009071056798.shtml" title="伪静态的实现方法：IIS环境下配置Rewrite规则">伪静态的实现方法：IIS环境下配置Rewri...</a><br />
<a href="http://www.alixixi.com/program/a/2009022521851.shtml" title="ASP生成静态首页的示例代码">ASP生成静态首页的示例代码</a><br />
<a href="http://www.alixixi.com/program/a/2010062661984.shtml" title="成为优秀的Web开发人员的学习步骤和学习内容">成为优秀的Web开发人员的学习步骤和学习...</a><br />
<a href="http://www.alixixi.com/program/a/2009040321864.shtml" title="水晶报表 配置错误:CrystalDecisions.Web ，系统找不到指定的文件的解决方法">水晶报表 配置错误:CrystalDecisions.W...</a><br />
<a href="http://www.alixixi.com/program/a/2008020618268.shtml" title="WEB打印设置解决方案二（利用ScriptX.cab控件改变IE打印设置）">WEB打印设置解决方案二（利用ScriptX.c...</a><br />
<a href="http://www.alixixi.com/program/a/2009062456371.shtml" title="asp防盗链技术的使用">asp防盗链技术的使用</a><br />
<a href="http://www.alixixi.com/program/a/2008070720430.shtml" title="ASP中有关字符编码转换的几个有用函数">ASP中有关字符编码转换的几个有用函数</a><br />
<a href="http://www.alixixi.com/program/a/2008071321752.shtml" title="最简单的ASP生成静态HTML页的方法[FSO支持]">最简单的ASP生成静态HTML页的方法[FSO支...</a><br />
<a href="http://www.alixixi.com/program/a/2010092765107.shtml" title="AspJpeg2.0组件教程完整版 aspjpeg教程">AspJpeg2.0组件教程完整版 aspjpeg教程...</a><br />
<a href="http://www.alixixi.com/program/a/2009071356841.shtml" title="ASP程序中调用函数Now()异常的问题">ASP程序中调用函数Now()异常的问题</a><br />

				</span>					
				<span>
					<a href="http://www.alixixi.com/program/a/2010062662041.shtml" title="PHP:招PHP高级工程师的面试题">PHP:招PHP高级工程师的面试题</a><br />
<a href="http://www.alixixi.com/program/a/2011033169189.shtml" title="PHP代码源有可能被污染 建议下载要小心">PHP代码源有可能被污染 建议下载要小心...</a><br />
<a href="http://www.alixixi.com/program/a/2011070572222.shtml" title="php中对2个数组相加的函数">php中对2个数组相加的函数</a><br />
<a href="http://www.alixixi.com/program/a/2011070572111.shtml" title="php学习之 数组声明">php学习之 数组声明</a><br />
<a href="http://www.alixixi.com/program/a/2009081457502.shtml" title="PHP:避免重复提交和检查数据来路">PHP:避免重复提交和检查数据来路</a><br />
<a href="http://www.alixixi.com/program/a/2011031468571.shtml" title="让PHP COOKIE立即生效,不用刷新就可以使用">让PHP COOKIE立即生效,不用刷新就可以使...</a><br />
<a href="http://www.alixixi.com/program/a/2011070572172.shtml" title="PHP5中新增stdClass 内部保留类">PHP5中新增stdClass 内部保留类</a><br />
<a href="http://www.alixixi.com/program/a/2011070572244.shtml" title="一个基于PDO的数据库操作类(新) 一个PDO事务实例">一个基于PDO的数据库操作类(新) 一个PD...</a><br />
<a href="http://www.alixixi.com/program/a/2011012067045.shtml" title="php循环检测目录是否存在并创建(循环创建目录)">php循环检测目录是否存在并创建(循环创...</a><br />
<a href="http://www.alixixi.com/program/a/2011070572140.shtml" title="实用PHP会员权限控制实现原理分析">实用PHP会员权限控制实现原理分析</a><br />

				</span>
				<span>
					<a href="http://www.alixixi.com/program/a/2008050727580.shtml" title="c#操作XML（读XML，写XML，更新，删除节点,与dataset结合等）">c#操作XML（读XML，写XML，更新，删除节...</a><br />
<a href="http://www.alixixi.com/program/a/2009062656457.shtml" title="分享：.NET发送邮件">分享：.NET发送邮件</a><br />
<a href="http://www.alixixi.com/program/a/2008100829831.shtml" title="用户 &amp;#39;sa&amp;#39; 登录失败。原因: 未与信任 SQL Server 连接 的解决方法">用户 &#39;sa&#39; 登录失败。原因: 未...</a><br />
<a href="http://www.alixixi.com/program/a/2008050627060.shtml" title="在asp.net页面中传递中文参数">在asp.net页面中传递中文参数</a><br />
<a href="http://www.alixixi.com/program/a/2008050727567.shtml" title="c#封装jmail的pop3收邮件">c#封装jmail的pop3收邮件</a><br />
<a href="http://www.alixixi.com/program/a/2011012667198.shtml" title="IIS7下Asp.net网站优化站点性能技巧">IIS7下Asp.net网站优化站点性能技巧</a><br />
<a href="http://www.alixixi.com/program/a/2008050727843.shtml" title="C# Socket编程">C# Socket编程</a><br />
<a href="http://www.alixixi.com/program/a/2008050727443.shtml" title="c#保存文件时候的弹出选择要保存的文件夹带新建文件夹效果的类代码">c#保存文件时候的弹出选择要保存的文件...</a><br />
<a href="http://www.alixixi.com/program/a/2011012967433.shtml" title="Silverlight 实现下载文件功能">Silverlight 实现下载文件功能</a><br />
<a href="http://www.alixixi.com/program/a/2008070228152.shtml" title="web.config 关于HttpHandlers 和HttpModules的使用实例【转】">web.config 关于HttpHandlers 和HttpMo...</a><br />

				</span>					
				</span>
			</div>		
			<script>
				var Tags=document.getElementById('NewsTop_tit').getElementsByTagName('p'); 
				var TagsCnt=document.getElementById('NewsTop_cnt').getElementsByTagName('span'); 
				var len=Tags.length; 
				var flag=1;//修改默认值
				for(i=1;i<len;i++){
					Tags[i].value = i;
					Tags[i].onmouseover=function(){changeNav(this.value)}; 
					TagsCnt[i].className='undis';					
				}
				Tags[flag].className='topC1';
				TagsCnt[flag].className='dis';
				function changeNav(v){	
					Tags[flag].className='topC0';
					TagsCnt[flag].className='undis';
					flag=v;	
					Tags[v].className='topC1';
					TagsCnt[v].className='dis';
				}
			</script>
</div></div>
    	 
    	  <div><div class="side_box mb5">
    <h3>分类热门文章</h3>
			  <a class="all" href="http://www.alixixi.com/program/s/top_1.shtml">更多 &#187;</a>
        
        <div class="side_content">
        <ul class="side_list">
        	<li><a href="http://www.alixixi.com/program/a/2009060956046.shtml" title="磁盘阵列RAID0，RAID1和RAID5的区别和安全性" target="_blank">磁盘阵列RAID0，RAID1和RAID5的区别...</a></li>
<li><a href="http://www.alixixi.com/program/a/2009082857788.shtml" title="解决并清除SQL被注入&amp;lt;script&amp;gt;恶意病毒代码的语句" target="_blank">解决并清除SQL被注入&lt;script&gt...</a></li>
<li><a href="http://www.alixixi.com/program/a/2008100829831.shtml" title="用户 &amp;#39;sa&amp;#39; 登录失败。原因: 未与信任 SQL Server 连接 的解决方法" target="_blank">用户 &#39;sa&#39; 登录失败。原因...</a></li>
<li><a href="http://www.alixixi.com/program/a/2008082955244.shtml" title="网站项目计划书模板范本" target="_blank">网站项目计划书模板范本</a></li>
<li><a href="http://www.alixixi.com/program/a/2009102958831.shtml" title="ASP用户登录模块的设计" target="_blank">ASP用户登录模块的设计</a></li>
<li><a href="http://www.alixixi.com/program/a/2008020518452.shtml" title="用ASP设计网站在线人数统计程序" target="_blank">用ASP设计网站在线人数统计程序</a></li>
<li><a href="http://www.alixixi.com/program/a/2008050727580.shtml" title="c#操作XML（读XML，写XML，更新，删除节点,与dataset结合等）" target="_blank">c#操作XML（读XML，写XML，更新，删...</a></li>
<li><a href="http://www.alixixi.com/program/a/2008091921636.shtml" title="源码实例：ASP实现远程保存图片" target="_blank">源码实例：ASP实现远程保存图片</a></li>
<li><a href="http://www.alixixi.com/program/a/2009022833482.shtml" title="一天学会PHP~!" target="_blank">一天学会PHP~!</a></li>
<li><a href="http://www.alixixi.com/program/a/2008070720804.shtml" title="asp快速分页代码" target="_blank">asp快速分页代码</a></li>

        </ul>
        <div class="clear"></div>
        </div>
    </div></div>
    	  
 <div id="rad2"><script>google_ad_client = "pub-3553298197128602";
google_ad_slot = "3370710432";
google_ad_width = 300;
google_ad_height = 250;
</script>
<script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script></div>
    </div>
</div>
<script>
setcount('/program/',59536);
</script>

<div class="clear"></div>
<div class="footer">
  <div class="info">
    <a href="http://www.alixixi.cn/dvd/" target="_blank"><img src="http://www.alixixi.com/hots/sale.gif" style="position:absolute;left:-28px;top:15px;border:0;" /></a><table width="958" border="0" cellpadding="0" cellspacing="0" bgcolor="#CCCCCC" style="margin:10px 0 0 0">
 
  <tr>
    <td style="padding:2px 0;"><a href="http://www.alixixi.cn/dvd/catalog-6-1.html"><img style="margin-left:2px" border="0" src="http://www.alixixi.cn/images/index/a7.gif"></a></td>
    <td></td>
    <td><a href="http://shop.alixixi.com/webdesigner/"><img border="0" src="http://www.alixixi.cn/images/index/a8.gif"></a></td>
    <td></td>
    <td><a href="http://www.alixixi.cn/dvd/catalog-5-1.html"><img border="0" src="http://www.alixixi.cn/images/index/a9.gif"></a></td>
    <td></td>
    <td><a href="http://www.alixixi.cn/dvd/19.html"><img style="margin-left:2px" border="0" src="http://www.alixixi.cn/images/index/a4.gif"></a></td>
    <td></td>
    <td><a href="http://www.alixixi.cn/dvd/33.html"><img border="0" src="http://www.alixixi.cn/images/index/a5.gif"></a></td>
    <td></td>
    <td><a href="http://www.alixixi.cn/dvd/215.html"><img border="0" src="http://www.alixixi.cn/images/index/a6.gif"></a></td>
    <td></td>
    <td><a href="http://www.alixixi.cn/dvd/catalog-8-1.html"><img style="margin-left:2px" border="0" src="http://www.alixixi.cn/images/index/a3.gif"></a></td>
    <td></td>
    <td><a href="http://www.alixixi.cn/dvd/105.html"><img style="margin-left:2px" border="0" src="http://www.alixixi.cn/images/index/a2.gif"></a></td>
    <td></td>
    <td><a href="http://www.alixixi.cn/dvd/catalog-65-1.html"><img style="margin-left:2px" border="0" src="http://www.alixixi.cn/images/index/a1.gif"></a></td>
  </tr>
</table>
      </div>
  <div class="copyright">
    <div class="copy"><A href="http://www.alixixi.com/about/gy.asp" target="_blank">关于我们</A> | <A href="http://www.alixixi.com/about/ad.asp" target="_blank">广告服务</A> | <A href="http://www.alixixi.com/sitemap.shtml" target="_blank">网站地图</A> | <A href="http://dir.alixixi.com/add_submit.asp" target="_blank">网站收录</A> | 客服QQ：<a href="tencent://message/?uin=663313&Site=在线客服2&Menu=yes" target="_blank"><img src="http://www.alixixi.com/images/qq1_online.gif" align="absmiddle" />663313</a> <span>Copyright &copy; 2004 - 2011 Alixixi.com. All Rights Reserved. ICP：桂ICP备09011362号</span></div>
  </div>
</div>
<script src="http://www.alixixi.com/script/count.js" type="text/javascript"></script>
</body>
</html>
<?php
//ob_end_flush();
?>
