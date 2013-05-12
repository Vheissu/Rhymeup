<!doctype html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
	<base href="{base_url()}">
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

	<title>{$site.title}</title>
	<meta name="description" content="Find free music via Youtube">
	<meta name="viewport" content="width=device-width">

	<link rel="stylesheet/less" href="assets/css/rhymeup.less">
	<script src="assets/js/less-1.3.0.min.js"></script>

	<link rel="stylesheet" href="assets/css/jquery-ui-1.8.18.custom.css">
	<link rel="stylesheet" href="assets/css/attention_box.css">
	
	<!-- 
	<link rel="stylesheet" href="less/style.css">
	 -->

	<script src="assets/js/modernizr-2.5.3-respond-1.1.0.min.js"></script>
</head>
<body>

    {block 'before_header'}{/block}
    <div id="header">
        {block 'before_inner_header'}{/block}
        {include 'includes/header.tpl'}
        {block 'after_inner_header'}{/block}
    </div>
    {block 'after_header'}{/block}

    <div id="wrapper" class="container">
    {block 'main'}{/block}
    </div>

    {include 'includes/player.tpl'}

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script>window.jQuery || document.write('<script src="assets/js/jquery-1.7.2.min.js"><\/script>')</script>

<script src="assets/js/jquery-ui-1.8.18.custom.min.js"></script>
<script src="assets/js/attention_box-min.js"></script>
<script src="assets/js/swfobject.js"></script>

<script src="assets/js/main.js"></script>
</body>
</html>
