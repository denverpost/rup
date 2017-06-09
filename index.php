<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

$errmsg = false;
if (!empty($_POST)) {
	if (isset($_POST['new_date'])) {
		$filename = $_POST['new_template'].'-'.$_POST['new_date'].'.html';
		if (file_exists($filename)) {
			$errmsg = 'That one already exists!';
		} else {
			touch('./cache/'.$filename);
		}
	} else {
		$errmsg = 'We need a date!';
	}
}

$files_list = array();
if ($handle = opendir('./cache')) {
    while (false !== ($file = readdir($handle)))
    {
        if ($file != "." && $file != ".." && strtolower(substr($file, strrpos($file, '.') + 1)) == 'html')
        {
            $files_list[] = $file;
        }
    }
    closedir($handle);
}
$files_list = array_reverse($files_list);

?>

<!DOCTYPE html>
<head>
	<title>ROUNDUP files</title>
    <meta name="robots" content="noindex">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<link rel="stylesheet" type="text/css" href="//cdn.foundation5.zurb.com/foundation.css" />
	<link rel="stylesheet" type="text/css" href="style.css" />
	<style type="text/css">

	</style>
	<link rel="icon" href="http://extras.mnginteractive.com/live/media/favIcon/dpo/favicon.ico" type="image/x-icon" />

	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
</head>

<body>
	<section id="header">

		<!-- NAVIGATION BAR -->
		<div id="top-bar-margin" class="sticky fixed">
			<nav class="top-bar" data-topbar="" role="navigation">
				<ul class="title-area">
					<li class="name">
						<a href="http://denverpost.com"><img src="http://extras.denverpost.com/candidate-qa/denver-2015/images/dp-logo-white.png" alt="The Denver Post logo" class="nav-logo"></a>
					</li>
				</ul>
				<section class="top-bar-section">
				<ul class="right">
					<li class="divider"></li>
				</ul>
			</section>
			</nav>
		</div> <!-- Closes top-bar-margin -->
	</section>
	<div id="wrapper">

		<div class="headerstyle">
			<div class="row">
				<div class="large-12 columns">
					<h1>Edit or create a newsletter</h1>
					<p>Select from the list to edit an existing newsletter.</p>
					<p>Select a template and input the date to create a new one. Then click the created link to edit.</p>
				</div>
			</div>
		</div>
		<div id="admin" class="row">
			<form id="newroundup" name="newroundup" method="post">
				<div class="large-10 large-centered columns">
					<fieldset>
						<legend> Create a new newsletter </legend>
							<div class="row">
								<div class="large-4 columns">
									<label for="new_date">Date:</label>
									<input type="text" name="new_date" placeholder="YYYYMMDD">
								</div>
								<div class="large-5 columns">
									<label for="new_template">Template:</label>
									<input type="radio" name="new_template" value="roundup" checked> Mile High Roundup
								</div>
								<div class="large-3 columns">
									<input type="submit" value="CREATE" class="button">
								</div>
							</div>
						</fieldset>
					<fieldset>
						<legend> <?php echo count($files_list); ?> files </legend>
							<?php if (!count($files_list)>0) { ?>
								<div class="row">
									<div class="large-12 columns text-center">
										<strong style="color:crimson">No source added!</strong>
									</div>
								</div>
							<?php } else { ?>
								<ul>
								<?php foreach($files_list as $file) { ?>
									<li><a href="edit.php?file=<?php echo $file; ?>"><?php echo $file; ?></a></li>
								<?php } ?>
								</ul>
							<?php } ?>
					</fieldset>
				</div>
			</form>
		</div>

		<footer>
		    <div class="row">
		      <div class="large-12 medium-12 small-12 columns">
		      <p style="text-align: right">Copyright &copy; 2017, The Denver Post</p>
		      </div>
		    </div>
		</footer>

	<script src="http://extras.denverpost.com/foundation/js/foundation.min.js"></script>
	<script>
		$(document).foundation();
	</script>
</body>
</html>