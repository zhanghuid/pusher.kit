<?php

declare(strict_types=1);

namespace Huid\Pusher\Command;

use Huid\Pusher\Support\SenderFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use function Huid\Pusher\Support\record;

class PusherCommand extends Command
{
    protected $config = [];

    protected function initConfig()
    {
        $this->config = [
            'set' => [
                'name' => 'apns',
                'description' => 'send message to your iphone',
                'help' => 'eg: php cli apns ',
            ],
            'add' => [
                'argument' => [
                    [
                        'name' => 'message',
                        'mode' => InputArgument::REQUIRED,
                        'description' => 'The push content',
                        'default' => null,
                    ],
                    [
                        'name' => 'device_token',
                        'mode' => InputArgument::REQUIRED,
                        'description' => 'The app uniq device token',
                        'default' => null,
                    ],
                ],
                'option' => [
                    // [
                    //     'name' => 'key_path',
                    //     'mode' => InputArgument::OPTIONAL,
                    //     'description' => 'The password of cert',
                    //     'default' => config('apns.key_path'),
                    // ],
                    // [
                    //     'name' => 'key_password',
                    //     'mode' => InputArgument::OPTIONAL,
                    //     'description' => 'The password of cert',
                    //     'default' => config('apns.key_password'),
                    // ],
                    [
                        'name' => 'key_type',
                        'mode' => InputArgument::OPTIONAL,
                        'description' => 'The type of cert',
                        'default' => config('apns.key_type'),
                    ],
                    [
                        'name' => 'sandbox',
                        'mode' => InputArgument::OPTIONAL,
                        'description' => 'The cert type for send message',
                        'default' => config('apns.sandbox')
                    ],
                ],
            ]
        ];
    }

    protected function configure()
    {
        $this->initConfig();

        foreach ($this->config as $methodPrefix => $methodConfig) {
            foreach ($methodConfig as $methodPrefix2=> $args) {
                $mtd = $methodPrefix . ucfirst($methodPrefix2);

                if (is_array($args)) {
                    foreach ($args as $item) {
                        $this->{$mtd}(...$item);
                    }
                } else {
                    $this->{$mtd}($args);
                }
            }
        }
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {

        $args = [];
        foreach ($this->config['add'] as $mtd => $items) {
            foreach ($items as $item) {
                $args[] = $input->{"get".ucfirst($mtd)}($item['name']);
            }
        }

        // record([
        //     $message, $deviceToken,
        //     $keyType, $certType, config('apns')
        // ]);
        try {
            SenderFactory::create($args)
                ->withBuilder()
                ->withReceiver()
                ->withNotification()
                ->execute();

            $output->writeln('<info>success ~</info>');
            return 0;
        } catch (\Exception $e) {
            $output->writeln("<error>error: </error>" . $e->getMessage());
            return 1;
        }
    }
}
