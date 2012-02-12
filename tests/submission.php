<?php

error_reporting(E_ALL);

function url_request($method, $url, $data, $head = '') {
 
    // parse the given URL
    $url = parse_url($url); 
 
    // extract host and path:
    $host = $url['host'];
    $path = $url['path'];
 
    // open a socket connection on port 80 - timeout: 30 sec
    $fp = fsockopen($host, 80, $errno, $errstr, 30);
 
    if ($fp){
 
        // send the request headers:
        fputs($fp, "$method $path HTTP/1.1\r\n");
	fputs($fp, "Host: $host\r\n");
        fputs($fp, "$head");
 
 
        fputs($fp, "Content-type: text/x-yaml+user\r\n");
        fputs($fp, "Content-length: ". strlen($data) ."\r\n");
        fputs($fp, "Connection: close\r\n\r\n");
        fputs($fp, $data);
 
        $result = ''; 
        while(!feof($fp)) {
            // receive the results of the request
            $result .= fgets($fp, 128);
        }
    }
    else { 
        return array(
            'status' => 'err', 
            'error' => "$errstr ($errno)"
        );
    }
 
    // close the socket connection:
    fclose($fp);
 
    // split the result header from the content
    $result = explode("\r\n\r\n", $result, 2);
 
    $header = isset($result[0]) ? $result[0] : '';
    $content = isset($result[1]) ? $result[1] : '';
 
    // return as structured array:
    return array(
        'status' => 'ok',
        'header' => $header,
        'content' => $content
    );
}

$post_data = '
<user uri="/exerciser/1">
	<user_id>1</user_id>
	<username>Martin</username>
	<age>29</age>
	<weight>78</weight>
	<gender>Male</gender>
</user>';
$head = '';//Authorization: Basic ' . base64_encode('1:password');
$result = url_request('POST', 'http://localhost/exerciser/', $post_data, $head);

if ($result['status'] == 'ok'){

    // Print headers 
    echo $result['header']; 

echo '<hr />';
	$headers = explode(':', $result['header']);
 	echo var_dump($headers);
    echo '<hr />';
 
    // print the result of the whole request:
    echo $result['content'];
 
}
else {
    echo 'A error occured: ' . $result['error']; 
}


?>
