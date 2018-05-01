<?php
	if(!empty($_GET['filename'])) {
	    // Fetch the file info.
	    $filePath = realpath('./cache/'.$_GET['filename'].'.html');

	    if(file_exists($filePath)) {
	        $fileName = basename($filePath);
	        $fileSize = filesize($filePath);

	        // Output headers.
	        header("Cache-Control: public");
		    header("Content-Description: File Transfer");
		    header("Content-Transfer-Encoding: binary");
	        header("Content-Type: text/html");
	        header("Content-Length: ".$fileSize);
	        header("Content-Disposition: attachment; filename=".$fileName);

	        // Output file.
	        readfile ($filePath);                   
	        exit();
	    }
	    else {
	        die('The provided file path is not valid.');
	    }
	}
?>

