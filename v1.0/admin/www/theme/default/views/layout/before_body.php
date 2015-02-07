<!DocType html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php Util::echoMsg('pageTitle'); ?></title>
<meta name="keywords" content="<?php Util::echoMsg('pageKeywords'); ?>">
<meta name="description" content="<?php Util::echoMsg('pageDescription'); ?>">
<?php
$cssFile = dirname(__FILE__) . "/../../css/demo.css";
$cssVersion = Util::getFilesModifyTime($cssFile);
?>
<link rel="stylesheet" type="text/css" href="<?php echo AppUtil::getCSSUrl("demo.css?v={$cssVersion}"); ?>">
</head>

<body class="<?php Util::echoMsg('pageName'); ?>">