<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

// All the functions stored in other files
require_once './wp-funcs.php';
require_once './yt-thumb.php';
require_once './variables.php';
require_once './constants.php';

// Bylines listed in the dropdown are added and removed here
$bylines_raw = file('bylines.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
function process_bylines($byline) {
	// There are four fields in a byline record, which will look something like this:
	// acrawford,'Adrian Crawford','acrawford@denverpost.com','Crawf33'
	// We want to turn it into a keyed array like this:
	// 'acrawford' => array('Adrian Crawford', 'acrawford@denverpost.com', 'Crawf33'),
	if ( $byline[2] === 'false' ) $byline[2] = false;
	if ( $byline[3] === 'false' ) $byline[3] = false;
	return $byline[0] => array($byline[1], $byline[2], $byline[3]);
}
$bylines = array_map('process_bylines', $bylines_raw);

// Raw code for byline format for nersletter templates
$byline_raw = '<p style="-ms-text-size-adjust:100%%;-webkit-text-size-adjust:100%%;text-transform:uppercase;font-weight:700;color:maroon;mso-line-height-rule:exactly;font-size:14px;line-height:1.5em;">By %1$s
                    <br /><a href="mailto:%2$s?subject=Newsletter%%20Feedback" title="Email %1$s @ The Denver Post" style="-ms-text-size-adjust:100%%;-webkit-text-size-adjust:100%%;border-bottom-style:none;position:relative;margin-top:.67em;margin-bottom:.67em;margin-right:0;margin-left:0;padding-top:0;padding-bottom:0;padding-right:0;padding-left:0;text-decoration:none;color:#1670A3!important;">%2$s</a> / <a href="https://twitter.com/%3$s" title="@%3$s on Twitter" style="-ms-text-size-adjust:100%%;-webkit-text-size-adjust:100%%;border-bottom-style:none;position:relative;margin-top:.67em;margin-bottom:.67em;margin-right:0;margin-left:0;padding-top:0;padding-bottom:0;padding-right:0;padding-left:0;text-decoration:none;color:#1670A3!important;">@%3$s</a></p>';

// broad-scope variables for moving stuff around
$author = $newsletter_date = $filename = $template = $input_text = $finished_html = false;

// Just FTPs to Extras
function do_ftp($files) {
    $conn_id = ftp_connect(FTP_SERVER) or die("Couldn't connect to $ftp_server");
    ftp_login($conn_id,FTP_USER_NAME,FTP_USER_PASS);
    ftp_pasv($conn_id, TRUE);    
    if ($files) {
        if (ftp_put($conn_id, FTP_DIRECTORY.'/screenshots/'.$files, './temp/'.$files, FTP_BINARY)) {
            unlink('./temp/'.$files);
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
    ftp_close($conn_id);
}

// Escapes dollar signs in strings so conversion with preg_replace won't accidentally remove the dollar sign in text
function escape_backreference($x){
    return preg_replace('/\$(\d)/', '\\\$$1', $x);
}

// Finds anchor (link) tags in a string and replaces them with inline-styled anchor tags
function add_link_styles($inputstring, $template_style) {
	if (preg_match('/<a.+?href="(.+?)">/', $inputstring)) {
		return preg_replace('/<a.+?href="(.+?)">/', '<a href="$1" style="'.$template_style.'">', $inputstring);
	} else {
		return $inputstring;
	}
}

// Finds blockquote elements and adds inline CSS
function add_quote_styles($inputstring) {
	if (preg_match('/<blockquote>/', $inputstring)) {
		return preg_replace('/<blockquote>/', '<blockquote style="font-family:serif;font-weight:bold;font-size:1.1em;color:#787878;border-left: 2px solid #ccc;padding:0 .5em;line-height:1.3em;margin-bottom:.2em;margin-left:.5em;">', $inputstring);
	} else {
		return $inputstring;
	}
}

// Replaces Wordpress image [caption] shorcodes with caption data with normal HTML
function format_images_without_captions($inputstring) {
	if (preg_match('/^<img\s.*?\bsrc="(.*?)".*?>/si', $inputstring)) {
		return preg_replace('/^<img\s.*?\bsrc="(.*?)".*?>/si', '<center>'."\r\n".'<p style="text-align: center;margin:0;padding:0;">'."\r\n".'<img src="$1" aria-hidden="true" width="100%" border="0" style="height: auto; background: #ffffff; font-family: sans-serif; width:100%;font-size: 15px; line-height: 100%; color: #555555;display:block;">'."\r\n".'</p>'."\r\n".'</center>', $inputstring);
	} else {
		return $inputstring;
	}
}

// Replaces Wordpress image [caption] shorcodes WITHOUT caption data with normal HTML
function format_images_with_captions($inputstring) {
	if (preg_match('/^\[cap.*\].+?src="(.+?)".+?>(.+?)\[\/cap.*\]$/', $inputstring)) {
		return preg_replace('/^\[cap.*\].+?src="(.+?)".+?>(.+?)\[\/cap.*\]$/', '<center>'."\r\n".'<p style="text-align: center;margin:0;padding:0;">'."\r\n".'<img src="$1" aria-hidden="true" width="100%" border="0" style="height: auto; background: #ffffff; font-family: sans-serif; width:100%;font-size: 15px; line-height: 100%; color: #555555;display:block;">'."\r\n".'</p>'."\r\n".'</center>'."\r\n".'<p style="font-size:0.85em;color:#595959;margin-top:.5em;font-style:italic;">$2</p>', $inputstring);
	} else {
		return $inputstring;
	}
}

// Makes a formatted HTML image embed from a giphy.com URL
function format_giphy_links($inputstring) {
	$giphy_start = 'https://giphy.com/';
	$gp_url_exploded = explode('/', $inputstring);
	$gp_url_text_exploded = explode('-', $gp_url_exploded[count($gp_url_exploded)-1]);
	$gp_url_text = $gp_url_text_exploded[count($gp_url_text_exploded)-1];
	$gp_media_url = 'https://media.giphy.com/media/' . $gp_url_text . '/giphy.gif';
	$raw_insert = '<span style="display:block;height:1em;width:100%%;"></span>'."\n".'<center>'."\n".'<p style="text-align: center;margin:0;padding:0;">'."\n".'<img src="%1$s" aria-hidden="true" width="80%%" border="0" style="height:auto;background:#ffffff;font-family:sans-serif; width:80%%;font-size:15px;line-height:100%%;color:#555555;display:block;margin:0 auto;">'."\n".'</p>'."\n".'</center>'."\n".'<span style="display:block;height:1em;width:100%%;"></span>'."\n";
	if (substr($inputstring, 0, strlen($giphy_start)) === $giphy_start) {
		return sprintf($raw_insert,trim($gp_media_url));
	} else {
		return $inputstring;
	}
}

// Makes a formatted and linked HTML image embed from a twitter.com URL
function format_twitter_links($inputstring) {
	$twitter_start = 'https://twitter.com/';
	$tw_url_exploded = explode('/', $inputstring);
	$file_name = 'tw_screenshot-'.$tw_url_exploded[count($tw_url_exploded)-1].'.png';
	$raw_insert = '<span style="display:block;height:1em;width:100%%;"></span>'."\n".'<center>'."\n".'<p style="text-align: center;margin:0;padding:0;">'."\n".'<a href="%1$s" style="text-decoration:none !important;border:none!important;" style="color:#CE4815;font-weight:bold;text-decoration:none;"><img src="%2$s" aria-hidden="true" width="80%%" border="0" style="height:auto;background:#ffffff;font-family:sans-serif; width:80%%;font-size:15px;line-height:100%%;color:#555555;display:block;margin:0 auto;"></a>'."\n".'</p>'."\n".'</center>'."\n".'<span style="display:block;height:1em;width:100%%;"></span>'."\n";
	if (substr($inputstring, 0, strlen($twitter_start)) === $twitter_start) {
		// NOTE: Gets a screenshot of a tweet, but it's not a very pretty one a lot of the time
		$tw_screenshot_raw = 'https://audubon-tweets.herokuapp.com/img?url='.$inputstring;
		$tw_screenshot = file_get_contents($tw_screenshot_raw);
		file_put_contents('./temp/'.$file_name, $tw_screenshot);
		// Put the image on Extras and use it
		$extras_url = 'https://extras.denverpost.com/newsletter/screenshots/'.$file_name;
		if (do_ftp($file_name)) {
			return sprintf($raw_insert,trim($inputstring),trim($extras_url));
		} else {
			return $inputstring;
		}
	} else {
		return $inputstring;
	}
}

// Makes a formatted and linked HTML image embed from a youtube.com URL
function format_youtube_links($inputstring) {
	$youtube_start = 'https://twitter.com/';
	// Gets the thumbnails of YouTube videos from part of the URL (stored in yt-thumb.php)
	if ($url_matches = getYouTubeIdFromURL($inputstring) && substr($inputstring, 0, strlen($youtube_start)) === $youtube_start) {
		$raw_insert = '<span style="display:block;height:1em;width:100%%;"></span>'."\n".'<center>'."\n".'<p style="text-align: center;margin:0;padding:0;">'."\n".'<a href="%1$s" style="text-decoration:none !important;border:none!important;" style="color:#CE4815;font-weight:bold;text-decoration:none;"><img src="%2$s" aria-hidden="true" width="80%%" border="0" style="height:auto;background:#ffffff;font-family:sans-serif; width:80%%;font-size:15px;line-height:100%%;color:#555555;display:block;margin:0 auto;"></a>'."\n".'</p>'."\n".'</center>'."\n".'<span style="display:block;height:1em;width:100%%;"></span>'."\n";
		$yt_thumb_url = 'https://extras.denverpost.com/newsletter/screenshots/'.getYoutubeThumb($url_matches);
		return sprintf($raw_insert,trim($inputstring),trim($yt_thumb_url));
	} else {
		return $inputstring;
	}
}

// Breaks apart every paragraph of the newsletter content input and iterates through them individually
// Formats images and external media embeds along the way, then reassembles
function go_through_grafs($inputstring) {
	$out = array();
	foreach (preg_split("/((\r?\n)|(\r\n?))/", $inputstring) as $line) {
		$line = format_images_without_captions($line);
		$line = format_images_with_captions($line);
		$line = format_twitter_links($line);
		$line = format_youtube_links($line);
		$line = format_giphy_links($line);
		$out[] = trim($line);
	}
	return implode("\r\n", $out);
}

// Ads in the appropriate ad tags (found in variables.php)
function add_ads($inputstring,$template,$template_ads) {
	// Only looking ofr H2 tags
	$heading_pattern = "/<h2>(.+?)<\/h2>/";
	preg_match_all($heading_pattern, $inputstring, $matches);
	$ad_one = $template_ads['ad_one']."\n".'<span style="display:block;height:1em;width:100%;"></span>';
	$ad_two = $template_ads['ad_two']."\n".'<span style="display:block;height:1em;width:100%;"></span>';
	// which H2 tags to place ads BEFORE based on how many there are
	$place_one = (count($matches[0]) <= 3) ? 1 : 2;
	$place_two = (count($matches[0]) <= 3) ? 2 : 4;
	// assume the Roundup has more H2 tags than other newsletters
	$place_two = ($template == 'roundup') ? 5 : $place_two;
	$counter = 0;
	// complicated-looking way to iterate through preg_matches and splice in content
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

// If the form was submitted and there's input to operate on...
if (!empty($_POST)) {
	// Get the POST stuff
	$input_text = isset($_POST['input_text']) ? $_POST['input_text'] : false;
	$template = isset($_POST['template']) ? $_POST['template'] : false;
	$author = isset($_POST['author']) ? $_POST['author'] : false;
	//Pick today's date if the supplied date is bad or missing
	if (isset($_POST['new_date']) && $_POST['new_date'] != '') {
		$newsletter_date = $_POST['new_date'];
	} else {
		$newsletter_date = date("Ymd");
	}
	//build the filename 
	$filename = $template.'-'.$newsletter_date.'.html';
	if (!file_exists('./cache/'.$filename)) {
		touch('./cache/'.$filename);
	}
	// Assemble the byline HTML block from pieces in
	$byline_text = ( $bylines[$author] && $bylines[$author][2] !== false ) ? sprintf(
		$byline_raw,
		$bylines[$author][0],
		$bylines[$author][1],
		$bylines[$author][2]
	) : '';

	// Get the raw template HTML from disk
	$template_raw = ($template) ? file_get_contents('./template-' . $template . '.html') : false;

	if ($input_text != false) {

		// Convert down-endashes to emdashes
		$finished_html = str_replace('--', '–', $input_text);
		// Replace H3 tags with H3 tags
		$finished_html = str_replace('<h3>', '<h2>', $finished_html);
		$finished_html = str_replace('</h3>', '</h2>', $finished_html);
		// Delete this junk elements Wordpress throws all over sometimes when editing in visual mode
		$finished_html = str_replace('<div class="mceTemp"></div>', "\n", $finished_html);
		// Go through and do a lot of stuff
		$finished_html = go_through_grafs($finished_html);
		// Run the Wordpress-borrowed paragraph creation tools
		$finished_html = wpautop($finished_html);
		// Sadd inline CSS to blockquotes and links
		$finished_html = add_quote_styles($finished_html);
		$finished_html = add_link_styles($finished_html,$templates[$template]['link_style']);
		// Insert ads
		$finished_html = add_ads($finished_html,$template,$templates[$template]);

	}
	//Insert the Byline and Content elements into the raw template HTML
	if ($template && $finished_html) {
		$template_raw = preg_replace('/<!--{{BYLINE}}-->(.*?)<!--{{\/BYLINE}}-->/', '<!--{{BYLINE}}-->' . escape_backreference($byline_text) . '<!--{{/BYLINE}}-->', $template_raw);
		$template_raw = preg_replace('/<!--{{CONTENT}}-->(.*?)<!--{{\/CONTENT}}-->/', '<!--{{CONTENT}}-->' . "\n\n" . escape_backreference($finished_html) . "\n\n" . '<!--{{/CONTENT}}-->', $template_raw);
		$finished_html = $template_raw;
	}
	// Save the file
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
						<input type="submit" class="button" id="process_btn" style="width:100%;" value="PROCESS WORDPRESS INTO HTML" />
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
		<div id="wait_gif" style="background-color:rgba(255,255,255,0.6);background-repeat:no-repeat;background-image:url('./wait.gif');background-position:center center;position:fixed;top:0;left:0;height:100%;width:100%;pointer-events:none;z-index:99999;display:none;"></div>
	<script src="//extras.denverpost.com/foundation/js/foundation.min.js"></script>
	<script>
		$(document).foundation();
		// Powers waiting GIF
		$('#process_btn').on('click',function() {
			$('#wait_gif').css('display','block');
		});
		// Powers Copy button
		function copyOutput() {
		    $("output_text").select();
		    document.execCommand('copy');
		}
	</script>
</body>
</html>
