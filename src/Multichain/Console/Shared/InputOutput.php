<?php

namespace Multichain\Console\Shared;

use Multichain\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;


trait InputOutput
{

    /** @var SymfonyStyle */
    protected $io;

    protected function prepareExecute(InputInterface $input, OutputInterface $output, Command $command){
        $this->io = new SymfonyStyle($input, $output);
        //$output->writeln(Application::LOGO);
        //$this->io->title($command->getName());
    }

    protected function listAddresses($allAddresses, $currentAddress, $balances){
        $rows = array();
        array_walk($allAddresses, function ($value) use (&$rows, $currentAddress, $balances) {
            $row = $value;
            if (isset($balances[$value[0]])) {
                $strBalances = "";
                foreach($balances[$value[0]] as $balance){
                    $strBalances .= (!empty($strBalances)?PHP_EOL:"") . $balance[0] . ": " . number_format($balance[1], 2, ".", ",");
                }
                $row[] = $strBalances;
            }
            if (!is_null($currentAddress) && $value[0] == $currentAddress) {
                $rows[] = $this->colorArrayItems($value, "red");
            } else {
                $rows[] = $row;
            }
        });
        $this->io->table(["Addresses", "KYC", "Balance"], $rows);
    }

    protected function listAssets($allAssets, $asset_issuetxid = null, $holders = null)
    {
        $rows = array();
        array_walk($allAssets, function ($value) use (&$rows, $asset_issuetxid, $holders) {
            $row = [$value["name"], number_format($value["issueqty"], 0, ".", ","), $value["units"], $value["assetref"]];
            if (isset($holders[$value["name"]])){
                $strHolders = "";
                foreach($holders[$value["name"]] as $holder){
                    $strHolders .= (!empty($strHolders)?PHP_EOL:"") . $holder[0] . (isset($holder[2])?' aka ' . $holder[2] : "") . ": " . number_format($holder[1], 2, ".", ",");
                }
                $row[] = $strHolders;
            }
            if (!is_null($asset_issuetxid) && $value["issuetxid"] == $asset_issuetxid) {
                $rows[] = $this->colorArrayItems($row, "red");
            } else {
                $rows[] = $row;
            }
        });
        $this->io->table(["Name", "Quantity", "Units", "Asset Ref", "Holders"], $rows);
    }

    protected function listTransactions($transactions){
        $rows = array();
        $timecolumn = 2;
        array_walk($transactions, function ($transaction) use (&$rows) {
            $row = [$transaction["txid"], $transaction["confirmations"], $transaction["time"]];
            $rows[] = $row;
        });
        usort($rows, function($a, $b) use ($timecolumn) {
            if ($a[$timecolumn] == $b[$timecolumn]) {
                return 0;
            }
            return ($a[$timecolumn] < $b[$timecolumn]) ? 1 : -1;
        });
        array_walk($rows, function (&$row) use ($timecolumn) {
            $row[$timecolumn] = $this->time2str($row[$timecolumn]);
        });
        $this->io->table(["Txid", "Confirmations", "Time"], $rows);
    }

    protected function colorArrayItems(array $array, $color)
    {
        array_walk($array, function (&$value) use ($color) {
            if (!is_array($value)) {
                $value = "<fg=$color;options=bold>" . $value . "</fg=$color;options=bold>";
            }
        });
        return $array;
    }


    function time2str($ts) {
        if(!ctype_digit($ts)) {
            $ts = strtotime($ts);
        }
        $diff = time() - $ts;
        if($diff == 0) {
            return 'now';
        } elseif($diff > 0) {
            $day_diff = floor($diff / 86400);
            if($day_diff == 0) {
                if($diff < 60) return 'just now';
                if($diff < 120) return '1 minute ago';
                if($diff < 3600) return floor($diff / 60) . ' minutes ago';
                if($diff < 7200) return '1 hour ago';
                if($diff < 86400) return floor($diff / 3600) . ' hours ago';
            }
            if($day_diff == 1) { return 'Yesterday'; }
            if($day_diff < 7) { return $day_diff . ' days ago'; }
            if($day_diff < 31) { return ceil($day_diff / 7) . ' weeks ago'; }
            if($day_diff < 60) { return 'last month'; }
            return date('F Y', $ts);
        } else {
            $diff = abs($diff);
            $day_diff = floor($diff / 86400);
            if($day_diff == 0) {
                if($diff < 120) { return 'in a minute'; }
                if($diff < 3600) { return 'in ' . floor($diff / 60) . ' minutes'; }
                if($diff < 7200) { return 'in an hour'; }
                if($diff < 86400) { return 'in ' . floor($diff / 3600) . ' hours'; }
            }
            if($day_diff == 1) { return 'Tomorrow'; }
            if($day_diff < 4) { return date('l', $ts); }
            if($day_diff < 7 + (7 - date('w'))) { return 'next week'; }
            if(ceil($day_diff / 7) < 4) { return 'in ' . ceil($day_diff / 7) . ' weeks'; }
            if(date('n', $ts) == date('n') + 1) { return 'next month'; }
            return date('F Y', $ts);
        }
    }
}
