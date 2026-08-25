<head>
<title>Unzip! PHP Script</title>
<meta name="author" content="Alfatah Nesab">
<meta name="keywords" content="unzip">
<meta name="DESCRIPTION" content="unzip zipped file to current directory. On your hosting server/localhost etc.">
</head> 
<!-- parsing stops here -->
<?php
		
error_reporting(E_ALL);
ini_set('display_errors','On');

  function getDirectoryList ($directory) 
  {

    // create an array to hold directory list
    $results = array();

    // create a handler for the directory
    $handler = opendir($directory);

    // open directory and walk through the filenames
    while ($file = readdir($handler)) {

      // if file isn't this directory or its parent, add it to the results
      if ($file != "." && $file != "..") {
	$ext = substr($file, strlen($file)-3, strlen($file));
	if($ext == 'zip')
        	$results[] = $file;
      }

    }

    // tidy up: close the handler
    closedir($handler);

    // done!
    return $results;

  }

$list = getDirectoryList(dirname(__FILE__));


if(isset($_GET['file_name']) && !empty($_GET['file_name']) && file_exists($_GET['file_name']))
{
	  $zip = new ZipArchive;

	//$zipFilename = substr($_GET['file_name'], 0, -4);
	  $zipped = $zip->open($_GET['file_name']);

	  if ( $zipped == TRUE) {

	  if(!$zip->extractTo(dirname(__FILE__))){
			echo "Unzip failed";
		}
	  $zip->close();
	
	  echo "File ".$_GET['file_name'].' unzip successfully.';
	}else{
		echo "Unable to unzip: failed.";
	}
}
?>
<br/>
<br/>
<br/>
<br/>
<?php

if(count($list) == 0 )
	echo "No Zip files available in the directory ".dirname(__FILE__);
else
{
	echo "Zip files of directory: ".dirname(__FILE__).'<br/>';
	echo "Select a zip file & submit ok button to unzip the file.<br/><br/><br/>";
?>

	<form method="get" action="unzip.php">

		<select name="file_name">
			
			<?php
			foreach($list as $item):
				echo "<option>".$item."</option>";
			endforeach;
			?>
		</select>
		
		<input type="submit" value="Ok">
	</form>

<?php
}
?>