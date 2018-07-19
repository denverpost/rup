<?php
/**
 * Returns the contents of a file as a download (rather than displaying them, since it's HTML files we're dealing with)
 */
	if(!empty($_GET['filename'])) {
	    // Fetch the file info.
	    $filePath = realpath('./cache/'.$_GET['filename'].'.html');

	    if(file_exists($filePath)) {
	        $fileName = basename($filePath);
	        $fileSize = filesize($filePath);

	        // Output headers.
	        header("Cache-Control: public");
		    header("Content-Transfer-Encoding: binary");
	        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($path)) . ' GMT');
	        header('Accept-Ranges: bytes');
	        header("Content-Length: ".$fileSize);
	        header('Content-Encoding: none');
	        header("Content-Type: text/html");
	        header("Content-Disposition: attachment; filename=".$fileName);

	        // Output file.
	        readfile($filePath);                   
	        exit();
	    }
	    else {
	        die('The provided file path is not valid.');
	    }
	}
?>
<script>
	// close when done
	window.close();
</script>