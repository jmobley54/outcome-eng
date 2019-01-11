<?php
/*
Plugin Name: Redirect List
Plugin URI: https://membershipworks.com/redirect-list-wordpress/
Description: Redirect List
Version: 1.8
Author: MembershipWorks
Author URI: https://membershipworks.com
Text Domain: redirect-list
License: GPL2
*/

/*  Copyright 2013-2016  SOURCEFOUND INC.  (email : info@sourcefound.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if (is_admin()) {
	add_action('admin_menu','sf_red_admin_menu');
	add_action('admin_init','sf_red_admin_init');
}

function sf_red_admin_init() {
	register_setting('sf_red_admin_group','sf_red','sf_red_validate');
}

function sf_red_admin_menu() {
	add_options_page('Redirect Settings','Redirects','manage_options','sf_red_options','sf_red_options');
}

function sf_red_options() {
	load_plugin_textdomain('redirect-list',false,basename(dirname(__FILE__)));
	if (!current_user_can('manage_options'))
		wp_die(__('You do not have sufficient permissions to access this page.','redirect-list'));
	echo '<div class="wrap"><h2>Redirect List</h2>'
		.'<form action="options.php" method="post">'
		.'<input id="sf_red" type="hidden" name="sf_red">';
	settings_fields("sf_red_admin_group");
	$red=get_option('sf_red');
	echo '</form>'
		.'<div id="redirect-list">';
	for ($i=0;isset($red[$i]);$i++)
		echo '<div data-idx="'.$i.'"><input type="text" name="sf_red['.$i.'][0]" value="'.$red[$i][0].'" style="width:300px;"><span> &raquo; </span><input type="text" name="sf_red['.$i.'][1]" value="'.$red[$i][1].'" style="width:300px;"><select name="sf_red['.$i.'][2]"><option value="1"'.($red[$i][2]=='1'?' selected="selected"':'').'>'.__('301 Moved Permanently','redirect-list').'</option><option value="2"'.($red[$i][2]=='2'?' selected="selected"':'').'>'.__('302 Moved Temporarily','redirect-list').'</option><option value="7"'.($red[$i][2]=='7'?' selected="selected"':'').'>'.__('307 Temporary Redirect','redirect-list').'</option><select></div>';
	echo '<div data-idx="'.$i.'"><input type="text" name="sf_red['.$i.'][0]" onchange="if (this.value) sf_red_add();" placeholder="'.__('url-to-match','redirect-list').'" style="width:300px;"><span> &raquo; </span><input type="text" name="sf_red['.$i.'][1]" placeholder="destination-url" style="width:300px;"><select name="sf_red['.$i.'][2]"><option value="1">301 Moved Permanently</option><option value="2">302 Moved Temporarily</option><option value="7">307 Temporary Redirect</option><select></div>'
		.'</div>'
		.'<p class="submit"><input type="submit" name="submit" id="submit" class="button-primary" value="'.__('Save Changes','redirect-list').'" onclick="sf_red_submit()"></p>'
		.'<div>'.__('Redirect when there are no GET query specified','redirect-list').': "'.__('url-to-match','redirect-list').'"<br>'.__('Ignore any GET parameters','redirect-list').': "'.__('url-to-match','redirect-list').'?"<br>'.__('GET parameter should exist but value does not matter','redirect-list').': "'.__('url-to-match','redirect-list').'?'.__('get-parameter','redirect-list').'"<br>'.__('GET parameter should exist and value must match','redirect-list').': "'.__('url-to-match','redirect-list').'?'.__('get-parameter','redirect-list').'='.__('value-to-match','redirect-list').'"<br>'.__('Empty url to delete','redirect-list').'</div>'
		.'<p class="submit"><button class="button-secondary" onclick="sf_red_exp()">'.__('Export CSV','redirect-list').'</button> <button class="button-secondary" onclick="sf_red_inp()">'.__('Import CSV','redirect-list').'</button></p>'
		.'<textarea id="SFredtxt" style="display:none;width:100%;height:200px">'.__('Copy and paste your CSV here and click Import CSV again to populate list. Then click Save Changes','redirect-list').'</textarea>'
		.'<script>'
		.'function sf_red_submit(){'
			.'var i,a=[],x,l=document.getElementById("redirect-list").childNodes;'
			.'for(i=0;i<l.length;i++)if(l[i].childNodes[0].value){x=[l[i].childNodes[0].value,l[i].childNodes[2].value,l[i].childNodes[3].value];a.push(x);}'
			.'document.getElementById("sf_red").value=JSON.stringify(a);'
			.'document.getElementById("sf_red").parentNode.submit();'
		.'}'
		.'function sf_red_add(){'
			.'var i,n,l=document.getElementById("redirect-list");'
			.'if (!l.lastChild.childNodes[0].value) return l.lastChild;'
			.'l.appendChild(n=l.lastChild.cloneNode(true));'
			.'n.setAttribute("data-idx",i=parseInt(n.getAttribute("data-idx"))+1);'
			.'n.childNodes[0].name="sf_red["+i+"][0]";n.childNodes[0].value="";'
			.'n.childNodes[2].name="sf_red["+i+"][1]";n.childNodes[2].value="";'
			.'n.childNodes[3].name="sf_red["+i+"][2]";'
			.'return n;'
		.'}'
		.'function sf_red_exp(){'
			.'var t=[],n,l=document.getElementById("redirect-list");'
			.'for(n=l.firstChild;n;n=n.nextSibling) if (n.childNodes[0].value)'
				.'t.push(\'"\'+n.childNodes[0].value+\'","\'+n.childNodes[2].value+\'",30\'+n.childNodes[3].value);'
			.'document.getElementById("SFredtxt").innerHTML=t.join("\n");'
			.'document.getElementById("SFredtxt").style.display="";'
			.'document.getElementById("SFredtxt").select();'
		.'}'
		.'function sf_red_inp(){'
			.'if(document.getElementById("SFredtxt").style.display){document.getElementById("SFredtxt").style.display="";return;}'
			.'var t=document.getElementById("SFredtxt").value.split("\n"),i,n,l=document.getElementById("redirect-list");'
			.'for(;l.childNodes.length>1;)l.removeChild(l.lastChild);'
			.'l.firstChild.childNodes[0].value=l.firstChild.childNodes[2].value="";'
			.'for(i=0,n=l.firstChild;i<t.length;i++) if (t[i]) {'
				.'t[i]=t[i].split(",");if (t[i].length<3) continue;'
				.'n.childNodes[0].value=t[i][0].substr(0,1)==\'"\'?t[i][0].slice(1,-1):t[i][0];'
				.'n.childNodes[2].value=t[i][1].substr(0,1)==\'"\'?t[i][1].slice(1,-1):t[i][1];'
				.'n.childNodes[3].value=t[i][2].substr(0,1)==\'"\'?t[i][2].substr(3,1):t[i][2].substr(2,1);'
				.'n=sf_red_add();'
			.'}'
		.'}'
		.'</script></div>';
}

function sf_red_validate($in) {
	$out=array();
	$url=get_site_url();
	$in=json_decode($in,true);
	if (is_array($in)) for ($i=0;$i<count($in);$i++) if (is_array($in[$i])&&$in[$i][0]) {
		$tmp=strpos($in[$i][0],substr(strstr($url,'//'),2));
		if ($tmp!==false) $in[$i][0]=substr($in[$i][0],$tmp+strlen(strstr($url,'//'))-2);
		if (substr($in[$i][0],0,1)!='/') $in[$i][0]='/'.$in[$i][0];
		$tmp=strpos($in[$i][1],'//');
		if ($tmp===false) $in[$i][1]=$url.(strpos($in[$i][1],'/')===0?'':'/').$in[$i][1];
		else if ($tmp===0) $in[$i][1]='http:'.$in[$i][1];
		if ($in[$i][1]==$url.'/') $in[$i][1]=$url;
		$out[]=$in[$i];
	}
	return $out;
}

function sf_red_go() {
	$red=get_option('sf_red');
	if (!empty($_SERVER['REQUEST_URI']))
		$uri=$_SERVER['REQUEST_URI'];
	else if (!empty($_SERVER['HTTP_X_ORIGINAL_URL'])) // IIS mod-rewrite
		$uri=$_SERVER['HTTP_X_ORIGINAL_URL'];
	else if (!empty($_SERVER['HTTP_X_REWRITE_URL'])) // IIS isapi_rewrite
		$uri=$_SERVER['HTTP_X_REWRITE_URL'];
	else {
		if (isset($_SERVER['PATH_INFO'])&&isset($_SERVER['SCRIPT_NAME']))
			$uri=$_SERVER['SCRIPT_NAME'].($_SERVER['PATH_INFO']==$_SERVER['SCRIPT_NAME']?'':$_SERVER['PATH_INFO']);
		else
			$uri=$_SERVER['PHP_SELF'];
		if (!empty($_SERVER['QUERY_STRING']))
			$uri.='?'.$_SERVER['QUERY_STRING'];
	}
	if (strpos($uri,'/index.php')===0) $uri=substr($uri,10);
	if (!$uri) $uri='/'; else if (substr($uri,0,1)!='/') $uri='/'.$uri;
	$qry=strpos($uri,'?');
	$url=($qry!==false?substr($uri,0,$qry):$uri);
	if (strlen($url)>1&&substr($url,-1)=='/') $url=substr($url,0,strlen($url)-1);
	for ($i=0;isset($red[$i]);$i++) {
		$tmp=explode('?',$red[$i][0]);
		if (strlen($tmp[0])>1&&substr($tmp[0],-1)=='/') $tmp[0]=substr($tmp[0],0,strlen($tmp[0])-1);
		if ($url==$tmp[0]) {
			$exe=true;
			if (count($tmp)>1) {
				$tmp=explode('&',$tmp[1]);
				foreach ($tmp as $x) if ($x) {
					$x=explode('=',$x);
					if (!isset($_GET[$x[0]])||(count($x)>1&&urldecode($_GET[$x[0]])!=urldecode($x[1]))) $exe=false;
				}
			} else if ($qry!==false)
				$exe=false;
			if ($exe) {
				wp_redirect($red[$i][1],300+intval($red[$i][2])); 
				exit;
			}
		}
	}
}
add_action('plugins_loaded','sf_red_go');

?>