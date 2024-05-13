<?php

// Global readonly, including when auth is not being used
$global_readonly = false;

// Root path for file manager
// use absolute path of directory i.e: '/var/www/folder' or $_SERVER['DOCUMENT_ROOT'].'/folder'
$root_path = '/var/www/artifactory/files';

// Root url for links in file manager.Relative to $http_host. Variants: '', 'path/to/subfolder'
// Direct links Will not working if $root_path will be outside of server document root. Public direct links will work everywhere
$root_url = '/artifactory/files';

// Root URL path of site on server
$root_link = '/artifactory';

// Login user name and password
// Users: array('Username' => 'Password', 'Username2' => 'Password2', ...)
// Generate secure password hash - https://tinyfilemanager.github.io/docs/pwd.html
$auth_users = array(
    'admin' => '$2y$10$/K.hjNr84lLNDt8fTXjoI.DBp6PpeyoJ.mGwrrLuCZfAwfSAGqhOW', //admin@123
    'user' => '$2y$10$Fg6Dz8oH9fPoZ2jJan5tZuv6Z4Kp7avtQ9bDfrdRntXtPeiMAZyGO' //12345
);

// Tokens for public direct link generation
// Public direct links have format: dlink.php?u=[USER]&t=[TOKEN]&p=[PATH] , PATH - is relative path in user directory, it is not absolute path on server
// For access to file, user may not be logged in. For download from public direct link, data directory may not be in wwwroot
$auth_users_directlink_tokens = array(
    'admin' => 'mM1z914mqVn',
    'user' => '581Qqzm4'
);

// Readonly users
// e.g. array('users', 'guest', ...)
$readonly_users = array(
    'user'
);

// user specific directories
// if not specified -> $root_path will be used
// array('Username' => 'Directory path', 'Username2' => 'Directory path', ...)
$directories_users = array(
   'user' => '/home/user'
);

?>
