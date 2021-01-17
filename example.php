<?php

use wbswbs\Template;

require '/vendor/autoload.php';

$tpl = new Template('/template/example.html');
$tpl->setContent([
                     'title' => 'My funny Website',
                     'content' => 'My funny Content'
                 ]
);
die($tpl->getOutput());