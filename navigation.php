<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script src="js/jquery.js" type="text/javascript"></script>
<script>
$(document).ready(function() {
$('#menu li').hover(function() {
$(this)
.find('ul')
.stop(true, true)
.slideDown('fast');
}, function() {
$(this)
.find('ul')
.stop(true,true)
.fadeOut('fast');
});
});
</script>
<title>Untitled Document</title>
<style>
#container {
position: relative;
}
#menu {
position: absolute;
top: 0;
right: 0;
}
#menu, #menu ul {
padding: 0;
margin: 0;
list-style: none;
}
#menu li {
float: left;
background: #FFF;
}
#menu a {
display: block;
padding: 4px;
width: 10em;
}
#menu li ul {
position: absolute;
width: 10em;
left: -999em;
}
#menu li:hover ul, #menu li ul:hover {
left:auto;
}
</style>
</head>

<body>
<ul id="menu">
<li><a href="#">What's new?</a>
<ul class="active">
<li><a href="#">Weekly specials</a></li>
<li><a href="#">Last night's pics!</a></li>
<li><a href="#">Users' comments</a></li>
</ul>
</li>
<li><a href="#">Member extras</a>
<ul>
<li><a href="#">Premium Celebrities</a></li>
<li><a href="#">24-hour Surveillance</a></li>
</ul>
</li>
</ul
></body>
</html>