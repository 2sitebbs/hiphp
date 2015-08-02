<?php
$viewPath = dirname(__FILE__);
include_once "{$viewPath}/before_body.php"; ?>
<div class="header">
    Welcome to use <a href="https://github.com/2sitebbs/hiphp" target="_blank">HiPHP</a> (a lightweight php framework).
</div>
<div class="content clearfix">
    <?php include_once "{$viewPath}/../{$viewGroup}/{$viewName}.php"; ?>
</div>
<div class="footer">
    Copyright @copyright 2014 - 2015 by <a href="https://github.com/2sitebbs/hiphp" target="_blank">HiPHP</a>.
</div>
<?php include_once "{$viewPath}/after_body.php"; ?>