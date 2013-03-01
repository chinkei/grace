<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Discuz! 管理中心</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta content="Comsenz Inc." name="Copyright" />
<link media="all" type="text/css" href="{$base_url}/images/admincp/admincp.css" rel="stylesheet">
<script src="{$base_url}/js/common.js" type="text/javascript"></script>
</head>
<body style="margin: 0px" scroll="no">
<table cellpadding="0" cellspacing="0" width="100%" height="100%">
  <tr>
    <td colspan="2" height="90"><div class="mainhd">
        <div class="logo">Discuz! Administrator's Control Panel</div>
        <div class="uinfo">
          <p>您好, <em>admin</em> [ <a href="admincp.php?action=logout&sid=q7p8QM" target="_top">退出</a> ]</p>
          <p class="btnlink"><a href="index.php" target="_blank">论坛首页</a></p>
        </div>
        <div class="navbg"></div>
        <div class="nav">
          <ul id="topmenu">
            <li><em><a href="javascript:;" id="header_index" hidefocus="true" onClick="toggleMenu('index', 'home.html');">首页</a></em></li>
            <li><em><a href="javascript:;" id="header_global" hidefocus="true" onClick="toggleMenu('global', 'http://www.google.com');">全局</a></em></li>
            <li><em><a href="javascript:;" id="header_style" hidefocus="true" onClick="toggleMenu('style', 'http://www.4006666688.com');">界面</a></em></li>
            <li><em><a href="javascript:;" id="header_forum" hidefocus="true" onClick="toggleMenu('forum', 'forums');">版块</a></em></li>
            <li><em><a href="javascript:;" id="header_user" hidefocus="true" onClick="toggleMenu('user', 'members');">用户</a></em></li>
            <li><em><a href="javascript:;" id="header_topic" hidefocus="true" onClick="toggleMenu('topic', 'moderate&operation=threads');">帖子</a></em></li>
            <li><em><a href="javascript:;" id="header_extended" hidefocus="true" onClick="toggleMenu('extended', 'tasks');">扩展</a></em></li>
            <li><em><a href="javascript:;" id="header_plugin" hidefocus="true" onClick="toggleMenu('plugin', 'plugins');">插件</a></em></li>
            <li><em><a href="javascript:;" id="header_adv" hidefocus="true" onClick="toggleMenu('adv', 'adv');">广告</a></em></li>
            <li><em><a href="javascript:;" id="header_tool" hidefocus="true" onClick="toggleMenu('tool', 'tools&operation=updatecache');">工具</a></em></li>
            <li><em><a id="header_uc" hidefocus="true" href="http://localhost/comsenz/ucenter/admin.php?m=frame&a=main&iframe=1" onClick="uc_login=1;toggleMenu('uc', '');" target="main">UCenter</a></em></li>
          </ul>
          <div class="currentloca">
            <p id="admincpnav"></p>
          </div>
          <div class="navbd"></div>
          <div class="sitemapbtn">
            <div style="float: left; margin:-5px 10px 0 0">
              <form name="search" method="post" action="admincp.php?action=search" target="main">
                <input type="text" name="keywords" value="" class="txt" />
                <input type="hidden" name="searchsubmit" value="yes" class="btn" />
                <input type="submit" name="searchsubmit" value="搜索" class="btn" style="margin-top: 5px;vertical-align:middle" />
              </form>
            </div>
            <span id="add2custom"></span> <a href="###" id="cpmap" onClick="showMap();return false;"><img src="images/admincp/btn_map.gif" title="后台导航" width="72" height="18" /></a> </div>
        </div>
      </div></td>
  </tr>
  <tr>
    <td valign="top" width="160" class="menutd"><div id="leftmenu" class="menu">
        <ul id="menu_global" style="display: none">
          <li><a href="admincp.php?action=settings&operation=basic" hidefocus="true" target="main">站点信息</a></li>
          <li><a href="admincp.php?action=settings&operation=access" hidefocus="true" target="main">注册与访问</a></li>
          <li><a href="admincp.php?action=settings&operation=seo" hidefocus="true" target="main">优化设置</a></li>
          <li><a href="admincp.php?action=settings&operation=functions" hidefocus="true" target="main">论坛功能</a></li>
          <li><a href="admincp.php?action=settings&operation=permissions" hidefocus="true" target="main">用户权限</a></li>
          <li><a href="admincp.php?action=settings&operation=credits" hidefocus="true" target="main">积分设置</a></li>
          <li><a href="admincp.php?action=settings&operation=mail" hidefocus="true" target="main">邮件设置</a></li>
          <li><a href="admincp.php?action=settings&operation=sec" hidefocus="true" target="main">安全验证</a></li>
          <li><a href="admincp.php?action=settings&operation=datetime" hidefocus="true" target="main">时间设置</a></li>
          <li><a href="admincp.php?action=settings&operation=attach" hidefocus="true" target="main">附件设置</a></li>
          <li><a href="admincp.php?action=settings&operation=dzfeed" hidefocus="true" target="main">论坛动态设置</a></li>
          <li><a href="admincp.php?action=settings&operation=wap" hidefocus="true" target="main">WAP 设置</a></li>
          <li><a href="admincp.php?action=settings&operation=uc" hidefocus="true" target="main">UCenter 设置</a></li>
        </ul>
        <ul id="menu_forum" style="display: none">
          <li><a href="admincp.php?action=forums" hidefocus="true" target="main">版块管理</a></li>
          <li><a href="admincp.php?action=forums&operation=merge" hidefocus="true" target="main">版块合并</a></li>
          <li><a href="admincp.php?action=threadtypes" hidefocus="true" target="main">主题分类</a></li>
          <li><a href="admincp.php?action=threadtypes&special=1" hidefocus="true" target="main">分类信息类别</a></li>
          <li><a href="admincp.php?action=threadtypes&operation=typemodel" hidefocus="true" target="main">分类信息模型</a></li>
          <li><a href="admincp.php?action=threadtypes&operation=typeoption" hidefocus="true" target="main">分类信息选项</a></li>
        </ul>
        <ul id="menu_user" style="display: none">
          <li><a href="admincp.php?action=members&operation=add" hidefocus="true" target="main">添加用户</a></li>
          <li><a href="admincp.php?action=members" hidefocus="true" target="main">用户管理</a></li>
          <li><a href="admincp.php?action=members&operation=ban" hidefocus="true" target="main">禁止用户</a></li>
          <li><a href="admincp.php?action=members&operation=ipban" hidefocus="true" target="main">禁止 IP</a></li>
          <li><a href="admincp.php?action=members&operation=reward" hidefocus="true" target="main">积分奖惩</a></li>
          <li><a href="admincp.php?action=moderate&operation=members" hidefocus="true" target="main">审核新用户</a></li>
          <li><a href="admincp.php?action=profilefields" hidefocus="true" target="main">用户栏目定制</a></li>
          <li><a href="admincp.php?action=admingroups" hidefocus="true" target="main">管理组</a></li>
          <li><a href="admincp.php?action=usergroups" hidefocus="true" target="main">用户组</a></li>
          <li><a href="admincp.php?action=ranks" hidefocus="true" target="main">发帖数级别</a></li>
        </ul>
        <ul id="menu_topic" style="display: none">
          <li><a href="admincp.php?action=moderate&operation=threads" hidefocus="true" target="main">审核帖子</a></li>
          <li><a href="admincp.php?action=threads" hidefocus="true" target="main">批量主题管理</a></li>
          <li><a href="admincp.php?action=prune" hidefocus="true" target="main">批量删帖</a></li>
          <li><a href="admincp.php?action=attach" hidefocus="true" target="main">附件管理</a></li>
          <li><a href="admincp.php?action=recyclebin" hidefocus="true" target="main">主题回收站</a></li>
          <li><a href="admincp.php?action=misc&operation=tag" hidefocus="true" target="main">标签管理</a></li>
          <li><a href="admincp.php?action=misc&operation=censor" hidefocus="true" target="main">词语过滤</a></li>
          <li><a href="admincp.php?action=misc&operation=attachtype" hidefocus="true" target="main">附件类型尺寸</a></li>
        </ul>
        <ul id="menu_extended" style="display: none">
          <li><a href="admincp.php?action=tasks" hidefocus="true" target="main">论坛任务</a></li>
          <li><a href="admincp.php?action=magics&operation=config" hidefocus="true" target="main">道具中心</a></li>
          <li><a href="admincp.php?action=medals" hidefocus="true" target="main">勋章中心</a></li>
          <li><a href="admincp.php?action=tools&operation=tag" hidefocus="true" target="main"> 标签聚合</a></li>
          <li><a href="admincp.php?action=faq&operation=list" hidefocus="true" target="main">论坛帮助</a></li>
          <li><a href="admincp.php?action=qihoo&operation=config" hidefocus="true" target="main">奇虎搜索</a></li>
          <li><a href="admincp.php?action=settings&operation=ec" hidefocus="true" target="main">电子商务</a></li>
          <li><a href="admincp.php?action=settings&operation=msn" hidefocus="true" target="main">企业邮局</a></li>
        </ul>
        <ul id="menu_plugin" style="display: none">
          <li><a href="admincp.php?action=addons" hidefocus="true" target="main">扩展中心</a></li>
          <li><a href="admincp.php?action=plugins" hidefocus="true" target="main">论坛插件</a></li>
        </ul>
        <ul id="menu_style" style="display: none">
          <li><a href="admincp.php?action=settings&operation=styles" hidefocus="true" target="main">界面设置</a></li>
          <li><a href="admincp.php?action=styles" hidefocus="true" target="main">风格管理</a></li>
          <li><a href="admincp.php?action=templates" hidefocus="true" target="main">模板管理</a></li>
          <li><a href="admincp.php?action=smilies" hidefocus="true" target="main">表情管理</a></li>
          <li><a href="admincp.php?action=misc&operation=icon" hidefocus="true" target="main">主题图标</a></li>
          <li><a href="admincp.php?action=settings&operation=editor" hidefocus="true" target="main">编辑器设置</a></li>
          <li><a href="admincp.php?action=misc&operation=onlinelist" hidefocus="true" target="main">在线列表图标</a></li>
        </ul>
        <ul id="menu_tool" style="display: none">
          <li><a href="admincp.php?action=members&operation=newsletter" hidefocus="true" target="main">论坛通知</a></li>
          <li><a href="admincp.php?action=announce" hidefocus="true" target="main">论坛公告</a></li>
          <li><a href="admincp.php?action=tools&operation=updatecache" hidefocus="true" target="main">更新缓存</a></li>
          <li><a href="admincp.php?action=counter" hidefocus="true" target="main">更新论坛统计</a></li>
          <li><a href="admincp.php?action=jswizard" hidefocus="true" target="main">数据调用</a></li>
          <li><a href="admincp.php?action=creditwizard" hidefocus="true" target="main">积分策略向导</a></li>
          <li><a href="admincp.php?action=project" hidefocus="true" target="main">论坛方案管理</a></li>
          <li><a href="admincp.php?action=db&operation=export" hidefocus="true" target="main">数据库</a></li>
          <li><a href="admincp.php?action=logs&operation=illegal" hidefocus="true" target="main">运行记录</a></li>
          <li><a href="admincp.php?action=misc&operation=custommenu" hidefocus="true" target="main">常用操作管理</a></li>
          <li><a href="admincp.php?action=misc&operation=cron" hidefocus="true" target="main">计划任务</a></li>
          <li><a href="admincp.php?action=tools&operation=fileperms" hidefocus="true" target="main">文件权限检查</a></li>
          <li><a href="admincp.php?action=checktools&operation=filecheck" hidefocus="true" target="main">文件校验</a></li>
        </ul>
        <ul id="menu_adv" style="display: none">
          <li><a href="admincp.php?action=misc&operation=link" hidefocus="true" target="main">友情链接</a></li>
          <li><a href="admincp.php?action=misc&operation=focus" hidefocus="true" target="main">站长推荐</a></li>
          <li><a href="admincp.php?action=adv" hidefocus="true" target="main">自定义广告</a></li>
        </ul>
        <ul id="menu_uc" style="display: none">
        </ul>
        <ul id="menu_index" style="display: none">
          <li><a href="admincp.php?action=home" hidefocus="true" target="main">系统设置首页</a></li>
        </ul>
      </div></td>
    <td valign="top" width="100%" class="mask" id="mainframes">
	<iframe src="home.html" id="main" name="main" onload="mainFrame(0)" width="100%" height="100%" frameborder="0" scrolling="yes" style="overflow: visible;display:"></iframe>
	</td>
  </tr>
</table>
<div class="custombar" id="custombarpanel"> &nbsp;<span id="custombar"></span><span id="custombar_add"></span> </div>
<div id="scrolllink" style="display: none"> <span onClick="menuScroll(1)"><img src="images/admincp/scrollu.gif" /></span> <span onClick="menuScroll(2)"><img src="images/admincp/scrolld.gif" /></span> </div>
<div class="copyright">
  <p>Powered by <a href="#" target="_blank">Zhoubc</a> </p>
  <p>&copy; 2001-2009, <a href="#" target="_blank">Zhoubc Personal.</a></p>
</div>
<div id="cpmap_menu" class="custom" style="display: none">
  <div class="cside">
    <h3><span class="ctitle1"></span><a href="javascript:;" onClick="toggleMenu('tool', 'misc&operation=custommenu');hideMenu();" class="cadmin">管理</a></h3>
    <ul class="cslist" id="custommenu">
    </ul>
  </div>
  <div class="cmain" id="cmain"></div>
  <div class="cfixbd"></div>
</div>
<script type="text/JavaScript"> 
	var headers = new Array('index', 'global', 'style', 'forum', 'user', 'topic', 'extended', 'plugin', 'adv', 'tool', 'uc');
	var admincpfilename = 'admincp.php';
	var menukey = '', custombarcurrent = 0;
	function toggleMenu(key, url) {
		/*if(key == 'index' && url == 'home') {
			if(BROWSER.ie) {
				doane(event);
			}
			parent.location.href = admincpfilename + '?frames=yes';
			return false;
		}*/

		menukey = key;
		for(var k in headers) {
			if($('menu_' + headers[k])) {
				$('menu_' + headers[k]).style.display = headers[k] == key ? '' : 'none';
			}
		}
		var lis = $('topmenu').getElementsByTagName('li');
		for(var i = 0; i < lis.length; i++) {
			if(lis[i].className == 'navon') lis[i].className = '';
		}
		$('header_' + key).parentNode.parentNode.className = 'navon';
		if(url) {
			parent.mainFrame(0);
			//parent.main.location = admincpfilename + '?action=' + url;
			//Page redirect
			//alert(url);
			parent.main.location = url;
			var hrefs = $('menu_' + key).getElementsByTagName('a');
			for(var j = 0; j < hrefs.length; j++) {
				hrefs[j].className = hrefs[j].href.substr(hrefs[j].href.indexOf(admincpfilename + '?action=') + 19) == url ? 'tabon' : (hrefs[j].className == 'tabon' ? '' : hrefs[j].className);
			}
		}
		setMenuScroll();
		return false;
	}
	function setMenuScroll() {
		var obj = $('menu_' + menukey);
		var scrollh = document.body.offsetHeight - 160;
		obj.style.overflow = 'visible';
		obj.style.height = '';
		$('scrolllink').style.display = 'none';
		if(obj.offsetHeight + 150 > document.body.offsetHeight && scrollh > 0) {
			obj.style.overflow = 'hidden';
			obj.style.height = scrollh + 'px';
			$('scrolllink').style.display = '';
		}
		custombar_resize();
	}
	function menuScroll(op, e) {
		var obj = $('menu_' + menukey);
		var scrollh = document.body.offsetHeight - 160;
		if(op == 1) {
			obj.scrollTop = obj.scrollTop - scrollh;
		} else if(op == 2) {
			obj.scrollTop = obj.scrollTop + scrollh;
		} else if(op == 3) {
			if(!e) e = window.event;
			if(e.wheelDelta <= 0 || e.detail > 0) {
				obj.scrollTop = obj.scrollTop + 20;
			} else {
				obj.scrollTop = obj.scrollTop - 20;
			}
		}
	}
	function initCpMenus(menuContainerid) {
		var key = '';
		var hrefs = $(menuContainerid).getElementsByTagName('a');
		for(var i = 0; i < hrefs.length; i++) {
			if(menuContainerid == 'leftmenu' && !key && 'action=home'.indexOf(hrefs[i].href.substr(hrefs[i].href.indexOf(admincpfilename + '?action=') + 12)) != -1) {
				key = hrefs[i].parentNode.parentNode.id.substr(5);
				hrefs[i].className = 'tabon';
			}
			if(!hrefs[i].getAttribute('ajaxtarget')) hrefs[i].onclick = function() {
				if(menuContainerid != 'custommenu') {
					var lis = $(menuContainerid).getElementsByTagName('li');
					for(var k = 0; k < lis.length; k++) {
						if(lis[k].firstChild.className != 'menulink') lis[k].firstChild.className = '';
					}
					if(this.className == '') this.className = menuContainerid == 'leftmenu' ? 'tabon' : 'bold';
				}
				if(menuContainerid != 'leftmenu') {
					var hk, currentkey;
					var leftmenus = $('leftmenu').getElementsByTagName('a');
					for(var j = 0; j < leftmenus.length; j++) {
						hk = leftmenus[j].parentNode.parentNode.id.substr(5);
						if(this.href.indexOf(leftmenus[j].href) != -1) {
							leftmenus[j].className = 'tabon';
							if(hk != 'index') currentkey = hk;
						} else {
							leftmenus[j].className = '';
						}
					}
					if(currentkey) toggleMenu(currentkey);
					hideMenu();
				}
			}
		}
		return key;
	}
	var header_key = initCpMenus('leftmenu');
	toggleMenu(header_key ? header_key : 'index');
	function initCpMap() {
		var ul, hrefs, s;
		s = '<ul class="cnote"><li><img src="images/admincp/btn_map.gif" /></li><li> 按 “ ESC ” 键展开 / 关闭此菜单</li></ul><table class="cmlist" id="mapmenu"><tr>';
 
		for(var k in headers) {
			if(headers[k] != 'index' && headers[k] != 'uc') {
				s += '<td valign="top"><ul class="cmblock"><li><h4>' + $('header_' + headers[k]).innerHTML + '</h4></li>';
				ul = $('menu_' + headers[k]);
				hrefs = ul.getElementsByTagName('a');
				for(var i = 0; i < hrefs.length; i++) {
					s += '<li><a href="' + hrefs[i].href + '" target="' + hrefs[i].target + '" k="' + headers[k] + '">' + hrefs[i].innerHTML + '</a></li>';
				}
				s += '</ul></td>';
			}
		}
		s += '</tr></table>';
		return s;
	}
	$('cmain').innerHTML = initCpMap();
	initCpMenus('mapmenu');
	var cmcache = false;
	function showMap() {
		showMenu({'ctrlid':'cpmap','evt':'click', 'duration':3, 'pos':'00'});
		if(!cmcache) ajaxget(admincpfilename + '?action=misc&operation=custommenu&' + Math.random(), 'custommenu', '');
	}
	function resetEscAndF5(e) {
		e = e ? e : window.event;
		actualCode = e.keyCode ? e.keyCode : e.charCode;
		if(actualCode == 27) {
			if($('cpmap_menu').style.display == 'none') {
				showMap();
			} else {
				hideMenu();
			}
		}
		if(actualCode == 116 && parent.main) {
			if(custombarcurrent) {
				parent.$('main_' + custombarcurrent).contentWindow.location.reload();
			} else {
				parent.main.location.reload();
			}
			if(document.all) {
				e.keyCode = 0;
				e.returnValue = false;
			} else {
				e.cancelBubble = true;
				e.preventDefault();
			}
		}
	}
	function uc_left_menu(uc_menu_data) {
		var leftmenu = $('menu_uc');
		leftmenu.innerHTML = '';
		var html_str = '';
		for(var i=0;i<uc_menu_data.length;i+=2) {
			html_str += '<li><a href="'+uc_menu_data[(i+1)]+'" hidefocus="true" onclick="uc_left_switch(this)" target="main">'+uc_menu_data[i]+'</a></li>';
		}
		leftmenu.innerHTML = html_str;
		toggleMenu('uc', '');
		$('admincpnav').innerHTML = 'UCenter';
	}
	var uc_left_last = null;
	function uc_left_switch(obj) {
		if(uc_left_last) {
			uc_left_last.className = '';
		}
		obj.className = 'tabon';
		uc_left_last = obj;
	}
	function uc_modify_sid(sid) {
		$('header_uc').href = 'http://localhost/comsenz/ucenter/admin.php?m=frame&a=main&iframe=1&sid=' + sid;
	}
 
	function mainFrame(id, src) {
		var setFrame = !id ? 'main' : 'main_' + id, obj = $('mainframes').getElementsByTagName('IFRAME'), exists = 0, src = !src ? '' : src;
		for(i = 0;i < obj.length;i++) {
			if(obj[i].name == setFrame) {
				exists = 1;
			}
			obj[i].style.display = 'none';
		}
		if(!exists) {
			if(BROWSER.ie) {
				frame = document.createElement('<iframe name="' + setFrame + '" id="' + setFrame + '"></iframe>');
			} else {
				frame = document.createElement('iframe');
				frame.name = setFrame;
				frame.id = setFrame;
			}
			frame.width = '100%';
			frame.height = '100%';
			frame.frameBorder = 0;
			frame.scrolling = 'yes';
			frame.style.overflow = 'visible';
			frame.style.display = 'none';
			if(src) {
				frame.src = src;
			}
			$('mainframes').appendChild(frame);
		}
		if(id) {
			custombar_set(id);
		}
		$(setFrame).style.display = '';
		if(!src && custombarcurrent) {
			$('custombar_' + custombarcurrent).className = '';
			custombarcurrent = 0;
		}
	}
 
	function custombar_update(deleteid) {
		var extra = !deleteid ? '' : '&deleteid=' + deleteid;
		if(deleteid && $('main_' + deleteid)) {
			$('mainframes').removeChild($('main_' + deleteid));
			if(deleteid == custombarcurrent) {
				mainFrame(0);
			}
		}
		ajaxget(admincpfilename + '?action=misc&operation=custombar' + extra, 'custombar', '', '', '', function () {custombar_resize();});
	}
	function custombar_resize() {
		custombarfixw = document.body.offsetWidth - 180;
		$('custombarpanel').style.width = custombarfixw + 'px';
	}
	function custombar_scroll(op, e) {
		var obj = $('custombarpanel');
		var step = 40;
		if(op == 1) {
			obj.scrollLeft = obj.scrollLeft - step;
		} else if(op == 2) {
			obj.scrollLeft = obj.scrollLeft + step;
		} else if(op == 3) {
			if(!e) e = window.event;
			if(e.wheelDelta <= 0 || e.detail > 0) {
				obj.scrollLeft = obj.scrollLeft + step;
			} else {
				obj.scrollLeft = obj.scrollLeft - step;
			}
		}
	}
	function custombar_set(id) {
		var currentobj = $('custombar_' + custombarcurrent), obj = $('custombar_' + id);
		if(currentobj == obj) {
			obj.className = 'current';
			return;
		}
		if(currentobj) {
			currentobj.className = '';
		}
		obj.className = 'current';
		custombarcurrent = id;
	}
 
	custombar_update();
	_attachEvent(document.documentElement, 'keydown', resetEscAndF5);
	_attachEvent(window, 'resize', setMenuScroll, document);
	if(BROWSER.ie){
		$('leftmenu').onmousewheel = function(e) { menuScroll(3, e) };
		$('custombarpanel').onmousewheel = function(e) { custombar_scroll(3, e) };
	} else {
		$('leftmenu').addEventListener("DOMMouseScroll", function(e) { menuScroll(3, e) }, false);
		$('custombarpanel').addEventListener("DOMMouseScroll", function(e) { custombar_scroll(3, e) }, false);
	}
</script>
</body>
</html>