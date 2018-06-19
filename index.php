<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

$errmsg = $all = false;
if (!empty($_POST)) {
	if (isset($_POST['new_date'])) {
		$filename = $_POST['new_template'].'-'.$_POST['new_date'].'.html';
		if (file_exists('./cache/'.$filename)) {
			$errmsg = 'That one already exists!';
		} else {
			touch('./cache/'.$filename);
		}
	} else {
		$errmsg = 'We need a date!';
	}
} else {
	$list = (isset($_GET['list'])) ? $_GET['list'] : false;
	$all = ($list == 'all') ? true : false;
}

$files_list = array();
$files_types = array();
if ($handle = opendir('./cache')) {
    while (false !== ($file = readdir($handle))) {
        if ($file != "." && $file != ".." && strtolower(substr($file, strrpos($file, '.') + 1)) == 'html') {
        	if ($all) {
		    	$files_list[] = $file;
		    } else {
        		$fileparts = explode('-', $file);
				$nl_type = $fileparts[0];
				if (isset($files_types[$nl_type])) {
					$files_types[$nl_type]++;
				} else {
					$files_types[$nl_type] = 1;
				}
				if ($files_types[$nl_type] <= 5) {
		            $files_list[] = $file;
		        }
		    } 
        }
    }
    closedir($handle);
}
sort($files_list, SORT_NATURAL);
$files_list = array_reverse($files_list);

?>

<!DOCTYPE html>
<head>
	<title>ROUNDUP files</title>
    <meta name="robots" content="noindex">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/foundation/5.5.3/css/foundation.min.css" />
	<link rel="stylesheet" type="text/css" href="style.css" />
	<style type="text/css">
	th { font-size:1.2em; }
	</style>
	<link rel="shortcut icon" href="//plus.denverpost.com/favicon.ico" type="image/x-icon" />

	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
</head>

<body>
	<section id="header">

		<!-- NAVIGATION BAR -->
		<div id="top-bar-margin" class="sticky fixed">
			<nav class="top-bar" data-topbar="" role="navigation">
				<ul class="title-area">
					<li class="name">
						<a href="https://denverpost.com"><img src="//extras.denverpost.com/candidate-qa/denver-2015/images/dp-logo-white.png" alt="The Denver Post logo" class="nav-logo"></a>
					</li>
				</ul>
				<section class="top-bar-section">
				<ul class="right">
					<li class="divider"></li>
					<li class="top-top"><a href="convert.php"><strong>WORDPRESS NL CONVERTER</strong></a></li>
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
					<p>Select from the list to edit an existing newsletter, or create a new Roundup right here.</p>
					<p>Select a template and input the date to create a new one. Then click the created link to edit.</p>
				</div>
			</div>
		</div>
		<div id="admin" class="row">
			<form id="newroundup" name="newroundup" method="post">
				<div class="large-10 large-centered columns">
					<fieldset>
						<legend>&nbsp;Create a new Roundup (for other newsletters, use the <a href="./convert.php">Wordpress Converter</a>)&nbsp;</legend>
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
						<legend>&nbsp;<?php echo count($files_list); ?> recent newsletters&nbsp;</legend>
							<?php if (!count($files_list)>0) { ?>
								<div class="row">
									<div class="large-12 columns text-center">
										<strong style="color:crimson">No source added!</strong>
									</div>
								</div>
							<?php } else { ?>
								<table style="width:100%;">
									<thead>
										<tr>
											<th width="*">Filename</th>
											<th width="20%" style="text-align:center;">Initial Setup</th>
											<th width="20%" style="text-align:center;">Live Editor</th>
										</tr>
									</thead>
									<tbody>
									<?php foreach($files_list as $file) { ?>
										<tr>
											<td><strong style="font-size:1.2em;"><?php echo $file; ?></strong></td>
											<td style="text-align:center;"><a href="edit.php?file=<?php echo $file; ?>"><img style="width:2.25em;" src="./img/icon-setup.png" alt="Edit Settings" /></a></td>
											<td style="text-align:center;"><a href="live.php?file=<?php echo $file; ?>"><img style="width:2.25em;" src="./img/icon-edit.png" alt="Live Edit" /></a></td>
										</tr>
									<?php } ?>
									</tbody>
								</table>
							<?php } ?>
					</fieldset>
					<?php if ($all) { ?>
					<fieldset>
						<legend>&nbsp;Too many files?&nbsp;</legend>
						<a href="./index.php?list=none" style="width:100%;" class="button">GO BACK TO THE SHOTER LIST OF NEWSLETTERS</a>
					</fieldset>
					<?php } else { ?>
					<fieldset>
						<legend>&nbsp;Looking for older files?&nbsp;</legend>
						<a href="./index.php?list=all" style="width:100%;" class="button">GET THE COMPLETE LIST OF CACHED NEWSLETTERS</a>
					</fieldset>
				<?php } ?>
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

	<script src="//extras.denverpost.com/foundation/js/foundation.min.js"></script>
	<script>
		$(document).foundation();
	</script>
</body>
</html>