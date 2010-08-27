<?php
  if (!defined ('BASEPATH')) die ('No direct file access allowed');
  header("HTTP/1.1 403 Forbidden");
?>

<html>
<head>
<?php
  echo '<link rel="stylesheet" href="/css/errors.css'.'" />';
?>
<title>404 Page Not Found</title>
</head>
<body>
	<div id="content">
		<h1><?php echo $heading; ?></h1>
                <?php
                  if (!empty ($message))
                   echo $message;
				  else
				  echo 'The page you requested was not found.';
                ?>
	</div>
</body>
</html>

<?php exit;?>