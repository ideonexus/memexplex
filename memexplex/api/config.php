<?php

/*
Example configuration. All fields optional except the dataset name.
*/

$args = array( 
            'name' => 'memexplex_db',
            'username' => 'DBUSER_HERE',
            'password' => 'DBPASSWORD_HERE',
            'server' => 'localhost',
            'port' => 3306,
            'type' => 'mysql',
            'table_blacklist' => array(),
            'column_blacklist' => array('email','password','publish_by_default')
);

register_db_api( 'api', $args );

