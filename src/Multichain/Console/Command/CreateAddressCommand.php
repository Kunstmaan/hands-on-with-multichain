<?php

namespace Multichain\Console\Command;

use Multichain\Console\Shared\InputOutput;
use Multichain\Console\Shared\MultichainCommands;
use Multichain\Console\Shared\MysqlCommands;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateAddressCommand extends Command
{
    use MultichainCommands;
    use MysqlCommands;
    use InputOutput;

    protected function configure()
    {
        $this
            ->setName('create-address')
            ->addArgument("kyc", InputArgument::OPTIONAL, "A KYC identifier for the address")
            ->setDescription('Creates an address')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->prepareExecute($input, $output, $this);
        $this->connectMultichain();
        $this->multichainDebug($output);
        $this->connectMysql();

        $kycName = $input->getArgument("kyc");
        $ormAddress = $this->em->getRepository('Multichain\Console\Entity\Address')->findOneByKycname($kycName);
        if (!$ormAddress) {
            $address = $this->multichain->getNewAddress();
            if (!is_null($kycName)) {
                $ormAddress = $this->storeKycAddress($address, $kycName);
                $this->io->text("Create a new address and call it <fg=blue>" . $kycName . "</fg=blue>");
            } else {
                $this->io->text("Create a new address");
            }
        }

        $this->io->text("");
        $addresses = $this->getKycAddresses($this->multichain->getAddresses());
        $this->listAddresses($addresses, $ormAddress->getAddress(), $this->getBalances());
    }

}


