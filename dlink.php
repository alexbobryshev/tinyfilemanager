<?php

include 'users.php';

function fm_get_file_mimes($extension) {
    $fileTypes['swf'] = 'application/x-shockwave-flash';
    $fileTypes['pdf'] = 'application/pdf';
    $fileTypes['exe'] = 'application/octet-stream';
    $fileTypes['zip'] = 'application/zip';
    $fileTypes['7z'] = 'application/zip';
    $fileTypes['doc'] = 'application/msword';
    $fileTypes['xls'] = 'application/vnd.ms-excel';
    $fileTypes['ppt'] = 'application/vnd.ms-powerpoint';
    $fileTypes['gif'] = 'image/gif';
    $fileTypes['png'] = 'image/png';
    $fileTypes['jpeg'] = 'image/jpg';
    $fileTypes['jpg'] = 'image/jpg';
    $fileTypes['webp'] = 'image/webp';
    $fileTypes['avif'] = 'image/avif';
    $fileTypes['rar'] = 'application/rar';

    $fileTypes['ra'] = 'audio/x-pn-realaudio';
    $fileTypes['ram'] = 'audio/x-pn-realaudio';
    $fileTypes['ogg'] = 'audio/x-pn-realaudio';

    $fileTypes['wav'] = 'video/x-msvideo';
    $fileTypes['wmv'] = 'video/x-msvideo';
    $fileTypes['avi'] = 'video/x-msvideo';
    $fileTypes['asf'] = 'video/x-msvideo';
    $fileTypes['divx'] = 'video/x-msvideo';

    $fileTypes['mp3'] = 'audio/mpeg';
    $fileTypes['mp4'] = 'video/mp4';
    $fileTypes['mkv'] = 'video/*';
    $fileTypes['mpeg'] = 'video/mpeg';
    $fileTypes['mpg'] = 'video/mpeg';
    $fileTypes['mpe'] = 'video/mpeg';
    $fileTypes['mov'] = 'video/quicktime';
    $fileTypes['swf'] = 'video/quicktime';
    $fileTypes['3gp'] = 'video/quicktime';
    $fileTypes['m4a'] = 'video/quicktime';
    $fileTypes['aac'] = 'video/quicktime';
    $fileTypes['m3u'] = 'video/quicktime';

    $fileTypes['php'] = ['application/x-php'];
    $fileTypes['html'] = ['text/html'];
    $fileTypes['txt'] = ['text/plain'];
    //Unknown mime-types should be 'application/octet-stream'
    if(empty($fileTypes[$extension])) {
      $fileTypes[$extension] = ['application/octet-stream'];
    }
    return $fileTypes[$extension];
}


$url = $_SERVER['QUERY_STRING'];

$username = '';
if (isset($_GET['u'])) {
  $username = $_GET['u'];
}

$token = '';
if (isset($_GET['t'])) {
  $token = $_GET['t'];
}

$path = '';
if (isset($_GET['p'])) {
  $path = $_GET['p'];
}

if (!isset($auth_users_directlink_tokens[$username]) || $auth_users_directlink_tokens[$username] != $token) {
  sleep(0.6);
  http_response_code(403);
  die();
}

if ( isset($directories_users[$username]) ) {
  $root_base = $directories_users[$username] . '/';
} else {
  $root_base = $root_path . '/';
}


$filename = urldecode($path);
$filename = str_replace('./','',$filename);
$filename = str_replace('../','',$filename);
$filename = str_replace('.\\','',$filename);
$filename = str_replace('..\\','',$filename);


$filename = $root_base . $filename;

$handle = fopen($filename, "rb");
if (!$handle) {
  http_response_code(404);
  die();
}

$path_parts = pathinfo($filename);
$return_name = $path_parts['filename'];
$extension = '';

if ( isset( $path_parts['extension'] )) {
  $extension = $path_parts['extension'];
  $return_name = $return_name . '.' . $extension;
}

$contentType = fm_get_file_mimes($extension);

header("Cache-Control: public");
header("Content-Description: File Transfer");
header("Content-Disposition: attachment; filename=$return_name");
header("Content-Type: $contentType");
header("Content-Transfer-Encoding: binary");
header("Accept-Ranges: bytes");

$range = 0;
$size = filesize($filename);

if(is_array($contentType)) {
  $contentType = implode(' ', $contentType);
}

$begin=0;
$end=$size;

$no_end_request = true;
$max_part_size = 65536;

if (isset($_SERVER['HTTP_RANGE'])) {
  $ranges = explode(',', substr($_SERVER['HTTP_RANGE'], 6));
  foreach ($ranges as $range) {
    $parts = explode('-', $range);
    $begin = $parts[0]; // If this is empty, this should be 0.
    if (!empty($parts[1])) {
      $no_end_request = false;
      $end = $parts[1]; // If this is empty or greater than than filelength - 1, this should be filelength - 1.
    }

    if ($begin > $end) {
      header('HTTP/1.1 416 Requested Range Not Satisfiable');
      header('Content-Range: bytes */' . $size ); // Required in 416.
      exit;
    }

    break;
  }

  if ($no_end_request && $end - $begin > $max_part_size) {
    $end = $begin + $max_part_size;
  }

  header("HTTP/1.1 206 Partial Content");
  header("Content-Length: ".($end-$begin));
  header("Content-Range: bytes $begin-" .($end-1) ."/$size");
} else {
  $size2 = $size - 1;
  header("Content-Range: bytes 0-$size2/$size");
  header("Content-Length: " . $size);
}

$cur = $begin;
fseek($handle, $begin, 0);

while(!feof($handle) && $cur < $end && (connection_status() == 0)) {
  $cur_read = min(4096, $end-$cur);
  print fread($handle, $cur_read);
  $cur += $cur_read;
}

fclose($handle);
?>
