<?phpsession_name("EspacePerso");session_start();require_once "config.php";$email = urldecode($_GET["e"]);?><html><head>	<!--<script type="text/javascript" src="../../js/ckeditor/ckeditor.js"></script>--></head><body onload="init();" onunload="quit();"><div class="formA">	<ul>		<fieldset>			<legend>R&eacute;daction d'un mail</legend>			<div style="height:120px;width:100%;">				<input type="text" value="<?php echo $email; ?>" style="width:100%;text-indent:24px;background-image:url('img/to.png');background-repeat:no-repeat;" />				<input type="text" value="" style="width:100%;text-indent:24px;background-image:url('img/to.png');background-repeat:no-repeat;" />			</div>			<li><input type="text" value="" style="width:100%;text-indent:35px;background-image:url('img/obj.png');background-repeat:no-repeat;" /></li>			<li><textarea id="corps" style="width:100%;height:390px;"></textarea></li>		</fieldset>	</ul></div></body></html>