<?php
$jsFile = dirname(__FILE__) . "/../../js/demo.js";
$jsVersion = Util::getFilesModifyTime($jsFile);
?>
<script src="<?php echo AppUtil::getJSUrl("demo.js?v={$jsVersion}"); ?>"></script>

<?php if (isset($_SERVER['APP_ENV']) && $_SERVER['APP_ENV'] == 'production') {  //线上环境才引入 ?>
<span style="display:none">

</span>
<?php } ?>
</body>
</html>