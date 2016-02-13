<?php

namespace Multichain\Console\Command;

use Multichain\Console\Shared\InputOutput;
use Multichain\Console\Shared\MultichainCommands;
use Multichain\Console\Shared\MysqlCommands;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExchangeCommand extends Command
{
    use MultichainCommands;
    use MysqlCommands;
    use InputOutput;

    protected function configure()
    {
        $this
            ->setName('exchange')
            ->addArgument("from-asset", InputArgument::REQUIRED, "The name of the asset")
            ->addArgument("from-quantity", InputArgument::REQUIRED, "The amount of the asset")
            ->addArgument("from", InputArgument::REQUIRED, "From which address")
            ->addArgument("to-asset", InputArgument::REQUIRED, "The name of the asset")
            ->addArgument("to-quantity", InputArgument::REQUIRED, "The amount of the asset")
            ->addArgument("to", InputArgument::REQUIRED, "To which address")
            ->setDescription('Exchange an asset for another one')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->prepareExecute($input, $output, $this);
        $this->connectMultichain();
        $this->multichainDebug($output);
        $this->connectMysql();

        $from = $this->getAddressByKyc($input->getArgument("from"));
        if (!$from){
            $from = $input->getArgument("from");
        } else {
            $from = $from->getAddress();
        }

        $to = $this->getAddressByKyc($input->getArgument("to"));
        if (!$to){
            $to = $input->getArgument("to");
        } else {
            $to = $to->getAddress();
        }
        $lock = $this->multichain->prepareLockUnspentFrom($from, array($input->getArgument("from-asset") => (float)$input->getArgument("from-quantity")));
        $this->io->text("Lock <fg=blue>" . $input->getArgument("from-quantity") . "</fg=blue> of <fg=blue>" . $input->getArgument("from-asset") . "</fg=blue> in address <fg=blue>" . $input->getArgument("from") . " (".$from.")</fg=blue>");

        $exchange = $this->multichain->createRawExchange($lock["txid"], $lock["vout"], array($input->getArgument("to-asset") => (float)$input->getArgument("to-quantity")));
        $this->io->text("Create the first side of the exchange with this lock and offer them for <fg=blue>" . $input->getArgument("to-quantity") . "</fg=blue> of <fg=blue>" . $input->getArgument("to-asset") . "</fg=blue>");

        $lock2 = $this->multichain->prepareLockUnspentFrom($to, array($input->getArgument("to-asset") => (float)$input->getArgument("to-quantity")));
        $this->io->text("Lock <fg=blue>" . $input->getArgument("to-quantity") . "</fg=blue> of <fg=blue>" . $input->getArgument("to-asset") . "</fg=blue> in address <fg=blue>" . $input->getArgument("to") . " (".$to.")</fg=blue>");

        $exchange2 = $this->multichain->appendRawExchange($exchange, $lock2["txid"], $lock2["vout"],  array($input->getArgument("from-asset") => (float)$input->getArgument("from-quantity")));
        $this->io->text("Add the second side of the exchange with this second lock");

        $result = $this->multichain->sendRawTransaction($exchange2["hex"]);
        $this->io->text("Send the completed transaction to the blockchain");

        $this->io->text("");

        $this->listAssets($this->multichain->listAssets(), null, $this->getKycForAssetHolders($this->getAssetHolders()));
        $addresses = $this->getKycAddresses($this->multichain->getAddresses());
        $this->listAddresses($addresses, null, $this->getBalances());    }
}
