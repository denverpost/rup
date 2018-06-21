<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once './wp-funcs.php';
require_once './variables.php';

$bylines = array(
	'none' => array('No byline',false,false),
	'dschneider' => array('Daniel J. Schneider', 'dschneider@denverpost.com', 'schneidan'),
	'acrawford' => array('Adrian Crawford', 'acrawford@denverpost.com', 'Crawf33'),
	'jrubino' => array('Joe Rubino', 'jrubino@denverpost.com', 'RubinoJC'),
	'jnguyen' => array('Joe Nguyen', 'jnguyen@denverpost.com', 'JoeNguyen'),
	'ehernandez' => array('Elizabeth Hernandez', 'ehernandez@denverpost.com', 'ehernandez'),
	'jpaul' => array('Jesse Paul', 'jpaul@denverpost.com', 'JesseAPaul'),
	'sgrant' => array('Sara Grant', 'sgrant@denverpost.com', 'ItsMeSaraG'),
);

$byline_raw = '<p style="-ms-text-size-adjust:100%%;-webkit-text-size-adjust:100%%;text-transform:uppercase;font-weight:700;color:maroon;mso-line-height-rule:exactly;font-size:14px;line-height:1.5em;">By %1$s
                    <br /><a href="mailto:%2$s?subject=Newsletter%%20Feedback" title="Email %1$s @ The Denver Post" style="-ms-text-size-adjust:100%%;-webkit-text-size-adjust:100%%;border-bottom-style:none;position:relative;margin-top:.67em;margin-bottom:.67em;margin-right:0;margin-left:0;padding-top:0;padding-bottom:0;padding-right:0;padding-left:0;text-decoration:none;color:#1670A3!important;">%2$s</a> / <a href="https://twitter.com/%3$s" title="@%3$s on Twitter" style="-ms-text-size-adjust:100%%;-webkit-text-size-adjust:100%%;border-bottom-style:none;position:relative;margin-top:.67em;margin-bottom:.67em;margin-right:0;margin-left:0;padding-top:0;padding-bottom:0;padding-right:0;padding-left:0;text-decoration:none;color:#1670A3!important;">@%3$s</a></p>';

$author = $newsletter_date = $filename = $template = $input_text = $finished_html = false;

function add_link_styles($inputstring, $template_style) {
	if (preg_match('/<a.+?href="(.+?)">/', $inputstring)) {
		return preg_replace('/<a.+?href="(.+?)">/', '<a href="$1" style="'.$template_style.'">', $inputstring);
	} else {
		return $inputstring;
	}
}

function format_images_without_captions($inputstring) {
	if (preg_match('/^<img\s.*?\bsrc="(.*?)".*?>/si', $inputstring)) {
		return preg_replace('/^<img\s.*?\bsrc="(.*?)".*?>/si', '<center>'."\r\n".'<p style="text-align: center;margin:0;padding:0;">'."\r\n".'<img src="$1" aria-hidden="true" width="100%" border="0" style="height: auto; background: #ffffff; font-family: sans-serif; width:100%;font-size: 15px; line-height: 100%; color: #555555;display:block;">'."\r\n".'</p>'."\r\n".'</center>', $inputstring);
	} else {
		return $inputstring;
	}
}

function format_images_with_captions($inputstring) {
	if (preg_match('/^\[cap.*\].+?src="(.+?)".+?>(.+?)\[\/cap.*\]$/', $inputstring)) {
		return preg_replace('/^\[cap.*\].+?src="(.+?)".+?>(.+?)\[\/cap.*\]$/', '<center>'."\r\n".'<p style="text-align: center;margin:0;padding:0;">'."\r\n".'<img src="$1" aria-hidden="true" width="100%" border="0" style="height: auto; background: #ffffff; font-family: sans-serif; width:100%;font-size: 15px; line-height: 100%; color: #555555;display:block;">'."\r\n".'</p>'."\r\n".'</center>'."\r\n".'<p style="font-size:0.85em;color:#595959;margin-top:.5em;font-style:italic;">$2</p>', $inputstring);
	} else {
		return $inputstring;
	}
}

function go_through_grafs($inputstring) {
	$out = array();
	foreach (preg_split("/((\r?\n)|(\r\n?))/", $inputstring) as $line) {
		$out[] = trim(format_images_with_captions(format_images_without_captions($line)));
	}
	return implode("\r\n", $out);
}

function add_ads($inputstring,$template,$template_ads) {
	$heading_pattern = (in_array($template, array('know','outdoors'))) ? "/<h3>(.+?)<\/h3>/" : "/<h2>(.+?)<\/h2>/";
	$ad_one = $template_ads['ad_one'];
	$ad_two = $template_ads['ad_two'];
	$place_one = ($template == 'spot') ? 1 : 2;
	$place_two = ($template == 'spot') ? 2 : 4;
	$counter = 0;
	$inputstring = preg_replace_callback($heading_pattern, function ($m) use (&$counter,&$place_one,&$place_two,&$ad_one,&$ad_two) {
		$counter++;
		if ($counter == $place_one) {
			return $ad_one."\n".$m[0];
		}
		if ($counter == $place_two) {
			return $ad_two."\n".$m[0];
		}
		return $m[0];
	}, $inputstring);
	return $inputstring;
}


if (!empty($_POST)) {
	$input_text = isset($_POST['input_text']) ? $_POST['input_text'] : false;
	$template = isset($_POST['template']) ? $_POST['template'] : false;
	$author = isset($_POST['author']) ? $_POST['author'] : false;
	if (isset($_POST['new_date']) && $_POST['new_date'] != '') {
		$newsletter_date = $_POST['new_date'];
	} else {
		$newsletter_date = date("Ymd");
	}
	$filename = $template.'-'.$newsletter_date.'.html';
	if (!file_exists('./cache/'.$filename)) {
		touch('./cache/'.$filename);
	}

	$byline_text = ( $bylines[$author] && $bylines[$author][2] !== false ) ? sprintf(
		$byline_raw,
		$bylines[$author][0],
		$bylines[$author][1],
		$bylines[$author][2]
	) : '';

	$template_raw = ($template) ? file_get_contents('./template-' . $template . '.html') : false;

	if ($input_text != false) {

		// Convert pipes to endashes
		$finished_html = str_replace('--', '–', $input_text);

		$finished_html = go_through_grafs($finished_html);
		$finished_html = wpautop($finished_html);
		$finished_html = add_link_styles($finished_html,$templates[$template]['link_style']);
		$finished_html = add_ads($finished_html,$template,$templates[$template]);

		$spacer_div = "<span style=\"display:block;height:1em;width:100%;\"></span>";
	}

	if ($template && $finished_html) {
		$template_raw = preg_replace('/<!--{{BYLINE}}-->(.*?)<!--{{\/BYLINE}}-->/', '<!--{{BYLINE}}-->' . $byline_text . '<!--{{/BYLINE}}-->', $template_raw);
		$template_raw = preg_replace('/<!--{{CONTENT}}-->(.*?)<!--{{\/CONTENT}}-->/', '<!--{{CONTENT}}-->' . "\n\n" . $finished_html . "\n\n" . '<!--{{/CONTENT}}-->', $template_raw);
		$finished_html = $template_raw;
	}
	if ($finished_html != false) { file_put_contents('./cache/'.$filename, $finished_html); }
}

?>

<!DOCTYPE html>
<head>
	<title>Wordpress-to-HTML Email newsletter converter</title>
    <meta name="robots" content="noindex">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/foundation/5.5.3/css/foundation.min.css" />
	<link rel="stylesheet" type="text/css" href="style.css" />
	<style type="text/css">

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
					<li class="top-top"><a href="index.php"><strong>EDIT ANOTHER NEWSLETTER</strong></a></li>
					<li class="divider"></li>
				</ul>
			</section>
			</nav>
		</div> <!-- Closes top-bar-margin -->
	</section>
	<div id="wrapper">

		<div class="headerstyle">
			<div class="row">
				<div class="large-9 columns">
					<h1>Convert Wordpress body to newsletter HTML</h1>
					<p>Converts the body copy from a Wordpress post into formatted email newsletter HTML suitable for pasting into Listrak.</p>
					<p style="color:darkred;font-style:italic;font-weight:bold;">NOTE: If a newsletter for the date you input already exists, it will be overwritten!</p>
				</div>
			</div>
		</div>
		<div id="admin" class="row">
			<form id="newletterraw" name="newletterraw" method="post">
				<div class="large-12 columns">
					<fieldset>
						<legend> Wordpress Source </legend>
						<label for="input_text">Paste body copy from Wordpress here:</label>
						<textarea id="input_text" name="input_text" style="width:100%;height:200px;"><?php echo $input_text; ?></textarea>
					</fieldset>

					<fieldset>
						<legend> Options </legend>
						<div class="row">
							<div class="large-4 columns">
								<fieldset>
									<legend> Newsletter Send Date </legend>
									<input type="text" name="new_date" placeholder="YYYYMMDD">
								</div>
								<div class="large-4 columns">
								<fieldset>
									<legend> Choose Template </legend>
									<select name="template">
										<?php
										foreach ($templates as $key => $values) {
											$selected = ($template && $key == $template) ? ' selected' : '';
											echo '<option value="' . $key . '"' . $selected . '>' . $values['name'] . '</option>';
										} 
										unset($selected);
										?>
									</select>
								</fieldset>
							</div>
							<div class="large-4 columns">
								<fieldset>
									<legend> Choose Author </legend>
									<select name="author">
										<?php
										foreach ($bylines as $key => $values) {
											$selected = ($author && $key == $author) ? ' selected' : '';
											echo '<option value="' . $key . '"' . $selected . '>' . $values[0] . '</option>';
										}
										unset($selected);
										?>
									</select>
								</fieldset>
							</div>
						</div>
					</fieldset>
				</div>
				<div class="large-12 columns">
					<fieldset>
						<input type="submit" class="button" style="width:100%;" value="PROCESS WORDPRESS INTO HTML" />
					</fieldset>
					<fieldset>
						<legend> Processed output </legend>
						<label for="output_text">Here is the fully-processed newsletter HTML:</label>
						<textarea id="output_text" name="output_text" style="width:100%;height:200px;"><?php echo $finished_html; ?></textarea>
					</fieldset>
					<fieldset>
						<a href="live.php?file=<?php echo ($filename == false) ? '' : $filename; ?>" style="width:100%;" class="button"<?php echo ($filename == false) ? ' disabled' : ''; ?>>LIVE EDIT THIS NEWSLETTER</a><br />
						<a href="javascript:copyOutput();" style="width:100%;" class="button"<?php echo ($finished_html == false) ? ' disabled' : ''; ?>>COPY OUTPUT HTML TO CLIPBOARD</a>
					</fieldset>
				</div>
			</form>
		</div>

		<footer>
		    <div class="row">
		      <div class="large-12 medium-12 small-12 columns">
		      <p style="text-align: right">Copyright &copy; 2018, The Denver Post</p>
		      </div>
		    </div>
		</footer>

	<script src="//extras.denverpost.com/foundation/js/foundation.min.js"></script>
	<script>
		$(document).foundation();
		function copyOutput() {
		    $("output_text").select();
		    document.execCommand('copy');
		}
	</script>
</body>
</html>