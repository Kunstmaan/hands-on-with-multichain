<?php

namespace Multichain\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateAccountCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('create-account')
            ->setDescription('Creates an account')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

    }
}