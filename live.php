<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

$file = (isset($_GET['file'])) ? $_GET['file'] : false;

if ($file != false && isset($_POST['editor_content'])) {
	file_put_contents('./cache/'.$file,$links_processed);
}

$editor_content = file_get_contents('./cache/'.$file);
?>

<!DOCTYPE html>
<head>
	<title>ROUNDUP LIVE EDITOR</title>
    <meta name="robots" content="noindex">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<link rel="stylesheet" type="text/css" href="//cdn.foundation5.zurb.com/foundation.css" />
	<link rel="stylesheet" type="text/css" href="style.css" />
	<style type="text/css">
	#editor_content,
	#editor_view {
		border:1px solid #ccc;
		width:100%;
		height:720px;
		overflow-y:scroll;
		overflow-x:hidden;
	}
	#editor_view { margin-left:3px; }
	#editor_content { margin-right:3px; }
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
					<li class="top-top"><a href="index.php"><strong>EDIT ANOTHER ROUNDUP</strong></a></li>
					<li class="divider"></li>
				</ul>
			</section>
			</nav>
		</div> <!-- Closes top-bar-margin -->
	</section>
	<div id="wrapper">

		<div class="headerstyle">
			<div class="row collapse" style="overflow:hidden;text-align:center;max-width:100%!important;">
				<div class="large-12 columns">
					<p style="margin:.25em auto;color:crimson;font-weight:bold;">LIVE EDIT — DO NOT FORGET TO SAVE</p>
				</div>
			</div>
		</div>
		<?php if ($file != false) { ?>
			<form id="editor" name="editor" method="post">
				<div id="admin" class="row collapse" style="overflow:hidden;max-width:100%!important;">
					<div class="large-6 columns">
						<script type="text/plain" style="display:block;" id="editor_content"><?php echo $editor_content; ?></script>
					</div>
					<div class="large-6 columns">
						<iframe src="<?php echo './cache/'.$file; ?>" id="editor_view"></iframe>
					</div>
				</div>
				<div class="row" style="overflow:hidden;max-width:90%!important;">
					<div class="large-6 large-centered columns" style="padding-top:1em;">
						<input type="submit" value="Update" class="button" style="width:100%;text-align:center;" />
					</div>
				</div>
			</form>
			<?php } else { ?>
			<div class="row collapse" style="overflow:hidden;text-align:center;max-width:100%!important;">
				<div class="large-12 columns">
					<h2 style="color:crimson;">No file content!</h2>
					<a href="./index.php">Go back and try again?</a>
				</div>
			</div>
		<?php } ?>
	</div>

	<footer>
	    <div class="row collapse" style="overflow:hidden;text-align:center;max-width:100%!important;">
			<div class="large-12 columns">
				<p style="text-align: center">Copyright &copy; 2017, The Denver Post</p>
			</div>
	    </div>
	</footer>

	<script src="http://extras.denverpost.com/foundation/js/foundation.min.js"></script>
	<script>
		$(document).foundation();
	</script>
	<script src="./ace/ace.js" charset="utf-8"></script>
	<script>
	    var editor = ace.edit("editor_content");
	    editor.setTheme("ace/theme/solarized_dark");
	    editor.getSession().setMode("ace/mode/html");
	    editor.getSession().setUseWrapMode(true);
	</script>
	<script>
        $(document).ready(function(){
        	editor.getSession().on('change', function(e) {
			    $("#editor_view").contents().find('html').html(editor.getValue());
			});
        });
	</script>
</body>
</html>