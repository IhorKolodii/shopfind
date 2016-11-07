<?php
/**
 * Settings for application
 * 
 */
return [
    /* Database */
    'db-driver' => 'mysql',
    'db-host' => 'localhost',
    'db-dbname' => 'codeit_test',
    'db-user' => 'codeit_test',
    'db-pass' => '12345',
    'db-itemtable' => 'items',
    'db-brandtable' => 'brands',
    /* Paths */
    'includes-path' => 'includes/',
    'template-path' => 'includes/templates/',
    'js-path' => 'static/js/',
    'css-path' => 'static/css/',
    /* File names */
    'layout-template-name' => 'main',
    'js' => ['jquery','bootstrap','main'],
    'css' => ['bootstrap', 'style'],
    /* Data generation */
    'brands-list' => 'brands.txt',
    'words-list' => 'words.txt',
    'dress-sizes' => ['S', 'M', 'L'],
    'shoes-sizes' => [35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45],
];