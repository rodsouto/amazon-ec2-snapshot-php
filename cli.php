<?php

include __DIR__.'/vendor/autoload.php';

/**
 * Usage
 *
 * cli.php describe
 * cli.php create
 */

if(empty($argv[1])) {
    die('Empty cli command.');
}

$configFile = __DIR__.'/config.php';

if (!file_exists($configFile)) {
    die('Missing config file');
}

$AWS_KEY = $AWS_SECRET = $AWS_VOLUME_ID = $AWS_VERSION = $AWS_REGION = $SNAPSHOT_DESCRIPTION = null;

extract(include $configFile, EXTR_OVERWRITE);

$clientConfig = [
    'version' => $AWS_VERSION,
    'region' => $AWS_REGION,
    'credentials' => [
        'key' => $AWS_KEY,
        'secret'  => $AWS_SECRET,
    ]
];

$ec2 = new Aws\Ec2\Ec2Client($clientConfig);

switch($argv[1]) {
    case 'describe':
        $describeConfig = [
            'Filters' => [
                ['Name' => 'volume-id', 'Values' => [$AWS_VOLUME_ID]],
            ]
        ];
        var_export($ec2->describeSnapshots($describeConfig)->toArray()['Snapshots']);
        break;
    case 'create':
        $createConfig = [
            'DryRun' => false,
            'VolumeId' => $AWS_VOLUME_ID,
            'Description' => $SNAPSHOT_DESCRIPTION,
        ];
        var_export($ec2->createSnapshot($createConfig)->toArray());
        break;
    default:
        echo 'Invalid command';
        break;
}