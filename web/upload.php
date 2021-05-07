<?php

require_once('vendor/autoload.php');
require_once('creds.php');
require_once('functions.php');
require_once('upload_functions.php');

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Database\Entities\DataEntry;
use Database\Entities\DataHeader;
use Database\Entities\DataPID;
use Database\Entities\UploadSession;
use Database\Entities\User;
use Database\Entities\Vehicle;

$paths = array("Database/Entities");
$isDevMode = false;

$connectionParams = array (
    'dbname' => $database,
    'user' => $user,
    'password' => $pass,
    'host' => $host,
    'driver' => 'mysqli',
);

$config = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode);
$entityManager = EntityManager::create($connectionParams, $config);

//$data_upload_session = new DataUploadSession($conn);

//$data_upload_session->CleanSessions(120);



if (
    isset($_GET['v'])
    && isset($_GET['eml'])
    && isset($_GET['id'])
    && isset($_GET['session'])
) {
    // Process input
    $user = $entityManager
        ->getRepository('User')
        ->findOneByUploadID($_GET['eml']);
    if ($user == null) {
        die("User not found!");
    }

    // Create a session key.
    $session_id = array(
        'upload_id' => $_GET['eml'],
        'session_start' => convertToDateTime($_GET['session'])
    );

    $uploadSession = $entityManager->find('UploadSession', $session_id);

    $session_data = array();

    if ($uploadSession == null) {
        // New Session
        $session_data['state'] = UploadSession::SESSION_HEADERS_NONE;
        $session_data['time'] = convertToDateTime($_GET['time']);

        $uploadSession = new UploadSession(
            $session_id['upload_id'],
            $session_id['session_start'],
            $json_encode($session_data)
        );

        $entityManager->persist($uploadSession);
    } else {
        $session_data = json_decode($uploadSession->getData());
    }

    //Determine state and process accordingly
    if ($session_data['state'] == UploadSession::SESSION_HEADERS_ALL) {
        // Actual data.
        $keys = preg_grep('/^k/', array_keys($_GET));
        $time_raw = $_GET['time'];
        $time = convertToDateTime($_GET['time']);

        $data = array();

        $abrp_forward =
            $session_data['abrp']['forward']
            && $session_data['abrp']['id'] != '';

        foreach ($keys as $key) {
            $value = $_GET[$key];
            if ($abrp_forward) {
                if (!isset($data['time'])) {
                    $data['time'] = $time_raw,
                    $data['values'] = array();
                }

                $data['values'][$key] = $value;
            }

            $dataEntry = new DataEntry(
                $session_data['session_id'],
                $session_data['pid_ids'][$key],
                $time,
                $value
            );

            $entityManager->persist($dataEntry);
        }

        // Insert $data['server']
        $entityManager->flush();

        if ($abrp_forward) {
            abrp_forward($session_id, $data, $session_data['abrp']['id']);
        }
    } else {
        $get_keys = array_keys($_GET);

        // We will attempt to get all the keys we're interested in getting.

        if (process_session_data('defaultUnit', $get_keys, $session_data)) {
            // Session Query 1
            $session_data['state'] |= UploadSession::SESSION_HEADERS_DEFAULTS;
        } elseif (process_session_data('profile', $get_keys, $session_data)) {
            // Session Query 2
            $session_data['state'] |= UploadSession::SESSION_HEADERS_VEHICLE;
        } elseif (
            process_session_data('userUnit', $get_keys, $session_data) &&
            process_session_data('userShortName', $get_keys, $session_data) &&
            process_session_data('userFullName', $get_keys, $session_data)
        ) {
            $session_data['state'] |= UploadSession::SESSION_HEADERS_USER;
        } else {
            // Do Nothing
        }

        if (
            $session_data['state'] != UploadSession::SESSION_HEADERS_ALL
        ) {
            // Save the session if it's incomplete.
            $uploadSession->setData(json_encode($session_data));
            $entityManager->flush();
        } elseif (
            $session_data['state'] == UploadSession::SESSION_HEADERS_ALL
        ) {
            // The session data is complete. Process and save.

            // Get vehicle profile from session data and then get the internal database ID.
            $vehicle_profile = $session_data['profile'];
            $vehicle = $entityManager
                ->getRepository('Vehicle')
                ->findOneBy(array(
                    'owner' => $user,
                    'name' => $vehicle_profile['name'])
            );

            if ($vehicle == null) {
                // If no vehicle is found then it's new.
                $vehicle = new Vehicle(
                    $user,
                    $vehicle_profile['name'],
                    $vehicle_profile['fuelType'],
                    $vehicle_profile['FuelCost'],
                    $vehicle_profile['Weight'],
                    $vehicle_profile['ve']
                );

                $entityManager->persist($vehicle);
            }

            $dataHeader = new DataHeader(
                $vehicle,
                $session_id['session_start']
            );

            $entityManager->persist($dataHeader);
            $entityManager->push();

            $upload_id = $session_id['eml'];

            // Get userID (int_id), abrp_id, and abrp_forward from user table.

            // If no results exit.

            // $abrp_forward = result['abrp_forward']
            $abrp_forward = false || $user->getABRPForward();
            $abrp_id = $user->getABRPID();;

            // if $abrp_forward, $abrp_id = result['abrp_id']
            if ($abrp_forward && trim($abrp_id) != '') {
                $session_data['abrp_id'] = $abrp_id;
                abrp_forward($session_id, $session_data, $abrp_id, true);
            }

            // Get or create vehicle from $session_data[profile][name] and
            //   $session_data[$userID] and store vehicleID locally. If new,
            //   include fuelType, fuelCost, weight, and ve from
            //   $session_data[profile] when creating.


            // Remove vehicle profile from session data.
            unset($session_data['profile']);

            // Create insert array.
            $vehicle_data = array(
                'owner' => $userID,
                'name' => $vehicle_profile['name'],
                'fuel_type' => $vehicle_profile['FuelType'],
                'fuel_cost' => $vehicle_profile['FuelCost'],
                'weight' => $vehicle_profile['Weight'],
                'volumetric_efficiency' => $vehicle_profile['ve']
            );

            // Get vehicle ID or insert and get same.


            // Create session_header from userID, vehicleID, $session_id[v],
            //   and $session_id[session] and store sessID locally and in
            //   $session_data['sessID'].

            // Create insert array.
            $session_header_data = array(
                'user_id' => $userID,
                'vehicle_id' => $vehicleID,
                'v' => $session_id['v'],
                'session' => $session_id['session']
            );

            // Insert $session_header_data and get sessID from result.

            // Get torque keys
            $keys = preg_grep('/^k/', array_keys($session_data));
            $values = array();

            // For each torque key (starts with 'k'), create database entry
            // from $sessID, $key, $data[userFullName], $data[userShortName],
            // $data[defaultUnit], and $data[userUnit].
            // Unset($session_data[$key]).
            foreach ($keys as $key) {
                // Get info for key and remove from session data.
                $value = $session_data[$key];
                unset($session_data[$key]);

                $values[] = array(
                    'session_id' => $sessID,
                    'key_id' => $key,
                    'long_name' => $value['userFullName'],
                    'short_name' => $value['userShortName'],
                    'default_unit' => $value['default_unit'],
                    'user_unit' => $value['userUnit']
                );
            }

            // Perform multi-insert on database.

            // Save the session.
            $data_upload_session->SaveSession($session_id, $session_data);
        } else {
            // Do nothing.
        }
    }
}
