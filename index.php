<?php
require 'vendor/autoload.php';
$Q = \Robot\Robot::init();
$Q->setData();
$Q->sendMsg();