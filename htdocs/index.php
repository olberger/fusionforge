<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 3.2//EN">
<html>
	<head>
		<link rel="shortcut icon" href="favicon.ico">
		<title>FusionForge home page</title>
		<style type="text/css" media="screen,projection">/*<![CDATA[*/ @import "main.css"; /*]]>*/</style>
		<link rel="stylesheet" type="text/css" href="https://fusionforge.org/themes/css/gforge.css" />
		<link rel="stylesheet" type="text/css" href="https://fusionforge.org/themes/gforge/css/theme.css" />
		<script src="prototype.js" type="text/javascript"></script>
		<script type="text/javascript">

		/* Optional: Temporarily hide the "tabber" class so it does not "flash"
   		on the page as plain HTML. After tabber runs, the class is changed
   		to "tabberlive" and it will appear. */

		document.write('<style type="text/css">.tabber{display:none;}<\/style>');
		var tabberOptions = {
			'onClick': function(argsObj) {

			var t = argsObj.tabber; /* Tabber object */
			var i = argsObj.index; /* Which tab was clicked (0..n) */
			var div = this.tabs[i].div; /* The tab content div */

			/* Display a loading message */
			div.innerHTML = "<p>Loading...<\/p>";

			/* Fetch some html depending on which tab was clicked */
			var url = 'index-ajax-' + i + '.html';
			var pars = 'foo=bar&foo2=bar2'; /* just for example */
			var myAjax = new Ajax.Updater(div, url, {method:'get',parameters:pars});
			},

			'onLoad': function(argsObj) {
			/* Load the first tab */
			argsObj.index = 0;
			this.onClick(argsObj);
			}

		}
		</script>
		<script type="text/javascript" src="tabber.js"></script>
		<link rel="stylesheet" href="tabber.css" TYPE="text/css" MEDIA="screen">
		<script type="text/javascript">
			document.write('<style type="text/css">.tabber{display:none;}<\/style>');
		</script>
		<script>
			function bigit() {
				document.getElementById('myiframe').width = '100%, *';
				document.getElementById('myiframe').height = '100%, *';
			}
			function smallit() {
				document.getElementById('myiframe').width = '100%, *';
				document.getElementById('myiframe').height = '40%, *';
			}
		</script>
	</head>
	<body>
	<blockquote>
		<table border="0" width="100%" bgcolor=black cellspacing="0" cellpadding="0">
		<tr>
				<td class="topLeft"><a href="https://fusionforge.org/"><img src="top-logo.png" border="0" alt="" width="300" height="54" /></a></td>
				<td width="100%" background="box-grad2.png"></td>
		</tr>
		</table>
		<div id="tabber" class="tabber" >
			<div class="tabbertab" title="FusionForge"></div>
			<div class="tabbertab" title="Debian"></div>
			<div class="tabbertab" title="Ubuntu"></div>
			<div class="tabbertab" title="RedHat / CentOS"></div>
			<div class="tabbertab" title="Demo"></div>
			<div class="tabbertab" title="VMWare image"></div>
			<div class="tabbertab" title="Sources"></div>
			<div class="tabbertab" title="Debian Package Page"></div>
			<div class="tabbertab" title="PlanetForge Agregator"></div>
			<div class="tabbertab" title="Online help"></div>
		</div>
	</blockquote>
	</body>
</html>
