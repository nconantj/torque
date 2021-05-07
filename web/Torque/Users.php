<?php

namespace \Torque;

use Database;

class Users
{
    private array $usersByID = null;
    private array $usersByUniqueKey = null;
    private array $updatedUsers = null;
    private array $newUsers = null;

    private Database $database;

    public function __construct(
        string $host,
        string $user,
        string $pass,
        string $database = ""
    ) {
        $this->database = new Database($host, $user, $pass, $database);
        $this->usersByID = array();
        $this->usersByUniqueKey = array();
        $this->newUsers = array();
        $this->updatedUsers = array();
    }

    public function load()
    {
        $this->usersByID = array();
        $this->usersByUniqueKey = array();
        array $dbUsers = $database->selectAll('users')->fetch_all(MYSQLI_ASSOC);

        foreach ($dbUsers as $dbUser) {
            User $user = new User($dbUser['eml'], $dbUser['id'], $dbUser['int_id']);
            $this->$usersByID[$user->getUserID()] = $user;
            $this->usersByUniqueKey[$user->getUniqueKey()] = $user;
        }
    }

    public function loadUserByID($userID)
    {
        // Not yet implemented
        die("Not yet implemented.");
    }

    public function getUserByID($userID): User
    {
        if ($this->usersByID == null) {
            return null;
        }

        return $this->usersByID[$userID];
    }

    public function getUser(string $eml, string $torqueID): User
    {
        $uniqueKey = array( 'eml' => $eml, 'torque_id' => $torqueID);

        return $this->usersByUniqueKey[$uniqueKey];
    }

    public function containsUserID($userID)
    {
        return ($this->getUserByID($userID) != null);
    }

    public function containsUser(string $eml, string $torqueID): bool
    {
        return ($this->getUser($eml, $torqueID) != null);
    }

    public function pushNewUsers()
    {
        while (count($this->newUsers) > 0) {
            $user = array_pop($this->newUsers);
            $user->setUserID(
                $this->database->insert(
                    'users',
                    array ('eml', 'id'),
                    array ($user->eml, $user->torqueID)
                )
            );

            $this->usersByID[$user->getUserID] = $user;
            $this->usersByUniqueKey[$user->getUniqueKey()] = $user;
        }
    }

    public function addUser(
        string $eml,
        string $torqueID,
        bool $push = true
    ): User {
        // Check before inserting.
        if ($this->containsUser($eml, $torqueID)) {
            return $this->usersByUniqueKey($eml, $torqueID);
        }

        User $user = new User($eml, $torqueID);

        $this->newUsers[] = $user;

        if ($push) {
            $this->pushNewUsers();
        }

        return $user;
    }

    public function pushUpdatedUsers()
    {
        // Update the database;
        while (count($this->updatedUsers) > 0) {
            $updatedUser = array_pop($this->updatedUsers);
            [$userID, $user] = $updatedUser;
            $this->database->update(
                'users',
                array('eml', 'id'),
                "int_id = ?",
                array($user->getEml(), $user->getTorqueID(), $userID())
            );
        }
    }

    public function flush()
    {
        $this->pushNewUsers();
        $this->pushUpdatedUsers();
    }

    public function reset()
    {
        $this->newUsers = array();
        $this->updatedUsers = array();
    }

    public function __destruct()
    {
        $this->flush();
    }

    public function updateUser(
        int $userID,
        string $eml = "",
        string $torqueID = "",
        bool $push = false
    ) {
        if (!$this->containsUserID($userID)) {
            return false;
        }

        $user = $this->getUserByID($userID);

        if ($eml != "") {
            $user->setEml($eml);
        }

        if ($torqueID != "") {
            $user->setTorqueID($torqueID);
        }

        if ($user->isChanged()) {
            $this->updatedUsers[] = array($userID, $user);

            if ($push) {
                $this->pushUpdatedUsers();
            }
        }
    }

    public function updateUserEml(
        int $userID,
        string $eml,
        bool $push = false
    ): bool {
        return $this->updateUser($userID, $eml, "", $push);
    }

    public function updateUserTorqueID(
        int $userID,
        string $torqueID,
        bool $push = false
    ): bool {
        return $this->updateUser($userID, "", $torqueID, $push);
    }
}
