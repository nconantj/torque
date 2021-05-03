<?php

// session_id and key_id (varchar) form are unique together.
namespace Database\Entities;

use Doctrine\Common\Collections\ArrayCollection;

// userID, vehicleID, and sessionStart form to make a unique entry.
/**
 * @Entity
 * @Table(name="data_pids")
 */
class DataPID
{
    /**
     * @Id
     * @Column(type="integer", name="id")
     * @GeneratedValue
     */
    private $pidID;

    /**
     * @Column(type="integer", name="header_id")
     */
    private $sessionID;

    /**
     * @ManyToOne(targetEntity="DataHeader", inversedBy="pids")
     * @JoinColumn(name="header_id", referencedColumnName="id")
     */
    private $session;

    /**
     * @Column(length=10, name="pid_id")
     */
    private $keyName;

    /**
     * @Column(length=60, name="full_name")
     */
    private $fullName;

    /**
     * @Column(length=30, name="short_name")
     */
    private $shortName;

    /**
     * @Column(length=10, name="unit")
     */
    private $unit;

    /*
     * @OneToMany(targetEntity="DataEntry", mappedBy="entryPid")
     */
    private $entries;

    public function __construct()
    {
        $this->entries = new ArrayCollection();
    }
}
