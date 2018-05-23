<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('./simple_html_dom.php');
require_once('./format.php');

$bylines = array(
	'schneider' => array(
		'byline' => '<p style="-ms-text-size-adjust:100%;-webkit-text-size-adjust:100%;text-transform:uppercase;font-weight:700;color:maroon;mso-line-height-rule:exactly;font-size:14px;line-height:1.5em;">By Daniel J. Schneider
                    <br /><a href="mailto:dschneider@denverpost.com?subject=Roundup%20Feedback" title="Email Daniel J. Schneider @ The Denver Post" style="-ms-text-size-adjust:100%;-webkit-text-size-adjust:100%;border-bottom-style:none;position:relative;margin-top:.67em;margin-bottom:.67em;margin-right:0;margin-left:0;padding-top:0;padding-bottom:0;padding-right:0;padding-left:0;text-decoration:none;color:#1670A3!important;">dschneider@denverpost.com</a> / <a href="https://twitter.com/schneidan" title="@schneidan on Twitter" style="-ms-text-size-adjust:100%;-webkit-text-size-adjust:100%;border-bottom-style:none;position:relative;margin-top:.67em;margin-bottom:.67em;margin-right:0;margin-left:0;padding-top:0;padding-bottom:0;padding-right:0;padding-left:0;text-decoration:none;color:#1670A3!important;">@schneidan</a></p>',
		'correx' => '<h2>Get in Touch</h2>
                  	<p>Remember, if you see something that doesn\'t look right or just have a comment, thought or suggestion, <a href="mailto:dschneider@denverpost.com?subject=Roundup Feedback" style="color:#CE4815;font-weight:bold;text-decoration:none;">email me at dschneider@denverpost.com</a> or <a href="https://twitter.com/schneidan" style="color:#CE4815;font-weight:bold;text-decoration:none;">yell at me on Twitter</a>.</p>'),
	'gonzalez' => array(
		'byline' => '<p style="-ms-text-size-adjust:100%;-webkit-text-size-adjust:100%;text-transform:uppercase;font-weight:700;color:maroon;mso-line-height-rule:exactly;font-size:14px;line-height:1.5em;">By Susan Gonzalez
                    <br /><a href="mailto:sgonzalez@denverpost.com?subject=Roundup%20Feedback" title="Email Susan Gonzalez @ The Denver Post" style="-ms-text-size-adjust:100%;-webkit-text-size-adjust:100%;border-bottom-style:none;position:relative;margin-top:.67em;margin-bottom:.67em;margin-right:0;margin-left:0;padding-top:0;padding-bottom:0;padding-right:0;padding-left:0;text-decoration:none;color:#1670A3!important;">sgonzalez@denverpost.com</a> / <a href="https://twitter.com/TheNewsan" title="@TheNewsan on Twitter" style="-ms-text-size-adjust:100%;-webkit-text-size-adjust:100%;border-bottom-style:none;position:relative;margin-top:.67em;margin-bottom:.67em;margin-right:0;margin-left:0;padding-top:0;padding-bottom:0;padding-right:0;padding-left:0;text-decoration:none;color:#1670A3!important;">@TheNewsan</a></p>',
		'correx' => '<h2>Get in Touch</h2>
                  	<p>Remember, if you see something that doesn\'t look right or just have a comment, thought or suggestion, <a href="mailto:sgonzalez@denverpost.com?subject=Roundup Feedback" style="color:#CE4815;font-weight:bold;text-decoration:none;">email me at sgonzalez@denverpost.com</a> or <a href="https://twitter.com/TheNewsan" style="color:#CE4815;font-weight:bold;text-decoration:none;">yell at me on Twitter</a>.</p>'),
	'crawford' => array(
		'byline' => '<p style="-ms-text-size-adjust:100%;-webkit-text-size-adjust:100%;text-transform:uppercase;font-weight:700;color:maroon;mso-line-height-rule:exactly;font-size:14px;line-height:1.5em;">By Adrian Crawford
                    <br /><a href="mailto:acrawford@denverpost.com?subject=Roundup%20Feedback" title="Email Adrian Crawford @ The Denver Post" style="-ms-text-size-adjust:100%;-webkit-text-size-adjust:100%;border-bottom-style:none;position:relative;margin-top:.67em;margin-bottom:.67em;margin-right:0;margin-left:0;padding-top:0;padding-bottom:0;padding-right:0;padding-left:0;text-decoration:none;color:#1670A3!important;">acrawford@denverpost.com</a> / <a href="https://twitter.com/Crawf33" title="@Crawf33 on Twitter" style="-ms-text-size-adjust:100%;-webkit-text-size-adjust:100%;border-bottom-style:none;position:relative;margin-top:.67em;margin-bottom:.67em;margin-right:0;margin-left:0;padding-top:0;padding-bottom:0;padding-right:0;padding-left:0;text-decoration:none;color:#1670A3!important;">@Crawf33</a></p>',
		'correx' => '<h2>Get in Touch</h2>
                  	<p>Remember, if you see something that doesn\'t look right or just have a comment, thought or suggestion, <a href="mailto:acrawford@denverpost.com?subject=Roundup Feedback" style="color:#CE4815;font-weight:bold;text-decoration:none;">email me at acrawford@denverpost.com</a> or <a href="https://twitter.com/Crawf33" style="color:#CE4815;font-weight:bold;text-decoration:none;">yell at me on Twitter</a>.</p>')
	);

function source_span($input) {
	$input = str_replace('.com','',$input);
	switch ($input) {
		case 'Denver Post':
			$source_name = false;
			break;
		case 'The Denver Post':
			$source_name = false;
			break;
		case 'Wall Street Journal':
			$source_name = 'WSJ';
			break;
		case 'New York Times':
			$source_name = 'NYT';
			break;
		case 'The New York Times':
			$source_name = 'NYT';
			break;
		case 'NYTimes':
			$source_name = 'NYT';
			break;
		case 'Associated Press':
			$source_name = 'AP';
			break;
		case 'The Associated Press':
			$source_name = 'AP';
			break;
		case 'Washington Post':
			$source_name = 'WaPo';
			break;
		case 'The Washington Post':
			$source_name = 'WaPo';
			break;
		case 'POLITICO':
			$source_name = 'Politico';
			break;
		default:
			$source_name = $input;
			break;
	}
	return ($source_name) ? sprintf(' <span style="font-style:italic;font-weight:bold;color:maroon;font-family:serif">&mdash; %s</span>',$source_name) : '';
}

$link_count = 0;
$schcheck = 'checked';
$goncheck = $cracheck= '';
$file = (isset($_GET['file'])) ? $_GET['file'] : false;
$links_processed = $input_text = $blank = $byline_text_file = $content_text_file = $intro_text_file = $sotd_text_file = $playlist_text_file = $correx_text_file = false;

function add_link_styles($inputstring) {
	if ( ! preg_match('/\<a(.+?)style="(.+?)"(.+?)\>/',$inputstring) ) {
		return preg_replace('/\<a href="(.+?)"\>/', '<a href="$1" style="border-bottom:1px dashed;padding:2px 0;text-decoration:none;color:#13618D;font-weight:bold;">', $inputstring);
	} else {
		return $inputstring;
	}
}

if (empty($_POST) && $file != false && file_exists('./cache/'.$file)) {
	$length = strlen(file_get_contents('./cache/'.$file));
	if ($length>1) {
		$links_processed = file_get_contents('./cache/'.$file);
		preg_match('/<!--{{BYLINE}}-->(.*?)<!--{{\/BYLINE}}-->/s', $links_processed, $byline_matches);
		$byline_text_file = (isset($byline_matches[1])) ? $byline_matches[1] : false;
		preg_match('/<!--{{CONTENT}}-->(.*?)<!--{{\/CONTENT}}-->/s', $links_processed, $content_matches);
		$content_text_file = (isset($content_matches[1])) ? $content_matches[1] : false;
		preg_match('/<!--{{INTRO}}-->(.*?)<!--{{\/INTRO}}-->/s', $links_processed, $intro_matches);
		$intro_text_file = (isset($intro_matches[1])) ? add_link_styles($intro_matches[1]) : false;
		preg_match('/<!--{{SOTD}}-->(.*?)<!--{{\/SOTD}}-->/s', $links_processed, $sotd_matches);
		$sotd_text_file = (isset($sotd_matches[1])) ? add_link_styles($sotd_matches[1]) : false;
		preg_match('/<!--{{CORREX}}-->(.*?)<!--{{\/CORREX}}-->/s', $links_processed, $correx_matches);
		$correx_text_file = (isset($correx_matches[1])) ? add_link_styles($correx_matches[1]) : false;
	} else {
		$blank = true;
	}
}
if ($blank == true || !empty($_POST)) {
	$input_text = isset($_POST['input_text']) ? $_POST['input_text'] : false;
	$template = isset($_POST['templates']) ? 'template-'.$_POST['templates'].'.html' : false;
	$author = isset($_POST['authors']) ? $_POST['authors'] : false;
	$byline_text = ($author != false) ? $bylines[$author]['byline'] : '';
	if ($author == 'gonzalez') {
		$schcheck = '';
		$goncheck = 'checked';
	}
	if ($author == 'crawford') {
		$schcheck = '';
		$cracheck = 'checked';
	}
	$intro_text = isset($_POST['intro_text']) ? $_POST['intro_text'] : false;
	$sotd_text = isset($_POST['sotd_text']) ? $_POST['sotd_text'] : false;
	$correx_text = ( isset($_POST['correx_text']) && strlen($_POST['correx_text']) > 1 ) ? $_POST['correx_text'] : ( ( ($author != false) && isset($bylines[$author]) && isset($bylines[$author]['correx']) ) ? $bylines[$author]['correx'] : false );

	$template_raw = ($template) ? file_get_contents('./'.$template) : false;

	if ($input_text != false) {

		// Remove line breaks
		$inputfile = trim(preg_replace('/\s\s+/', ' ', $input_text));

		// Strip head element and contents
		$inputfile = preg_replace("/<head> s*(.*?) <\/head>/i", '', $inputfile);

		// Convert non-static elements to junk with regex
		$inputfile = preg_replace('/qrCode\?pageId=(.*?)"/', 'XXX"', $inputfile);
		$inputfile = preg_replace('/Shared: (.*?) tabs/', 'XXX', $inputfile);

		// Strip all the icon codes
		$inputfile = preg_replace('/<img src="https\:\/\/www\.google\.com\/s2\/favicons\?domain=(.*?)" style="vertical-align\: middle\; width\:16px\; height\:16px">/', '', $inputfile);

		// Replace additional surrounding HTML with nothing

		$inputfile = preg_replace('/<!DOCTYPE html(.*?)About OneTab/', '', $inputfile);
		$inputfile = trim(preg_replace('/<\/a><\/span><\/div>( *)<\/div>/', '', $inputfile));
		$inputfile = trim(str_replace('<div style="padding-bottom: 0px; border-bottom-width: 1px; border-bottom-style: dashed; border-bottom-color: rgb(221, 221, 221);"></div>', '', $inputfile));
		$inputfile = trim(str_replace('<div style="padding-top:0px;">&nbsp;</div> </div> </body></html>', '', $inputfile));
		$inputfile = trim(str_replace('<div style="padding-top:8px;"></div>', '', $inputfile));
		$inputfile = trim(str_replace('<div style="padding-top:8px; "></div>', '', $inputfile));
		$inputfile = trim(str_replace('<div style="padding-top: 8px;"></div>', '', $inputfile));
		$inputfile = trim(str_replace('<div style="padding-top: 8px; "></div>', '', $inputfile));
		$inputfile = trim(str_replace('<div style="padding-top:0px;"> </div>', '', $inputfile));
		$inputfile = trim(preg_replace('/<div style="padding-bottom:(\s*)8px;">(\s*)<\/div>/', '', $inputfile));
		$inputfile = trim(preg_replace('/<div style="padding-top:0px;">(\s*)<\/div>/', '', $inputfile));
		$inputfile = trim(str_replace('<div style="padding-top:0px;"></div>', '', $inputfile));
		$inputfile = trim(str_replace('<div style="padding-top:0px;"></div>', '', $inputfile));

		

		// Close those paragraph tags
		$inputfile = str_replace('</a> </div>', "</a></p>", $inputfile);

		// Convert pipes to endashes
		$inputfile = str_replace(' | ', ' – ', $inputfile);
		$inputfile = str_replace(' - ', ' – ', $inputfile);

		// decode the remaining HTML entities ... twice, and then url encoindg
		$inputfile = html_entity_decode($inputfile,ENT_QUOTES,'UTF-8');
		$inputfile = html_entity_decode($inputfile,ENT_QUOTES,'UTF-8');
		$inputfile = urldecode($inputfile);

		// Convert title chunks to H2 tags with spacers before them
		$inputfile = preg_replace('/<div style="padding-top:10px; padding-bottom:0px; padding-left:24px; font-family: \'Open Sans\', \'Helvetica Neue\', Arial, sans-serif; color: #444; font-size:26px; font-weight:300">(.*?)<\/div>/', "\n\n" . "<span style=\"display:block;height:1em;width:100%;\"></span>"."\n\n"."<h2>$1</h2>", $inputfile);

		// Strip styling from link tags
		$inputfile = str_replace('<a style="vertical-align: middle; padding-left: 10px; padding-right: 12px; text-decoration: none;" href', '<a style="border-bottom:1px dashed;padding:2px 0;text-decoration:none;color:#13618D;font-weight:bold;" href', $inputfile);

		// Change ugly divs to simple paragraphs
		$inputfile = str_replace(' <div style="padding-left: 24px; padding-top: 8px; position: relative; font-size: 13px;">  <a', '<p>+ <a', $inputfile);

		$inputfile = preg_replace('/(\n)+/m', "\n", $inputfile);

		$html = new simple_html_dom();

		$html->load($inputfile,true,false);

		foreach($html->find('div') as $div) {
			$div->outertext = '';
		}

		$html->save();

		foreach($html->find('div') as $div) {
			$div->outertext = NULL;
		}

		$html->save();

		foreach($html->find('a[href]') as $link) {
			$link_count++;
			$link_override = false;
			$src = $link->href;
			$text = $link->innertext;
			$source_text = '';

			$re = '/(.*) – (.*)/s';
			if ( preg_match('/ on Twitter: "/', $text) == 1) {
				preg_match_all('/(.*) on Twitter: "(.*)/', $text, $twmatches);
				$source_text = source_span($twmatches[1][0]);
				preg_match_all('#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#', $twmatches[2][0], $twmatch);
				$link_override = $twmatch[0][0];
				$text_raw = str_replace($twmatch[0], '', $twmatches[2][0]);
				$text_raw = trim(preg_replace('/"$/', '', $text_raw));
				$text_raw = str_replace('--', ' – ', $text_raw);
				$text_raw = str_replace('  ', ' ', $text_raw);
				$text = $text_raw;
			} else if (preg_match($re,$text) == 1) {
				preg_match_all($re, $text, $matches);
				$source_text = source_span($matches[2][0]);
				$text = $matches[1][0];
			}

			$quotesearch = array("\xe2\x80\x9c", "\xe2\x80\x9d", "\xe2\x80\x98", "\xe2\x80\x99");
			$quotereplace = array('"', '"', "'", "'");
			 
			$text = str_replace($quotesearch, $quotereplace, $text);
			
			//echo "\n".'Corrected: '.strtok($src,'?')."\n";
			$link->href = ($link_override !== false) ? $link_override : ( strpos($src, 'utm_' !== false) ? strtok($src,'?') : $src );
			$link->title = $link->href;
			$link->innertext = $text.$source_text;
		}

		$links_processed = $html->save();
		$links_format = new Format;
		$links_processed = $links_format->HTML($links_processed);
		$links_processed = preg_replace('/(.*?)<\/div>(.*?)<\/body><\/html>/i', '', $links_processed);
		$links_processed = trim(str_replace('<div style="padding-top:8px;"></div>', '', $links_processed));
		$links_processed = trim(str_replace('<div style="padding-top:8px; "></div>', '', $links_processed));
		$links_processed = trim(str_replace('<div style="padding-top: 8px;"></div>', '', $links_processed));
		$links_processed = trim(str_replace('<div style="padding-top: 8px; "></div>', '', $links_processed));
		$links_processed = trim(str_replace('<div style="padding-top:0px;"> </div>', '', $links_processed));
		$links_processed = trim(preg_replace('/<div style="padding-top:0px;">(\s*)<\/div>/', '', $links_processed));
		$links_processed = trim(str_replace('<div style="padding-top:0px;"></div>', '', $links_processed));
		$links_processed = preg_replace('/ <span style="font-style:italic;font-weight:bold;color:maroon;font-family:serif">(.*?)<\/span><\/a>/i', '</a> <span style="font-style:italic;font-weight:bold;color:maroon;font-family:serif">$1</span>', $links_processed);
		$links_processed = preg_replace('/<span style="display:block;height:1em;width:100%;"><\/span>( *)<h2>(.*?)<\/h2>/', "\n\n" . "<span style=\"display:block;height:1em;width:100%;\"></span>"."\n"."<h2>$1</h2>", $links_processed);
		$links_processed = preg_replace('/<\/p>(\s*)<span/', '</p>'."\n\n".'<span', $links_processed);
		$links_processed = str_replace('<div style="padding-bottom: 8px; "></div> <span', '<span', $links_processed);
		$links_processed = preg_replace('/(^\s*)<span/', "\n\n".'<span', $links_processed);
		//$links_processed = preg_replace('/(\n)+/m', "\n", $links_processed);

	}
	if ($template) {
		$template_raw = preg_replace('/<!--{{BYLINE}}-->(.*?)<!--{{\/BYLINE}}-->/', '<!--{{BYLINE}}-->'.$byline_text.'<!--{{/BYLINE}}-->', $template_raw);
		$template_raw = preg_replace('/<!--{{CONTENT}}-->(.*?)<!--{{\/CONTENT}}-->/', '<!--{{CONTENT}}-->'."\n\n".$links_processed."\n\n".'<!--{{/CONTENT}}-->', $template_raw);
		$template_raw = preg_replace('/<!--{{INTRO}}-->(.*?)<!--{{\/INTRO}}-->/', '<!--{{INTRO}}-->'.add_link_styles($intro_text).'<!--{{/INTRO}}-->', $template_raw);
		$template_raw = preg_replace('/<!--{{SOTD}}-->(.*?)<!--{{\/SOTD}}-->/', '<!--{{SOTD}}-->'.add_link_styles($sotd_text).'<!--{{/SOTD}}-->', $template_raw);
		$template_raw = preg_replace('/<!--{{CORREX}}-->(.*?)<!--{{\/CORREX}}-->/', '<!--{{CORREX}}-->'.add_link_styles($correx_text).'<!--{{/CORREX}}-->', $template_raw);
		$links_processed = $template_raw;
	}
	if ($file != false) { file_put_contents('./cache/'.$file,$links_processed); }
}

?>

<!DOCTYPE html>
<head>
	<title>ROUNDUP link processor</title>
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
					<li class="top-top"><a href="index.php"><strong>EDIT ANOTHER ROUNDUP</strong></a></li>
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
					<h1>Process you some Roundup links</h1>
					<p>Outputs some unfinished (but way more finished) Roundup code from a OneTab page source</p>
					<p>NOTE: Use full HTML in the Intro, SotD, etc., as you want it to appear.</p>
					<p><span style="color:red;font-weight:bold">IMPORTANT:</span> You must click the CREATE button at the bottom of the left-hand column before attempting to COPY AS HTML or EDIT LIVE.</p>
				</div>
			</div>
		</div>
		<div id="admin" class="row">
			<form id="roundupraw" name="roundupraw" method="post">
				<div class="large-6 columns">
					<fieldset>
						<legend> Options </legend>
							<fieldset>
								<legend> Template </legend>
								<input type="radio" name="templates" value="roundup" checked> Mile High Roundup
							</fieldset>
							<fieldset>
								<legend> Author </legend>
								<?php if (!$byline_text_file) { ?>
								<input type="radio" name="authors" value="schneider" <?php echo $schcheck; ?>> Daniel J. Schneider<br />
								<input type="radio" name="authors" value="gonzalez" <?php echo $goncheck; ?>> Susan Gonzalez<br />
								<input type="radio" name="authors" value="crawford" <?php echo $cracheck; ?>> Adrian Crawford
								<?php } else { ?>
								<textarea name="intro_text" style="width:100%;height:250px;"><?php echo $byline_text_file; ?></textarea>
								<?php } ?>
							</fieldset>
							<fieldset>
								<legend> Intro </legend>
								<textarea name="intro_text" style="width:100%;height:200px;"><?php echo ($intro_text_file != false) ? $intro_text_file : ''; ?></textarea>
							</fieldset>
							<fieldset>
								<a href="javascript:populateSOTD();" id="sotd_text_button" style="font-style:italic;font-size:.9em;">Populate blank SOTD format</a>
								<legend> SotD </legend>
								<textarea name="sotd_text" id="sotd_text" style="width:100%;height:200px;"><?php echo ($sotd_text_file != false) ? $sotd_text_file : ''; ?></textarea>
							</fieldset>
							<fieldset>
								<legend> CX chunk (overrides author default) </legend>
								<textarea name="correx_text" style="width:100%;height:200px;"><?php echo ($correx_text_file != false) ? $correx_text_file : ''; ?></textarea>
							</fieldset>
					</fieldset>
					<input type="submit" value="CREATE"  style="width:100%;" class="button" />
				</div>
				<div class="large-6 columns">
					<fieldset>
						<legend> OneTab source </legend>
							<p style="color:#888;font-style:italic"><strong>To get source from your OneTab page:</strong> right-click and select View Source (also: CTRL+U or CMD+OPTION+U); CTRL+A to select all, CTRL+C to copy, CTRL+V to paste.</p>
							<label for="input_text">Paste here:</label>
							<textarea id="input_text" name="input_text" style="width:100%;height:400px;"><?php echo ($input_text!=false) ? $input_text : ''; ?></textarea>
					</fieldset>
					<fieldset>
						<legend> HTML Output </legend>
							<textarea id="roundup_text" style="width:100%;height:600px;"><?php echo $links_processed; ?></textarea>
							<input type="button" value="COPY AS HTML" class="button"  style="width:100%;" id="roundup_text_button" />
							<script>
								var copyTextareaBtn = document.querySelector('#roundup_text_button');
								copyTextareaBtn.addEventListener('click', function(event) {
									var copyTextarea = document.querySelector('#roundup_text');
									copyTextarea.select();
									try {
									    var successful = document.execCommand('copy');
									    var msg = successful ? 'Copied!' : 'Oops...';
									    var col = successful ? 'green' : 'red';
									    copyTextareaBtn.style.backgroundColor = col;
									    copyTextareaBtn.value = msg;
									} catch (err) {
										console.log('Oops, unable to copy');
									}
								});
							</script>
							<?php if ( $file != false ) { ?>
								<a href="./live.php?file=<?php echo $file; ?>" style="width:100%;" class="button" id="live_edit">EDIT LIVE</a>
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

	<script src="//extras.denverpost.com/foundation/js/foundation.min.js"></script>
	<script>
		$(document).foundation();
		function populateSOTD() {
			var SOTDstring = '<p><strong>Song:</strong> &ldquo;<a href="YOUTUBE_LINK_HERE" title="YOUTUBE_LINK_HERE">SONG_TITLE_HERE</a>&rdquo;</p>\n' +
'<p><strong>Artist:</strong> ARTIST_NAME_HERE</p>\n' +
'<p><strong>Sounds like:</strong> YOUR_DESCRIPTION_HERE</p>';
			$('#sotd_text').val(SOTDstring);
		}
	</script>
</body>
</html>