<?php

namespace Database\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Ramsey\Uuid\Uuid;

require_once('creds.php');

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

    public static function generateUploadID($email)
    {
        // Create a V5 UUID from https://uuid.ramsey.dev/en/latest/rfc4122/version5.html
        if (TORQUE_NAMESPACE == '') {
            die("Please generate a UUID and enter it into creds.php where it says 'TORQUE_NAMESPACE.'");
        }

        if ($email == null || $email == '') {
            die("Email required.");
        }

        return Uuid::uuid5(TORQUE_NAMESPACE, $email);
    }

    public function __construct(
        $email,
        $password,
        $uploadID,
        $alias = '',
        $abrpID = '',
        $abrpForward = false
    ) {
        if ($abrpForward && ($abrpID == null || $abrpID == '')) {
            die("Cannot enable ABRP forwarding without an ABRP ID.");
        }

        if ($email == null || $emal == '') {
            die("Email required.");
        }

        if ($password == null || $password == '') {
            die("Password required.")
        }

        if ($uploadID == null || $uploadID == '') {
            die("Upload ID require.");
        }

        $this->email = $email;
        $this->password -> $password;
        $this->alias = ($alias == null ? '' : $alias);
        $this->abrpID = ($abrpID == null ? '' : $abrpID);
        $this->abrpForward = $abrpForward;
        $this->uploadID = $uploadID;

        $this->vehicles = new ArrayCollection();
    }

    public function getABRPForward()
    {
        return $this->abrpForward;
    }

    public function getABRPID()
    {
        return $this->abrpID;
    }

    public function setABRPForward($value)
    {
        if($abrpForward && $this->abrpID == '') {
            die("Cannot enable ABRP forwarding without an ABRP ID.");
        }

        $this->abrpForward = $value;
    }

    public function setABRPID($value, $enable = true)
    {
        if ($enable && $value == '') {
            die("Cannot enable ABRP forwarding without an ABRP ID.")
        }

        $this->abrpID = $value;
        $this->abrpForward = $enable;
    }

    public function enableABRPForward() {
        $this->setABRPForward(true);
    }

    public function disableABRPForward() {
        $this->setABRPForward(false);
    }

    public function hashPassword() {
        $this->password = password_hash($this->password, PASSWORD_DEFAULT);
    }
}
