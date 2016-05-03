#!/usr/bin/env php
<?php

$root = __DIR__;
$buildDir = $root.'/build';

$targetPharName = 'sync.phar';
$outputFile = __DIR__ . '/' . $targetPharName;

if (file_exists($outputFile)) {
    echo "Removing $outputFile\n";
    unlink($outputFile);
}

$phar = new Phar($outputFile, 0, $targetPharName);

$phar->buildFromIterator(
    new CallbackFilterIterator(
        new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($root, \FilesystemIterator::SKIP_DOTS)
        ),
        function(\SplFileInfo $info) use ($root) {
            $relativePath = str_replace($root, '', $info->getRealPath());

            $include = !(
                0 === strpos($relativePath, '/.git') ||
                0 === strpos($relativePath, '/.idea') ||
                0 === strpos($relativePath, '/.vagrant') ||
                0 === strpos($relativePath, '/tests') ||
                0 === strpos($relativePath, '/ci') ||
                0 === strpos($relativePath, '/vendor/phpunit') ||
                0 === strpos($relativePath, '/vendor/phpspec')
            );

            return $include;
        }
    ),
    $root
);

exec('git describe --always', $version);
$version = implode($version);

//echo "Replacing @package_version@ with $version in bin/sync\n";
//$phar['bin/sync'] = str_replace('@package_version@', $version, $phar['bin/sync']->getContent());


echo "Configuring stub for bin/sync\n";

$stub = "#!/usr/bin/env php
<?php

Phar::mapPhar('sync.phar');

require 'phar://sync.phar/bin/sync.php';
__HALT_COMPILER();
EOF";

$phar->setStub($stub);

echo "Marking $outputFile as executable\n";
exec('chmod +x ' . escapeshellarg($outputFile));
