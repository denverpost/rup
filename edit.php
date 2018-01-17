<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('./simple_html_dom.php');
require_once('./format.php');

$bylines = array(
	'lubbers' => array(
		'byline' => '<p class="byline">By Eric Lubbers
					<br /><a href="mailto:;elubbers@denverpost.com?subject=Roundup%20Feedback" title="Email Eric Lubbers @ The Denver Post" style="border-bottom: none;">elubbers@denverpost.com</a> / <a href="http://twitter.com/brofax" title="@brofax on Twitter" style="border-bottom: none;">@brofax</a></p>',
		'correx' => '<h2>Get in Touch</h2>
					<p>Remember, if you see something that doesn\'t look right or just have a comment, thought or suggestion, <a href="mailto:elubbers@denverpost.com?subject=Roundup Feedback">email me at elubbers@denverpost.com</a> or <a href="http://twitter.com/brofax">yell at me on Twitter</a>.</p>',
        'playlist' => '<p><strong>Follow our Spotify playlist for an endless fountain of tunes: <a href="http://enews.denverpost.com/q/ooNJ7_PjvFJxizqwJn7JblNPkeFU5voLse94S-zCFdqr8XIFp3DEJ-UX3ZFw" title="http://open.spotify.com/user/ericjlubbers/playlist/0qyRwyDlwECmsGb3JwPeE8" target="_blank">Click this link</a> or search "Mile High Roundup" in your app.</strong></p>'),
	'schneider' => array(
		'byline' => '<p class="byline">By Daniel J. Schneider
                    <br /><a href="mailto:dschneider@denverpost.com?subject=Roundup%20Feedback" title="Email Daniel J. Schneider @ The Denver Post" style="border-bottom: none;">dschneider@denverpost.com</a> / <a href="http://twitter.com/schneidan" title="@schneidan on Twitter" style="border-bottom: none;">@schneidan</a></p>',
		'correx' => '<h2>Get in Touch</h2>
                  	<p>Remember, if you see something that doesn\'t look right or just have a comment, thought or suggestion, <a href="mailto:dschneider@denverpost.com?subject=Roundup Feedback">email me at dschneider@denverpost.com</a> or <a href="http://twitter.com/schneidan">yell at me on Twitter</a>.</p>',
        'playlist' => '<p><strong>Follow our Spotify playlist for an endless fountain of tunes: <a href="http://open.spotify.com/user/ericjlubbers/playlist/0qyRwyDlwECmsGb3JwPeE8" title="http://open.spotify.com/user/ericjlubbers/playlist/0qyRwyDlwECmsGb3JwPeE8" target="_blank">Click this link</a> or search "Mile High Roundup" in your app.</strong></p>')
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
	return ($source_name) ? sprintf(' <span class="source">&mdash; %s</span>',$source_name) : '';
}

$link_count = 0;
$schcheck = '';
$lubcheck = 'checked';
$file = (isset($_GET['file'])) ? $_GET['file'] : false;
$links_processed = $input_text = $blank = $byline_text_file = $content_text_file = $intro_text_file = $sotd_text_file = $playlist_text_file = $correx_text_file = false;

if (empty($_POST) && $file != false && file_exists('./cache/'.$file)) {
	$length = strlen(file_get_contents('./cache/'.$file));
	if ($length>1) {
		$links_processed = file_get_contents('./cache/'.$file);
		preg_match('/<!--{{BYLINE}}-->(.*?)<!--{{\/BYLINE}}-->/s', $links_processed, $byline_matches);
		$byline_text_file = $byline_matches[1];
		preg_match('/<!--{{CONTENT}}-->(.*?)<!--{{\/CONTENT}}-->/s', $links_processed, $content_matches);
		$content_text_file = $content_matches[1];
		preg_match('/<!--{{INTRO}}-->(.*?)<!--{{\/INTRO}}-->/s', $links_processed, $intro_matches);
		$intro_text_file = $intro_matches[1];
		preg_match('/<!--{{SOTD}}-->(.*?)<!--{{\/SOTD}}-->/s', $links_processed, $sotd_matches);
		$sotd_text_file = $sotd_matches[1];
		preg_match('/<!--{{PLAYLIST}}-->(.*?)<!--{{\/PLAYLIST}}-->/s', $links_processed, $playlist_matches);
		$playlist_text_file = $playlist_matches[1];
		preg_match('/<!--{{CORREX}}-->(.*?)<!--{{\/CORREX}}-->/s', $links_processed, $correx_matches);
		$correx_text_file = $correx_matches[1];
	} else {
		$blank = true;
	}
}
if ($blank == true || !empty($_POST)) {
	$input_text = isset($_POST['input_text']) ? $_POST['input_text'] : false;
	$template = isset($_POST['templates']) ? 'template-'.$_POST['templates'].'.html' : false;
	$author = isset($_POST['authors']) ? $_POST['authors'] : false;
	$byline_text = ($author != false) ? $bylines[$author]['byline'] : '';
	if ($author = 'schneider') {
		$schcheck = 'checked';
		$lubcheck = '';
	}
	$intro_text = isset($_POST['intro_text']) ? $_POST['intro_text'] : false;
	$playlist_text = ( isset($_POST['playlist_text']) && strlen($_POST['playlist_text']) > 1 ) ? $_POST['playlist_text'] : ( ( ($author != false) && isset($bylines[$author]['playlist']) ) ? $bylines[$author]['playlist'] : false );
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
		$inputfile = trim(preg_replace('/<div style="padding-top:0px;">( *)<\/div>/', '', $inputfile));
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

		// Convert title chunks to H2 tags
		$inputfile = preg_replace('/<div style="padding-top:10px; padding-bottom:0px; padding-left:24px; font-family: \'Open Sans\', \'Helvetica Neue\', Arial, sans-serif; color: #444; font-size:26px; font-weight:300">(.*?)<\/div>/', "<h2>$1</h2>", $inputfile);

		// Strip styling from link tags
		$inputfile = str_replace('<a style="vertical-align: middle; padding-left: 10px; padding-right: 12px; text-decoration: none;" href', '<a href', $inputfile);

		// Change ugly divs to simple paragraphs
		$inputfile = str_replace(' <div style="padding-left: 24px; padding-top: 8px; position: relative; font-size: 13px;">  <a', '<p>+ <a', $inputfile);

		$html = new simple_html_dom();

		$html->load($inputfile,true,false);

		foreach($html->find('div') as $div) {
			if(trim($div->innertext) == '') {
				$div->outertext = '';
			}
		}

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
			$link->href = ($link_override == false) ? strtok($src,'?') : $link_override;
			$link->title = $link->href;
			$link->innertext = $text.$source_text;
		}

		$links_processed = $html->save();
		$links_format = new Format;
		$links_processed = $links_format->HTML($links_processed);
		$links_processed = preg_replace('/(.*?)<\/div>(.*?)<\/body><\/html>/i', '', $links_processed);
		$links_processed = preg_replace('/<span class="source">(.*?)<\/span><\/a>/i', '</a>. <span class="source">$1</span>', $links_processed);

	}
	if ($template) {
		$template_raw = preg_replace('/<!--{{BYLINE}}-->(.*?)<!--{{\/BYLINE}}-->/', '<!--{{BYLINE}}-->'.$byline_text.'<!--{{/BYLINE}}-->', $template_raw);
		$template_raw = preg_replace('/<!--{{CONTENT}}-->(.*?)<!--{{\/CONTENT}}-->/', '<!--{{CONTENT}}-->'.$links_processed.'<!--{{/CONTENT}}-->', $template_raw);
		$template_raw = preg_replace('/<!--{{INTRO}}-->(.*?)<!--{{\/INTRO}}-->/', '<!--{{INTRO}}-->'.$intro_text.'<!--{{/INTRO}}-->', $template_raw);
		$template_raw = preg_replace('/<!--{{SOTD}}-->(.*?)<!--{{\/SOTD}}-->/', '<!--{{SOTD}}-->'.$sotd_text.'<!--{{/SOTD}}-->', $template_raw);
		$template_raw = preg_replace('/<!--{{PLAYLIST}}-->(.*?)<!--{{\/PLAYLIST}}-->/', '<!--{{PLAYLIST}}-->'.$playlist_text.'<!--{{/PLAYLIST}}-->', $template_raw);
		$template_raw = preg_replace('/<!--{{CORREX}}-->(.*?)<!--{{\/CORREX}}-->/', '<!--{{CORREX}}-->'.$correx_text.'<!--{{/CORREX}}-->', $template_raw);
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
			<div class="row">
				<div class="large-9 columns">
					<h1>Process you some Roundup links</h1>
					<p>Outputs some unfinished (but way more finished) Roundup code from a OneTab page source</p>
					<p>Get source from your OneTab page: right-click and select View Source (also: CTRL+U or CMD+OPTION+U); CTRL+A to select all, CTRL+C to copy, CTRL+V to paste.</p>
				</div>
			</div>
		</div>
		<div id="admin" class="row">
			<form id="roundupraw" name="roundupraw" method="post">
				<div class="large-6 columns">
					<fieldset>
						<legend> OneTab source </legend>
							<label for="input_text">Paste here:</label>
							<textarea id="input_text" name="input_text" style="width:100%;height:100px;"><?php echo ($input_text!=false) ? $input_text : ''; ?></textarea>
					</fieldset>
					<fieldset>
						<legend> HTML Output </legend>
							<textarea id="roundup_text" style="width:100%;height:600px;"><?php echo $links_processed; ?></textarea>
							<input type="button" value="Copy!" class="button" id="roundup_text_button" />
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
								<input type="radio" name="authors" value="lubbers" <?php echo $lubcheck; ?>> Eric Lubbers
								<?php } else { ?>
								<textarea name="intro_text" style="width:100%;height:250px;"><?php echo $byline_text_file; ?></textarea>
								<?php } ?>
							</fieldset>
							<fieldset>
								<legend> Intro </legend>
								<textarea name="intro_text" style="width:100%;height:250px;"><?php echo ($intro_text_file != false) ? $intro_text_file : ''; ?></textarea>
							</fieldset>
							<fieldset>
								<legend> SotD </legend>
								<textarea name="sotd_text" style="width:100%;height:250px;"><?php echo ($sotd_text_file != false) ? $sotd_text_file : ''; ?></textarea>
							</fieldset>
							<fieldset>
								<legend> Playlist Promo (overrides author default)</legend>
								<textarea name="playlist_text" style="width:100%;height:250px;"><?php echo ($playlist_text_file != false) ? $playlist_text_file : ''; ?></textarea>
							</fieldset>
							<fieldset>
								<legend> CX chunk (overrides author default) </legend>
								<textarea name="correx_text" style="width:100%;height:250px;"><?php echo ($correx_text_file != false) ? $correx_text_file : ''; ?></textarea>
							</fieldset>
					</fieldset>
					<input type="submit" value="Update" class="button" />
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