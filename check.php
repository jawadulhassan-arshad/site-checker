<?php
/**
 * PHP/cURL function to check a web site status. If HTTP status 
 * is not 200 or 302, or the requests takes longer than 10 
 * seconds, the website is unreachable.
 * 
 * Follow me on Twitter: @jd_arshad
 * For work hire me https://www.upwork.com/freelancers/~01b2defe3811daddbd. Thanks!
 *
 * @param string $url URL that must be checked
 */
 
//ini_set('display_errors', 1);

//ini_set('display_startup_errors', 1);
 
 // Report all errors

//error_reporting(E_ALL);

//ini_set("error_reporting", E_ALL);

ini_set('max_execution_time', '900'); //300 seconds = 5 minutes

//ini_set('memory_limit', '512MB');

$source=intval($_REQUEST['src']);

function get_website( $url, $search_string = '')
{
	// Create a stream
$opts = array(
  'http'=>array(
    'method'=>"GET",
    'header'=>"Accept-language: en\r\n" .
/*		    "--user-agent:jawad-crawler\r\n" . */
              "Cookie: foo=bar\r\n"
  )
);

$context = stream_context_create($opts);

// Open the file using the HTTP headers set above
$file = file_get_contents($url, false, $context,0,100);

sleep(3);

/*
if (str_contains($file, $search_string)) { 
    echo 'true';
}*/

echo $file = substr(strip_tags($file),0,50);

$pos = strpos($file, $search_string);
$mystring='';
// Note our use of ===.  Simply == would not work as expected
// because the position of 'a' was the 0th (first) character.
if ($pos === false) {
	
	
    echo "The string '$search_string' was not found in the string '$mystring'";
	return "Website offline :".$url;
} else {
    echo "The string '$search_string' was found in the string '$mystring'";
    echo " and exists at position $pos";
	return "Website Online :".$url;
}


}


function url_test( $curl, $url, $search_string = '' ) {
	$timeout = 30;
//	$ch = curl_init();
	$ch = $curl;
	curl_setopt ( $ch, CURLOPT_URL, $url );
	curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt ( $ch, CURLOPT_TIMEOUT, $timeout );
	
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");   
	 
	// Return follow location true
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);

	$userAgent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.114 Safari/537.36';
	
//	$userAgent = 'jawad-crawler';

	curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
	
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120);
	
	curl_setopt($ch, CURLOPT_TIMEOUT, 120);
			
	
	$http_respond = curl_exec($ch);
	$http_respond = trim( strip_tags( $http_respond ) );
	
	$http_code = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
	
	// Getinfo or redirected URL from effective URL
	$redirectedUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);

	
	echo $url.' = '.$redirectedUrl.' = code : '.$http_code. ' = keyword : '.$search_string;
	echo '<br />';
	
	$http_respond = substr($http_respond, 0, 100);
	//echo '\n'; 
	//echo $http_respond;
	//echo '\n\n\n';
//	echo '<hr />';
	
	if( $http_respond!='' && ( $http_code!='200' || $http_code!='302' ))
	{
		
		$pos = strpos($http_respond, $search_string);

		// Note our use of ===.  Simply == would not work as expected
		// because the position of 'a' was the 0th (first) character.
		if ($pos === false)
		{
			
		}
		else
		{

	echo '\n'; 
	echo $http_respond;
	echo '\n\n\n';
	
			echo 'keyword found inside html';
			$http_code ="200";
		}
	
		
	}

	
	if ( ( $http_code == "200" ) || ( $http_code == "302" ) ) {
		return true;
	} else {
		// you can return $http_code here if necessary or wanted
		return false;
	}
	
	//curl_close( $ch );
}


// ============================================================

$website_array=array();
$i=0;

$handle = fopen(__DIR__ ."/check.txt", "r");
if ($handle) {
    while (($line = fgets($handle)) !== false) {
        // process the line read.
		//print_r($line);
		
		if(trim($line)!='')
		{
		$string_array=null;
		
		$string_array = explode('|',$line);
		
		$website_array[$i]['url']=$string_array[0];
		
		$website_array[$i]['keyword']=$string_array[1];
		
		$i++;
		}
		
    }

    fclose($handle);
} else {
    // error opening the file.
} 
/*
echo '<pre>';
print_r($website_array);
echo '</pre>';
*/

$email_string='';

echo '<hr />';
echo '<h2>Pinging Websites :</h2>';
// ============================================================
$count_website= count($website_array);

$error_website=0;

echo $email_string= 'Total Website Count :'.$count_website;

		
echo '<pre>';	

$ch = curl_init();

for($i=0; $i < $count_website; $i++)
{
	$website_url  =  $search_string = '';
	//url_test($website_array[$i]['url']);
	$website_url = $website_array[$i]['url'];
	
	$search_string = $website_array[$i]['keyword'];
	
if( !url_test($ch, $website_url, $search_string ) ) {
	echo '<br />';
	echo $website_url ." is down!";
	echo '<br />';echo '<br />';
	
	$email_string .= "Problem: $website_url is down\n";
	$error_website++;
	
} else {
	echo '<br />';
	echo $website_url ." functions correctly.";
	echo '<br />';echo '<br />';
}


echo 'i count is '.$i;
echo '<br />';

/*if($i==18 && $source==1)
{

//$email_string .= get_website($website_url,$search_string);
	
	//exit;
	break 1;
}
*/

 sleep(3); // Wait 1 second before retrying

}

curl_close($ch);
// ending curl here

echo '</pre>';


echo $email_string;

if ($email_string != '' && $error_website > 0)
{

		$to_emails='jawadakr[at]yahoo';
	
	mail($to_emails,'Site Check Problem',$email_string);
	
	
}

// simple usage:
//$website = "www.yahoo.com";
//$website = "https://gmail.com";

	
/*if( !url_test( $website ) ) {
	echo $website ." is down!";
} else {
	echo $website ." functions correctly.";
}*/
?>
