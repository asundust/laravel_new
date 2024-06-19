<?php

require_once __DIR__ . '/src/functions.php';

$readme = file_get_contents(__DIR__ . '/README.md');

$functions = file_get_contents(__DIR__ . '/src/functions.php');

preg_match_all('/function\s+(\w+)\s*\((.*?)\)/', $functions, $matches);

$content = '';
foreach ($matches[1] as $index => $functionName) {
    $doc = (new ReflectionFunction($functionName))->getDocComment();
    $doc = explode("\n", $doc);
    $doc = trim($doc[1], ' *');

    $func    = $matches[0][$index];
    $content .= <<<EOT
// $doc$func


EOT;
}

$content = preg_replace('/\/\/ functions start(.*?)\/\/ functions end/s', "// functions start\n\n$content// functions end", $readme);

file_put_contents(__DIR__ . '/README.md', $content);


echo 'Done.' . PHP_EOL;
