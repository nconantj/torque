<?php

namespace Database\Entities;

require_once('../../functions.php');

// userID, vehicleID, and sessionStart form to make a unique entry.
/**
 * @Entity
 * @Table(name="server_sessions")
 */
class UploadSession
{
    public const SESSION_HEADERS_NONE = 0;
    public const SESSION_HEADERS_DEFAULTS = 1;
    public const SESSION_HEADERS_VEHICLE = 2;
    public const SESSION_HEADERS_USER = 4;
    public const SESSION_HEADERS_ALL = SESSION_HEADERS_DEFAULTS | SESSION_HEADERS_VEHICLE | SESSION_HEADERS_USER;

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

    public function __construct(
        $uploadID,
        $sessionStart,
        $data,
        $lastAccess = null
    ) {
        $this->uploadID = $uploadID;
        $this->sessionStart = $sessionStart;
        $this->data = $data;

        if ($lastAccess == null) {
            $lastAccess = convertToDateTime(microtime(true), false);
        }

        $this->lastAccess = $last_access;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setData($value)
    {
        $this->data = $value;
        $this->last_access = convertToDateTime(microtime(true), false);
    }
}
