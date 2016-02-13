<?php

namespace Multichain\Console\Command;

use Multichain\Console\Shared\InputOutput;
use Multichain\Console\Shared\MultichainCommands;
use Multichain\Console\Shared\MysqlCommands;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListAddressesCommand extends Command
{
    use MultichainCommands;
    use MysqlCommands;
    use InputOutput;

    protected function configure()
    {
        $this
            ->setName('list-addresses')
            ->setDescription('Lists all addresses and their balances')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->prepareExecute($input, $output, $this);
        $this->connectMultichain();
        $this->multichainDebug($output);
        $this->connectMysql();

        $addresses = $this->getKycAddresses($this->multichain->getAddresses());
        $this->listAddresses($addresses, null, $this->getBalances());
    }
}