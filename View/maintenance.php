<?php
  if (!defined ('BASEPATH')) die ('No direct file access allowed');
  header("HTTP/1.1 500 Internal error");
?>

<html>
<head>
<?php
  echo '<link rel="stylesheet" href="'.CORECSSPATH.'errors.css'.'" />';
?>
<title>This website is currently undergoing maintenance</title>
<center>
	<fieldset>
		<legend><b>This website is currently undergoing maintenance</b></legend>
		<br />
		We are sorry to inform you about downtime for this system, The system administrator is currently working on the system wich will hopefully be functioning soon again.
	</fieldset>
</center>