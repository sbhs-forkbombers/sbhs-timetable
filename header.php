<!DOCTYPE html>
<!--
    Copyright (C) 2014  James Ye  Simon Shields

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
-->
<html><!--<html manifest='timetable.appcache'>-->
<head>
	<script>window.applicationCache.onerror = function(e) { window.lasterror = e }</script>
<meta charset="UTF-8" />
<meta name="google-site-verification" content="5vw2xj7xVh3ElQbRgNwk4G3oXc-wTQYJL5j9e9oMAjQ" />
<meta property="og:title" content="SBHS Timetable" />
<title>SBHS Timetable</title>
<meta property="og:image" content="http://sbhsbelltimes.tk/assets/logo_big.jpg" />
<meta name="keywords" content="SBHS, Timetable, Bell, Times, Belltimes, SHS, Sydney Boys, sbhstimetable, sbhsbelltimes, sbhstimetable.tk" />
<meta property="og:description" content="The fastest countdown to the bell for Sydney Boys High. The place to go to know what's next, and how long you've got left, or when that teacher you woe didn't read out today's notices..." />
<meta name="description" content="The fastest countdown to the bell for Sydney Boys High. The place to go to know what's next, and how long you've got left, or when that teacher you woe didn't read out today's notices..." />
<meta name="viewport" content="width=device-width, user-scalable=no" />
<meta name="mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black" />
<link rel="shortcut icon" sizes="196x196" href="/icon.png" />
<link rel="apple-touch-icon" sizes="196x196" href="/icon.png" />
<link rel="apple-touch-startup-image" href="/startup.png" />
<!--[if IE]><meta http-equiv="X-UA-Compatible" content="IE=edge" /><![endif]-->
<link rel="stylesheet" href="/style/common.css" />
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/modernizr/2.7.1/modernizr.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/webfont/1.3.0/webfont.js"></script>
<script defer src="//cdnjs.cloudflare.com/ajax/libs/datejs/1.0/date.min.js"></script>
<?php if (isset($EXTRA_STYLESHEETS)) {
	echo $EXTRA_STYLESHEETS;
}?>
<script defer src="//cdnjs.cloudflare.com/ajax/libs/spin.js/1.3.3/spin.min.js" type="application/javascript"></script>
	<script>
<?php include "./script/yepnope.min.js"; /* we cannot use cdn because only yepnope in the gh repo contains certain fixes for older webkit versions */ ?>
window.actualMobile = <?php echo (isset($_REQUEST["mobile"]) ? "true" : "false")?>;
(function(a,b){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |mobile safari|maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4)))window.actualMobile=true})(navigator.userAgent||navigator.vendor||window.opera,'http://detectmobilebrowser.com/mobile');
yepnope([{
	test: window.actualMobile,
	yep: "/style/mobile.css",
}]);
WebFont.load({
google: {
families: ['Roboto:400,100,700', 'Roboto Condensed', 'Roboto Slab:400,700']
}
});
 
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

    ga('create', 'UA-47488887-1', 'sbhstimetable.tk');
	ga('send', 'pageview');
/*
	$(document).ready(function() { 
		$(document).on("touchmove", function(e) {
			if (!e.target.classList.contains('long-slideout') && !e.target.classList.contains('slideout'))
				e.preventDefault()
		}); 
	});*/
	// http://stackoverflow.com/a/8173161
(function(a,b,c){if(c in b&&b[c]){var d,e=a.location,f=/^(a|html)$/i;a.addEventListener("click",function(a){d=a.target;while(!f.test(d.nodeName))d=d.parentNode;"href"in d&&(d.href.indexOf("http")||~d.href.indexOf(e.host))&&(a.preventDefault(),e.href=d.href)},!1)}})(document,window.navigator,"standalone")
</script>
