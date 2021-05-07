<?php

namespace Torque;

class User
{
    private int $userID; // DB int_id
    private string $eml;
    private string $torqueID; //DB id, URL id
    private bool $changed;

    public function __construct(
        string $eml,
        string $torque_id,
        int $userID = -1
    ) {
        $this->userID = $userID;
        $this->eml = $eml;
        $this->torque_id = $torque_id;
        $this->changed = ($userID == -1);
    }

    public function getUserID(): int
    {
        return $this->userID;
    }

    public function setUserID($value)
    {
        if (!$this->isNew()) {
            $this->userID = $value;
            $this->changed = false;
        } else {
            die("UserID is populated from the database.");
        }
    }

    public function getEml(): string
    {
        return $this->eml;
    }

    public function setEml(string $value)
    {
        if ($value != $this->eml) {
            $this->eml = $value;
            $this->changed = true;
        }
    }

    public function getTorqueID(): string
    {
        return $this->torque_id;
    }

    public function isChanged(): bool
    {
        return $this->changed;
    }

    public function isNew(): bool
    {
        return $this->userID == -1;
    }

    public function getUpdateArray(): array
    {
        if ($this->isNew() || !$this->isChanged()) {
            return null;
        }

        return array(
            'userID' => $this->userID,
            'eml' => $this->eml,
            'torqueID' => $this->torqueID
        );
    }

    public function getInsertArray(): array
    {
        if (!$this->isNew()) {
            return null;
        }

        return array('eml' => $this->eml, 'torqueID' => $this->torqueID);
    }

    // Returns an array containing the data for the unique columns
    public function getUniqueKey(): array
    {
        return array('eml' => $this->eml, 'torqueID' => $this->torqueID);
    }
}
