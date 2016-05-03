<?php

use Mashbo\ConsoleToolkit\Arguments\ArgumentList;
use Mashbo\ConsoleToolkit\Arguments\UnixStyleArgumentDataMapper;
use Mashbo\ConsoleToolkit\Terminal;
use Mashbo\Mashbot\Extensions\ProcessTaskRunnerExtension\Process\SymfonyProcessRunner;
use Mashbo\Mashbot\Extensions\ProcessTaskRunnerExtension\ProcessTaskRunnerExtension;
use Mashbo\Mashbot\Extensions\SshTaskRunnerExtension\SshTaskRunnerExtension;
use Mashbo\Mashbot\TaskRunner\Hooks\AfterTask\AfterTaskContext;
use Mashbo\Mashbot\TaskRunner\TaskRunner;
use Psr\Log\NullLogger;

$autoloadFile = __DIR__ . DIRECTORY_SEPARATOR . '../vendor/autoload.php';
if (file_exists($autoloadFile)) {

    require_once $autoloadFile;

    $terminal = new Terminal(STDIN, STDOUT);
    $mapper = new UnixStyleArgumentDataMapper(
        [
            0 => 'command',
            'host'              => 'host',
            'remote.host'       => 'remoteSshHost',
            'remote.port'       => 'remoteSshPort',
            'remote.user'       => 'remoteSshUser',
            'remote.db.name'    => 'remoteDbName',
            'remote.db.user'    => 'remoteDbUser',
            'remote.path'       => 'remotePath',
            'local.path'        => 'localPath',
            'local.db.name'     => 'localDbName',
            'local.db.user'     => 'localDbUser',

            'source.host' => 'sourceHost',
            'target.host' => 'targetHost'
        ]
    );
    $cliArgs = $mapper->resolve(ArgumentList::fromArgv($_SERVER['argv']));

    $runner = new TaskRunner(new NullLogger());
    $runner->extend(new ProcessTaskRunnerExtension(new SymfonyProcessRunner()));
    $runner->extend(new SshTaskRunnerExtension());
    $runner->extend(new Mashbo\Mashbot\Extensions\EnvToolTaskRunnerExtension\EnvToolTaskRunnerExtension());

//    $runner->after('env:database:import', function (AfterTaskContext $context) use ($cliArgs) {
//        $context
//            ->runner()
//            ->invoke(
//                'process:command:run', [
//                    'command' => sprintf('wp-cli search-replace %s %s', escapeshellarg($cliArgs['sourceHost']), escapeshellarg($cliArgs['targetHost']))
//            ]);
//    });

    \Assert\Assertion::keyExists($cliArgs, 'command');

    switch ($cliArgs['command']) {
        case 'pull':

            \Assert\Assertion::keyExists($cliArgs, 'remoteSshHost'       );
            \Assert\Assertion::keyExists($cliArgs, 'remoteSshUser'       );
            \Assert\Assertion::keyExists($cliArgs, 'remoteSshPort'       );
            \Assert\Assertion::keyExists($cliArgs, 'remoteDbName'        );
            \Assert\Assertion::keyExists($cliArgs, 'remoteDbUser'        );
            \Assert\Assertion::keyExists($cliArgs, 'remotePath' );
            \Assert\Assertion::keyExists($cliArgs, 'localPath'  );
            \Assert\Assertion::keyExists($cliArgs, 'localDbName'         );
            \Assert\Assertion::keyExists($cliArgs, 'localDbUser'         );

            $runner->invoke(
                'env:sync:pull', [

                    'remote' => [
                        'connection' => [
                            'host' => $cliArgs['remoteSshHost'],
                            'user' => $cliArgs['remoteSshUser'],
                            'port' => $cliArgs['remoteSshPort']
                        ],
                    ],
                    'databases' => [
                        [
                            'remote' => ['name' => $cliArgs['remoteDbName'], 'user' => $cliArgs['remoteDbUser']],
                            'local' => ['name' => $cliArgs['localDbName'], 'user' => $cliArgs['localDbUser']]
                        ]
                    ],
                    'paths' => [
                        [
                            'remote' => $cliArgs['remotePath'],
                            'local' => $cliArgs['localPath']
                        ]
                    ]
                ]
            );
            break;
        default:
            throw new \InvalidArgumentException("Invalid command");

    }

    exit;
}

throw new \RuntimeException("Composer autoload file not found.");
