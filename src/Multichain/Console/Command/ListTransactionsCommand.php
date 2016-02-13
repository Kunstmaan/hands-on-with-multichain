<?php

namespace Multichain\Console\Command;

use Multichain\Console\Shared\InputOutput;
use Multichain\Console\Shared\MultichainCommands;
use Multichain\Console\Shared\MysqlCommands;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListTransactionsCommand extends Command
{
    use MultichainCommands;
    use MysqlCommands;
    use InputOutput;

    protected function configure()
    {
        $this
            ->setName('list-transactions')
            ->setDescription('List the last transactions')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->prepareExecute($input, $output, $this);
        $this->connectMultichain();
        $this->multichainDebug($output);
        $this->connectMysql();

        //$this->listTransactions($this->multichain->listWalletTransactions(100, 0, false, true));

        $rr = $this->multichain->listWalletTransactions(10, 0, false, true);
        var_dump($rr);
        //$this->multichain->listTransactions();
    }

}