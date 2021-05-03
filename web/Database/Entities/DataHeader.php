<?php

namespace Database\Entities;

use Doctrine\Common\Collections\ArrayCollection;

// userID, vehicleID, and sessionStart form to make a unique entry.
/**
 * @Entity
 * @Table(name="data_headers")
 */
class DataHeader
{
    /**
     * @Id
     * @Column(type="integer", name="int_id")
     * @GeneratedValue
     */
    private $sessionID;

    /**
     * @Column(type="integer", name="vehicle_id")
     */
    private $vehicleID; // Vehicle Profile Name

    /*
     * @OneToMany(targetEntity="DataPID", mappedBy="session")
     */
    private $pids;

    /*
     * @OneToMany(targetEntity="DataEntry", mappedBy="session")
     */
    private $entries;

    /**
     * @ManyToOne(targetEntity="Vehicle", inversedBy="sessions")
     * @JoinColumn(name="vehicle_id", referencedColumnName="id")
     */
    private $vehicle;

    /**
     * @Column(type="datetime", name="session_start")
     */
    private $sessionStart;

    public function __construct()
    {
        $this->pids = new ArrayCollection();
        $this->entries = new ArrayCollection();
    }

}
