<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once './variables.php';

$file = (isset($_GET['file'])) ? $_GET['file'] : false;

if ($file != false && isset($_POST['editor_html'])) {
	file_put_contents('./cache/'.$file,html_entity_decode($_POST['editor_html']));
}

$editor_html = file_get_contents('./cache/'.$file);
$fileparts = explode('-', $file);
$nl_type = $fileparts[0];
?>

<!DOCTYPE html>
<head>
	<title>ROUNDUP LIVE EDITOR</title>
    <meta name="robots" content="noindex">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/foundation/5.5.3/css/foundation.min.css" />
	<link rel="stylesheet" type="text/css" href="style.css" />
	<style type="text/css">
	#editor_content,
	#editor_view {
		border:1px solid #ccc;
		width:100%;
		height:850px;
		overflow-y:scroll;
		overflow-x:hidden;
	}
	#editor_view { margin-left:3px; }
	#editor_content { margin-right:3px; }
	#editor_content textarea { white-space:nowrap!important; }
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
					<li class="top-top"><a href="index.php"><strong>EDIT ANOTHER NEWSLETTER</strong></a></li>
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
					<p style="margin:.25em auto;color:crimson;font-weight:bold;">LIVE EDIT &mdash; DO NOT FORGET TO SAVE</p>
				</div>
			</div>
		</div>
		<?php if ($file != false) { ?>
			<form id="editor" name="editor" method="post">
				<div id="admin" class="row collapse" style="overflow:hidden;max-width:100%!important;">
					<div class="large-6 columns">
						<script type="text/plain" style="display:block;" id="editor_content"><?php echo $editor_html; ?></script>
					</div>
					<div class="large-6 columns">
						<iframe src="<?php echo './cache/'.$file; ?>" id="editor_view"></iframe>
					</div>
				</div>
				<div class="row" style="overflow:hidden;max-width:60%!important;">
					<div class="large-4 columns" style="padding-top:1em;">
						<a href="#" data-reveal-id="myModal">
							<button class="button" style="width:100%;text-align:center;">HOTKEY REMINDER</button>
						</a>
					</div>
					<div class="large-4 columns" style="padding-top:1em;">
						<button class="button" style="width:100%;text-align:center;" id="download_file_button">DOWNLOAD FILE</button>
					</div>
					<div class="large-4 columns" style="padding-top:1em;">
						<input type="submit" value="UPDATE SAVED FILE" class="button" id="update_file_button"style="width:100%;text-align:center;" disabled />
					</div>
					<input type="hidden" id="editor_html" name="editor_html" value="<?php echo htmlentities($editor_html); ?>" />
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

	<div id="myModal" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
		<h2 id="modalTitle">AWESOME HOTKEYS!</h2>
		<p class="lead">These hotkeys will help you bang out a Roundup lickety-split!</p>
		<ul>
			<li><strong>Ctrl-B / Command-B</strong>: Make highlighted text <strong>bold</strong>.</li>
			<li><strong>Ctrl-I / Command-I</strong>: Make highlighted text <em>italic</em>.</li>
			<li><strong>Ctrl-Alt-B / Command-Ctrl-B</strong>: Insert a &lt;blockquote&gt; template; highlighted text will be wrapped, otherwise a blank template will be inserted with your cursor placed appropriately.</li>
			<li><strong>Ctrl-Alt-G / Command-Ctrl-G</strong>: Insert a suitable tag for an animated GIF!</li>
			<li><strong>Ctrl-Alt-I / Command-Ctrl-I</strong>: Insert an image template snippet.</li>
			<li><strong>Ctrl-Alt-T / Command-Ctrl-T</strong>: Insert a suitable image tag for a Tweet or YouTube video.</li>
			<li><strong>Ctrl-Alt-H / Command-Ctrl-H</strong>: Insert a subheading; highlighted text will be wrapped, otherwise a blank tag will be inserted with your cursor ready to type the content.</li>
			<li><strong>Ctrl-Alt-S / Command-Ctrl-S</strong>: Insert a spacer div (increases vertical gap by the height of a line of text).</li>
			<li><strong>Ctrl-Alt-L / Command-Ctrl-L</strong>: Wrap the selected text with a link (&lt;a&gt;) tag; you will be prompted to past ein the URL you want.</li>
			<li><strong>Ctrl-Alt-P / Command-Ctrl-P</strong>: Insert a source tag (like in What We're Reading); highlighted text will be wrapped, otherwise you will be promted to type or paste a source name.</li>
			<li><strong>Ctrl-Alt-K / Command-Ctrl-K</strong>: Insert a By The Numbers-type number template; highlighted text will be wrapped, otherwise a blank tag will be inserted with your cursor ready to type the number.</li>
			<li><strong>Ctrl-Alt-U / Command-Ctrl-U</strong>: Insert a House Ad for digital subscriptions, with a random graphic.</li>
			<li><strong>Ctrl-Alt-O / Command-Ctrl-O</strong>: Insert a sponsored logo. Must go immediately below the header image, and takes a click-through URL and an image URL.</li>

		</ul>
		<a class="close-reveal-modal" aria-label="Close">&#215;</a>
	</div>

	<script src="//extras.denverpost.com/foundation/js/foundation.min.js"></script>
	<script>
		$(document).foundation();
	</script>
	<script src="./ace/ace.js" charset="utf-8"></script>
	<script>
		function htmlEntities(str) {
			return String(str).replace(/\u00A0/g, '&nbsp;').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
		}
		function getRandomInt(max) {
			return Math.floor(Math.random() * Math.floor(max));
		}
	    var editor = ace.edit("editor_content");
	    editor.setTheme("ace/theme/monokai_bright");
	    editor.getSession().setMode("ace/mode/html");
	    ace.config.loadModule('ace/ext/language_tools');
	    editor.$blockScrolling = Infinity
    	
    	//editor.insertSnippet(snippetText);

	    editor.getSession().setUseWrapMode(true);
	    editor.setShowPrintMargin(false);
	    for (key in editor.keyBinding.$defaultHandler.commandKeyBinding) {
		    if (key == "ctrl-l" && key == "command-l")
		        delete editor.keyBinding.$defaultHandler.commandKeyBinding[key]
		}
	    editor.commands.addCommand({
		    name: 'wrapBold',
		    bindKey: {win: 'Ctrl-B',  mac: 'Command-B'},
		    exec: function(editor) {
	            var origText = editor.session.getTextRange(editor.getSelectionRange());
	            var snippetText = '<strong>' + origText + '</strong>';
		        editor.session.replace(editor.selection.getRange(), snippetText);
		    },
		    readOnly: false
		});
	    editor.commands.addCommand({
		    name: 'wrapEm',
		    bindKey: {win: 'Ctrl-I',  mac: 'Command-I'},
		    exec: function(editor) {
	            var origText = editor.session.getTextRange(editor.getSelectionRange());
	            var snippetText = '<em>' + origText + '</em>';
		        editor.session.replace(editor.selection.getRange(), snippetText);
		    },
		    readOnly: false
		});
	    editor.commands.addCommand({
		    name: 'insertBlockQuote',
		    bindKey: {win: 'Ctrl-Alt-B',  mac: 'Command-Ctrl-B'},
		    exec: function(editor) {
		    	var origText = editor.session.getTextRange(editor.getSelectionRange());
		    	if (origText == '') {
			    	var snippetText = '<blockquote style="font-family:serif;font-weight:bold;font-size:1.2em;color:#555555;border-left: 2px solid #ccc;padding:0 1em;line-height:1.3em;margin-bottom:.2em;">\n$0\n</blockquote>\n<p style="margin-top:0;font-family:serif;color:#800000;text-align:right;font-weight:bold;font-style:italic;">QUOTE_CREDIT_GOES_HERE</p>';
			        editor.insertSnippet(snippetText);
		        } else {
		            var link = '<blockquote style="font-family:serif;font-weight:bold;font-size:1.2em;color:#555555;border-left: 2px solid #ccc;padding:0 1em;line-height:1.3em;margin-bottom:.2em;">\n' + origText + '\n</blockquote>\n<p style="margin-top:0;font-family:serif;color:#800000;text-align:right;font-weight:bold;font-style:italic;">QUOTE_CREDIT_GOES_HERE</p>';
			        editor.session.replace(editor.selection.getRange(), link);
			    }
		    },
		    readOnly: false
		});
		editor.commands.addCommand({
		    name: 'insertGif',
		    bindKey: {win: 'Ctrl-Alt-G',  mac: 'Command-Ctrl-G'},
		    exec: function(editor) {
		    	var snippetText = '<center><img src="$0" aria-hidden="true" width="680" border="0" style="height: auto; background: #ffffff; font-family: sans-serif; width:70%;font-size: 15px; line-height: 100%; color: #555555;display:block;"></center>\n';
		        editor.insertSnippet(snippetText);
		    },
		    readOnly: false
		});
		editor.commands.addCommand({
		    name: 'insertSpacer',
		    bindKey: {win: 'Ctrl-Alt-S',  mac: 'Command-Ctrl-S'},
		    exec: function(editor) {
		    	var snippetText = '<span style="display:block;height:1em;width:100%;"></span>';
		        editor.insertSnippet(snippetText);
		    },
		    readOnly: false
		});
		editor.commands.addCommand({
		    name: 'insertImage',
		    bindKey: {win: 'Ctrl-Alt-I',  mac: 'Command-Ctrl-I'},
		    exec: function(editor) {
		    	var snippetText = '<center>\n<p style="text-align: center;margin:0;padding:0;">\n<a href="STORY_LINK_GOES_HERE" style="text-decoration:none !important;border:none!important;" style="color:#CE4815;font-weight:bold;text-decoration:none;"><img src="$0" aria-hidden="true" width="680" border="0" style="height: auto; background: #ffffff; font-family: sans-serif; width:100%;font-size: 15px; line-height: 100%; color: #555555;display:block;"></a>\n</p>\n</center>\n<p style="font-style:italic;font-size:14px;margin-top:.25em;color:#444444;">CUTLINE_GOES_HERE</p>\n';
		        editor.insertSnippet(snippetText);
		    },
		    readOnly: false
		});
		editor.commands.addCommand({
		    name: 'insertTweet',
		    bindKey: {win: 'Ctrl-Alt-T',  mac: 'Command-Ctrl-T'},
		    exec: function(editor) {
		    	var snippetText = '<center>\n<p style="text-align: center;margin:0;padding:0;">\n<a href="TWEET_LINK_GOES_HERE" style="text-decoration:none !important;border:none!important;" style="color:#CE4815;font-weight:bold;text-decoration:none;"><img src="$0" aria-hidden="true" width="680" border="0" style="height: auto; background: #ffffff; font-family: sans-serif; width:70%;font-size: 15px; line-height: 100%; color: #555555;display:block;margin:0 auto;"></a>\n</p>\n</center>\n';
		        editor.insertSnippet(snippetText);
		    },
		    readOnly: false
		});
	    editor.commands.addCommand({
		    name: 'wrapWithLink',
		    bindKey: {win: 'Ctrl-Alt-L',  mac: 'Command-Ctrl-L'},
		    exec: function(editor) {
		    	var result = prompt('Paste link URL:\n','');
	            var origText = editor.session.getTextRange(editor.getSelectionRange());
	            var link = '<a style="<?php echo $templates[$nl_type]['link_style']; ?>" href="' + result + '" title="' + result + '">' + origText + '</a>';
		        editor.session.replace(editor.selection.getRange(), link);
		    },
		    readOnly: false
		});
		editor.commands.addCommand({
		    name: 'insertSource',
		    bindKey: {win: 'Ctrl-Alt-P',  mac: 'Command-Ctrl-P'},
		    exec: function(editor) {
		    	var origText = editor.session.getTextRange(editor.getSelectionRange());
		    	if (origText == '') {
			    	var result = prompt('Source name:\n','');
		            var link = ' <span style="font-style:italic;font-weight:bold;color:#800000;font-family:serif">&mdash; ' + result + '</span>';
			        editor.session.insert(editor.getCursorPosition(), link)
			    } else {
		            var snippetText = ' <span style="font-style:italic;font-weight:bold;color:#800000;font-family:serif">&mdash; ' + origText + '</span>';
			        editor.session.replace(editor.selection.getRange(), snippetText);
			    }
		    },
		    readOnly: false
		});
		editor.commands.addCommand({
		    name: 'wrapNumber',
		    bindKey: {win: 'Ctrl-Alt-K',  mac: 'Command-Ctrl-K'},
		    exec: function(editor) {
	            var origText = editor.session.getTextRange(editor.getSelectionRange());
	            if (origText == '') {
	            	var snippetText = '<p style="text-align:center;font-size:42px;font-weight:bold;margin:0;">$0</p>';
			        editor.insertSnippet(snippetText);
	            } else {
		            var link = '<p style="text-align:center;font-size:42px;font-weight:bold;margin:0;">' + origText + '</p>';
			        editor.session.replace(editor.selection.getRange(), link);
			    }
		    },
		    readOnly: false
		});
		editor.commands.addCommand({
		    name: 'wrapSubhed',
		    bindKey: {win: 'Ctrl-Alt-H',  mac: 'Command-Ctrl-H'},
		    exec: function(editor) {
	            var origText = editor.session.getTextRange(editor.getSelectionRange());
	            if (origText == '') {
	            	var snippetText = '<h3 style="font-size:1.1em;padding-top:.5em;color:#800000;text-transform:uppercase;">$0</h3>';
			        editor.insertSnippet(snippetText);
	            } else {
		            var link = '<h3 style="font-size:1.1em;padding-top:.5em;color:#800000;text-transform:uppercase;">' + origText + '</h3>';
			        editor.session.replace(editor.selection.getRange(), link);
			    }
		    },
		    readOnly: false
		});
		editor.commands.addCommand({
		    name: 'insertSub',
		    bindKey: {win: 'Ctrl-Alt-U',  mac: 'Command-Ctrl-U'},
		    exec: function(editor) {
		    	var subNum = 1 + getRandomInt(6);
		    	var snippetText = '<span style="display:block;height:1em;width:100%;"></span>\n<center>\n<p style="text-align: center;margin:0;padding:0;"><a href="http://dpo.st/99cents" style="text-decoration:none !important;border:none!important;" style="color:#CE4815;font-weight:bold;text-decoration:none;"><img src="https://extras.denverpost.com/newsletter/subscribe-' + subNum + '.png" aria-hidden="true" width="90%" border="0" style="height: auto; background: #ffffff; font-family: sans-serif; width:90%;margin:0 auto;font-size: 15px; line-height: 100%; color: #555555;display:block;"></a></p>\n</center>';
		        editor.insertSnippet(snippetText);
		    },
		    readOnly: false
		});
		editor.commands.addCommand({
		    name: 'insertSpon',
		    bindKey: {win: 'Ctrl-Alt-O',  mac: 'Command-Ctrl-O'},
		    exec: function(editor) {
		    	var snippetText = '<center>\n<p class="leader">Presented By</p>\n<center>\n<p style="text-align: center;margin:0;padding:0;">\n<center>\n<a href="CLICK_THROUGH_URL_GOES_HERE" style="text-decoration:none !important;border:none!important;"><img src="SPONSOR_LOGO_IMAGE_URL_GOES_HERE" aria-hidden="true" width="220" border="0" style="height: auto; background: #ffffff; font-family: sans-serif; width:220px;font-size: 15px; line-height: 100%; color: #555555;display:block;"></a></center>\n</p>\n</center>\n</center>';
		        editor.insertSnippet(snippetText);
		    },
		    readOnly: false
		});


	</script>
	<script>
		var unsaved = false;
        $(document).ready(function(){
        	editor.getSession().on('change', function(e) {
			    $("#editor_view").contents().find('html').html(editor.getValue());
			    $("#editor_html").val(htmlEntities(editor.getValue()));
			    if (unsaved == false) {
				    $('#update_file_button').removeAttr('disabled');
				    unsaved = true;
				}
			});
			document.getElementById('download_file_button').onclick = function() {
				filename = './download.php?filename=<?php echo str_replace('.html','',$file); ?>';
				window.open(filename, '_blank');
			}
			document.getElementById('update_file_button').onclick = function() {
				unsaved=false;
			}
			window.onbeforeunload = function(){
				if (unsaved) {
					return 'Are you sure you want to leave?';
				}
			};
        });
	</script>
	<script>
		function adjustHeight(){
			var window_height = (window.innerHeight-175)+'px';
			$('#editor_content').css('height',window_height);
			$('#editor_view').css('height',window_height);
		}
		adjustHeight();
		$(window).resize(function(){
			adjustHeight();
		})
	</script>
</body>
</html>