<?php

namespace Database\Entities;

// userID, vehicleID, and sessionStart form to make a unique entry.
/**
 * @Entity
 * @Table(name="data_entries")
 */
class DataEntry
{
    /**
     * @Id
     * @Column(type="integer", name="session_id")
     */
    private $sessionID;


    /**
     * @ManyToOne(targetEntity="DataHeader", inversedBy="entries")
     * @JoinColumn(name="session_id", referencedColumnName="id")
     */
    private $session;

    /**
     * @Id
     * @Column(type="integer", name="entry_pid_id")
     */
    private $pidID;

    /**
     * @ManyToOne(targetEntity="DataPID", inversedBy="entries")
     * @JoinColumn(name="entry_pid_id", referencedColumnName="id")
     */
    private $entryPID;

    /**
     * @Id
     * @Column(type="datetime", name="entry_time")
     */
    private $time;

    /**
     * @Column(type="float", name="entry_value")
     */
    private $value;

    public function __construct($sessionID, $pidID, $time, $value)
    {
        $this->sessionID = $sessionID;
        $this->$pidID = $pidID;
        $this->$time = $time;
        $this->$value = $value;
    }
}
