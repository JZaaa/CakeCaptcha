<?php
use Cake\Core\Configure;

$config = 'captcha';

$configPath = CONFIG . $config . '.php';

if (file_exists($configPath)) {
    Configure::load($config, 'default');
}
