<?php

namespace Database\Entities;

use Doctrine\Common\Collections\ArrayCollection;

// owner and name form to make a unique entry.
/**
 * @Entity
 * @Table(name="vehicle")
 */
class Vehicle
{
    /**
     * @Id
     * @Column(type="integer", name="id")
     * @GeneratedValue
     */
    private $vehicleID;

    /**
     * @Column(type="integer", name="user_id")
     */
    private $ownerID; // A User

    /**
     * @ManyToOne(targetEntity="User", inversedBy="vehicles")
     * @JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $owner;

    /**
     * @Column(length=30)
     */
    private $name; // Vehicle Profile Name

    /**
     * @Column(type="smallint", name="fuel_type")
     */
    private $fuelType;

    /**
     * @Column(type="float", name="fuel_cost")
     */
    private $fuelCost;

    /**
     * @Column(type="float")
     */
    private $weight;

    /**
     * @Column(type="float")
     */
    private $ve;

    /*
     * @OneToMany(targetEntity="DataHeader", mappedBy="vehicle")
     */
    private $sessions;

    public function __construct()
    {
        $this->sessions = new ArrayCollection();
    }
}
