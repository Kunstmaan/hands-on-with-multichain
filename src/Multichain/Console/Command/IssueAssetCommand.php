<?php

namespace Multichain\Console\Command;

use Multichain\Console\Shared\InputOutput;
use Multichain\Console\Shared\MultichainCommands;
use Multichain\Console\Shared\MysqlCommands;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class IssueAssetCommand extends Command
{
    use MultichainCommands;
    use MysqlCommands;
    use InputOutput;

    protected function configure()
    {
        $this
            ->setName('issue-asset')
            ->addArgument("name", InputArgument::REQUIRED, "The name of the asset")
            ->addArgument("quantity", InputArgument::REQUIRED, "The amount of the asset to create")
            ->addArgument("units", InputArgument::REQUIRED, "The division of the asset")
            ->addArgument("address", InputArgument::REQUIRED, "The address to create the asset in")
            ->setDescription('Issues an asset')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->prepareExecute($input, $output, $this);
        $this->connectMultichain();
        $this->multichainDebug($output);
        $this->connectMysql();

        $quantity = $input->getArgument("quantity");
        if ($quantity == "max"){
            $quantity = 1000000000000000; // http://www.multichain.com/qa/925/maximum-quantity-for-an-asset;
        }

        $address = $this->getAddressByKyc($input->getArgument("address"));
        if (!$address){
            $address = $input->getArgument("address");
        } else {
            $address = $address->getAddress();
        }

        $this->io->text("Issue a new asset called <fg=blue>" . $input->getArgument("name") . "</fg=blue> with a quantity of <fg=blue>" . $quantity . "</fg=blue> and a unit division of <fg=blue>" . $input->getArgument("units") . "</fg=blue> in address <fg=blue>" . $input->getArgument("address") . " (".$address.")</fg=blue>");

        $asset_issuetxid = $this->multichain->issue($address, $input->getArgument("name"), (int) $quantity, (float) $input->getArgument("units"));
        $this->io->text("");
        $this->listAssets($this->multichain->listAssets(), $asset_issuetxid, $this->getAssetHolders());
    }

}
