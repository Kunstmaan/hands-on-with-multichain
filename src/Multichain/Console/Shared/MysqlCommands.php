<?php

namespace Multichain\Console\Shared;

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Multichain\Console\Entity\Address;
use Multichain\Console\Entity\Plot;
use Multichain\Console\Entity\PlotTransaction;


trait MysqlCommands
{

    /** @var EntityManager */
    protected $em;

    /**
     * Connects to the Redis node
     */
    protected function connectMysql(){
        $dbParams = array(
            'driver'   => 'pdo_mysql',
            'user'     => 'blockchain',
            'password' => 'blockchain',
            'dbname'   => 'blockchain',
            'host'     => 'multichain-mysql.docker'
        );
        $isDevMode = true;
        $paths = array("./yml/");
        $config = Setup::createYAMLMetadataConfiguration($paths, $isDevMode);
        $this->em = EntityManager::create($dbParams, $config);
    }

    protected function storeKycAddress($address, $kycName = null)
    {
            $ormAddress = new Address();
            $ormAddress->setAddress($address);
            $ormAddress->setKycname($kycName);
            $this->em->persist($ormAddress);
            $this->em->flush();

        return $ormAddress;
    }

    protected function getKycAddresses($allAddresses){
        array_walk($allAddresses, function (&$row) {
            $address = $this->getAddressByAddress($row);
            if ($address){
                $row = [$address->getAddress(), $address->getKycname()];
            } else {
                $row = [$row, ""];
            }
        });
        return $allAddresses;
    }

    protected function getAddressByAddress($address){
        $qb = $this->em->createQueryBuilder();
        $qb->select('a')
            ->from('Multichain\Console\Entity\Address', 'a')
            ->where('a.address = ?1')
            ->setParameter(1, $address);
        $query = $qb->getQuery();
        return $query->getOneOrNullResult();
    }

    protected function getAddressByKyc($kyc){
        $qb = $this->em->createQueryBuilder();
        $qb->select('a')
            ->from('Multichain\Console\Entity\Address', 'a')
            ->where('a.kycname = ?1')
            ->setParameter(1, $kyc);
        $query = $qb->getQuery();
        return $query->getOneOrNullResult();
    }

    protected function getKycForAssetHolders($holders){
        array_walk($holders, function (&$holders, $asset) {
            foreach ($holders as &$holder) {
                $address = $this->getAddressByAddress($holder[0]);
                $holder[] = $address->getKycName();
            }
        });
        return $holders;
    }
}
