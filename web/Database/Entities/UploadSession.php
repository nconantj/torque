<?php

namespace Database\Entities;

// userID, vehicleID, and sessionStart form to make a unique entry.
/**
 * @Entity
 * @Table(name="server_sessions")
 */
class UploadSession
{
    /**
     * @Id
     * @Column(length=36, name=upload_id)
     * @GeneratedValue
     */
    private $uploadID;

    /**
     * @Id
     * @Column(type="datetime", name="session_start")
     */
    private $sessionStart;

    /**
     * @Column(type="json_array")
     */
    private $data;

    /**
     * @Column(type="datetime", name="last_access")
     */
    private $lastAccess;
}
