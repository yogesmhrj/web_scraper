<?php
//get url from user
$line = readline("URL : ");
//dump variables
$user_url = trim(readline_info()['line_buffer']);
if (!filter_var($user_url, FILTER_VALIDATE_URL)) {
	die("Not a valid URL : ".$user_url."\n*****Please Input a valid URL.*****\n")	;
}
	
$url = trim($user_url);

$html = file_get_contents($url);

echo "********** Start of task **********\n";
//create a container to store the images
$images = array();
//set the preg match patterns to search in the web url
$preg_match_patterns = ['|<img.*?src=[\'"](.*?)[\'"].*?>|i',
			'|.*?data-lazyload=[\'"](.*?)[\'"].*?>|i'];
//loop to go through all the patterns
foreach($preg_match_patterns as $pattern){
	preg_match_all($pattern,$html, $matches ); 
	$images = array_merge($images,array_unique($matches[1]));
}

echo count($images)." files found to scrape out. \n";

//we have got the images for the url, now start downloading the images

//the path of the saved images
//get the domain of the url
$domain = current(explode("/",explode("://",$url)[1]));
//get the file name of the web page
$file = end(explode("/",$url));
//then finally prepare the path
$path = "scraper/".$domain."/".$file."/images/";
echo "Files will be saved to ".$path."\n\n";
//ensure the directory structure
if(!file_exists($path)){
	if (!mkdir($path, 0777, true)) {
    	die('Failed to create directory : '.$path);
	}
}
//save images locally
foreach ($images as $image) {
	echo "Working on -> ".$image."\n";
	//get the filename
	$filename = end(explode("/", $image));
	$success = file_put_contents($path.$filename,file_get_contents($image));
	if($success){
		$kb = (int)($success / 1024);
		echo "              Success : [$kb KB] saved!\n";
	}else{
		echo "              Failed \n";
	}
}

echo "********** End of task **********\n";
?>
