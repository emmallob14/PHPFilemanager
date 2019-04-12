<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<style type="text/css">
::selection { background-color: #E13300; color: white; }
::-moz-selection { background-color: #E13300; color: white; }

h1 {
	color: #444;
	background-color: transparent;
	border-bottom: 1px solid #D0D0D0;
	font-size: 19px;
	font-weight: normal;
	margin: 0 0 14px 0;
	padding: 14px 15px 10px 15px;
}

code {
	font-family: Consolas, Monaco, Courier New, Courier, monospace;
	font-size: 12px;
	background-color: #f9f9f9;
	border: 1px solid #D0D0D0;
	color: #002166;
	display: block;
	margin: 14px 0 14px 0;
	padding: 12px 10px 12px 10px;
}

#container_div {
	margin: 40px;
	width:90%;
	margin:auto auto;
	border: 1px solid #D0D0D0;
	background:#fff!important;
	box-shadow: 0 0 8px #D0D0D0;
}

p {
	margin: 12px 15px 12px 15px;
}
</style>
<div id="container_div">
	<h1><?php echo $heading; ?></h1>
	<?php echo $message; ?>
</div>