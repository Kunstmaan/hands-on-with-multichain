<?php

namespace Multichain\Console\Command;

use Multichain\Console\Shared\InputOutput;
use Multichain\Console\Shared\MultichainCommands;
use Multichain\Console\Shared\MysqlCommands;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SendCommand extends Command
{
    use MultichainCommands;
    use MysqlCommands;
    use InputOutput;

    protected function configure()
    {
        $this
            ->setName('send')
            ->addArgument("asset", InputArgument::REQUIRED, "The name of the asset")
            ->addArgument("quantity", InputArgument::REQUIRED, "The amount of the asset")
            ->addArgument("from", InputArgument::REQUIRED, "From which address")
            ->addArgument("to", InputArgument::REQUIRED, "To which address")
            ->setDescription('Sends an asset to an address')
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
        $this->io->text("Send <fg=blue>" . $input->getArgument("quantity") . "</fg=blue> of <fg=blue>" . $input->getArgument("asset") . "</fg=blue> from address <fg=blue>" . $input->getArgument("from") . " (".$from.")</fg=blue> to <fg=blue>" . $input->getArgument("to") . " (".$to.")</fg=blue>");

        $this->multichain->sendAssetFrom($from, $to, $input->getArgument("asset"), (float) $input->getArgument("quantity"));
        $this->io->text("");
        $this->listAssets($this->multichain->listAssets(), null, $this->getKycForAssetHolders($this->getAssetHolders()));
        $addresses = $this->getKycAddresses($this->multichain->getAddresses());
        $this->listAddresses($addresses, null, $this->getBalances());
    }
}
