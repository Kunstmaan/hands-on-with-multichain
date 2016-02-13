<?php

namespace Multichain\Console\Entity;

/**
 * Address
 */
class Address
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $address;

    /**
     * @var string
     */
    private $kycname;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set address
     *
     * @param string $address
     *
     * @return Address
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set kycname
     *
     * @param string $kycname
     *
     * @return Address
     */
    public function setKycname($kycname)
    {
        $this->kycname = $kycname;

        return $this;
    }

    /**
     * Get kycname
     *
     * @return string
     */
    public function getKycname()
    {
        return $this->kycname;
    }
}
