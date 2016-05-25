<?php

namespace Multichain\Console\Shared;


use be\kunstmaan\multichain\MultichainClient;
use be\kunstmaan\multichain\MultichainHelper;
use Symfony\Component\Console\Output\OutputInterface;

trait MultichainCommands
{

    /** @var MultichainClient */
    protected $multichain;

    /** @var  MultichainHelper */
    protected $helper;

    /**
     * Connects to the Multichain master node
     */
    protected function connectMultichain(){
        $this->multichain = new MultichainClient("http://mulichain-master.docker:8000", 'multichainrpc', 'this-is-insecure-change-it', 3);
        $this->helper = new MultichainHelper($this->multichain);
    }

    /**
     * Sets the debug flag on the Multichain library when running in verbose mode
     *
     * @param OutputInterface $output
     */
    protected function multichainDebug(OutputInterface $output){
        $this->multichain->setDebug($output->getVerbosity() > OutputInterface::VERBOSITY_NORMAL);
    }

    protected function getAssetHolders(){
        $holders = array();
        $allAddresses = $this->multichain->getAddresses();
        array_walk($allAddresses, function (&$address) use (&$holders) {
            $balance = $this->multichain->getAddressBalances($address);
            foreach($balance as $asset){
                $holders[$asset["name"]][] = [ $address, $asset["qty"]];
            }
        });
        return $holders;
    }

    protected function getBalances(){
        $balances = array();
        $allAddresses = $this->multichain->getAddresses();
        array_walk($allAddresses, function (&$address) use (&$balances) {
            $balance = $this->multichain->getAddressBalances($address);
            foreach($balance as $asset){
                $balances[$address][] = [$asset["name"], $asset["qty"]];
            }
        });
        return $balances;
    }

}
