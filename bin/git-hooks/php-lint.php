#!/usr/bin/env php
<?php

$output = array();
$return = 0;
exec('git rev-parse --verify HEAD 2> /dev/null', $output, $return);
$against = $return == 0 ? 'HEAD' : '4b825dc642cb6eb9a060e54bf8d69288fbee4904';

exec("git diff-index --cached --name-only {$against}", $output);

$filename_pattern = '/\.(php|gpx)$/';
$exit_status = 0;

foreach ($output as $file) {
    if (!preg_match($filename_pattern, $file)) {
        // don't check files that aren't PHP
        continue;
    }

    if(!file_exists($file)) {
        // if the file has been moved or deleted,
        // the old filename should be skipped
        continue;
    }

    $lint_output = array();
    exec("php -l " . escapeshellarg($file), $lint_output, $return);
    if ($return == 0) {
        continue;
    }
    echo implode("\n", $lint_output), "\n";
    $exit_status = 1;
}

exit($exit_status);
