<?php

namespace Database\Entities;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity
 * @Table(name="users")
 */
class User
{
    /**
     * @Id
     * @Column(type="integer", name="id")
     * @GeneratedValue
     */
    private $userID;

    /** @Column(length=255, unique=true) **/
    private $email;

    /** @Column(length=255) **/
    private $password;

    /** @Column(length=30) **/
    private $alias;

    /** @Column(length=36, name="upload_id", unique=true) **/
    private $uploadID;

    /** @Column(length=36, name="abrp_id") **/
    private $abrpID;

    /** @Column(type="boolean", name="abrp_forward") **/
    private $abrpForward;

    /**
     * @OneToMany(targetEntity="Vehicle", mappedBy="owner")
     */
    private $vehicles;

    public function __construct()
    {
        $this->vehicles = new ArrayCollection();
    }
}
