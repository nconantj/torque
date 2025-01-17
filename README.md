This repo is a fork of the original OpenTorque Viewer by econpy.

This repo contains everything needed to setup an interface for uploading ODB2 data logged from your car in real-time using the [Torque Pro](https://play.google.com/store/apps/details?id=org.prowl.torque) app for Android.

The interface allows the user to:

  * View an Open Street Map showing your trips logged via Torque
  * Create time series plots of OBD2 data
  * Easily export data to CSV or JSON

<!--
# Demo #

[Check out the demo!](http://data.mattnicklay.com/torque/session.php?id=1404557450999)
-->

# Requirements #

These instructions assume you already have a LAMP-like server (on a Linux/UNIX based host) or have access to one. Specifically, you'll need the following:

  * MySQL database
  * Apache webserver
  * PHP server-side scripting

If in doubt, I'd recommend using Ubuntu 12.04 LTS.

# Server Setup #

First clone the repo:

```bash
git clone https://github.com/nconantj/torque
cd torque
```

## Configure MySQL ##

To get started, create a database named `torque` and a user with permission to insert and read data from the database. In this tutorial, we'll create a user `steve` with password `zissou` that has access to all tables in the database `torque` from `localhost`:

```sql
CREATE DATABASE torque;
CREATE USER 'steve'@'localhost' IDENTIFIED BY 'zissou';
GRANT USAGE, FILE TO 'steve'@'localhost';
GRANT ALL PRIVILEGES ON torque.* TO 'steve'@'localhost';
FLUSH PRIVILEGES;
```

Then create a table in the database to store the logged data using the `create_torque_log_table.sql` file provided in the `scripts` folder of this repo:

**WARNING:** This is data destructive. Export your data before running the script if you are updating.

```bash
mysql -u yoursqlusername -p < scripts/create_torque_log_table.sql
```

## Configure Webserver ##

### Move Files ###
Move the contents of the `web` folder to your webserver and set the appropriate permissions. For example, using an Apache server located at `/var/www`:

```bash
mv web /var/www/torque
cd /var/www/torque
find . -type d -exec chmod 755 {} +
find . -type f -exec chmod 644 {} +
```

### Set Credentials ###

Rename the `creds-sample.php` file to `creds.php`:

```bash
mv creds-sample.php creds.php
```

Then edit/enter your MySQL username and password in the empty **$db_user** and **$db_pass** fields:

```php
...
$db_host = "localhost";
$db_user = "steve";
$db_pass = "zissou";
$db_name = "torque";
$db_table = "raw_logs";
...
```

### Session Viewing ###

In order to see the map underlying a session location plot when viewing
sessions, you need to got to mapbox.com, create an account, and create a token.

MapBox automatically creates a token for you, but it is highly recommended that
you create a new one that you can customize.

Once you have the token, copy and paste it to the **$mapBoxAccessToken** field.

```php
...
$mapBoxAccessToken = 'mapBoxAccessToken';
...
```

### Enable [A Better Route Planner (ABRP)](https://abetterrouteplanner.com) Forwarding (optional) ###
**NOTE:** ABRP forwarding assumes you have your Torque e-mail address set to your ABRP token as described in the ABRP Torque setup.

To forward data to ABRP change **$abrp_forward** field to **true**:

```php
...
$abrp_forward = true;
...
```

# Torque Settings #

### General Settings ###

To use your database/server with Torque, open the app on your phone and navigate to:

```
Settings -> Data Logging & Upload -> Webserver URL
```

Enter the URL to your **upload_data.php** script and press `OK`. Test that it works by clicking `Test settings` and you should see a success message like the image on the right:

<div align="center" style="padding-bottom:15px;"><a href="https://storage.googleapis.com/torque_github/torque_webserver_url.png" target="_blank"><img src="https://storage.googleapis.com/torque_github/torque_webserver_url.png" width="49%" align="left"></img></a><a href="https://storage.googleapis.com/torque_github/torque_test_passed.png" target="_blank"><img src="https://storage.googleapis.com/torque_github/torque_test_passed.png" width="49%" align="right"></img></a></div>

The final thing you'll want to do before going for a drive is to check the appropriate boxes on the `Data Logging & Upload` page under the `REALTIME WEB UPLOAD` section. Personally, I have both **Upload to webserver** and **Only when ODB connected** checked.

At this point, you should be all setup. The next time you connect to Torque in your car, data will begin syncing into your MySQL database in real-time!

### ABRP Settings ###

To use your database/server with Torque, open the app on your phone and navigate to:

```
Settings -> Data Logging & Upload -> Webserver URL
```

Follow instructions from ABRP, but enter the URL to your **upload_data.php** script and press `OK`. Test that it works by clicking `Test settings` and you should see a success message like the image on the right:

<div align="center" style="padding-bottom:15px;"><a href="https://storage.googleapis.com/torque_github/torque_webserver_url.png" target="_blank"><img src="https://storage.googleapis.com/torque_github/torque_webserver_url.png" width="49%" align="left"></img></a><a href="https://storage.googleapis.com/torque_github/torque_test_passed.png" target="_blank"><img src="https://storage.googleapis.com/torque_github/torque_test_passed.png" width="49%" align="right"></img></a></div>

At this point, you should be all setup. The next time you connect to Torque in your car, data will begin syncing into your MySQL database in real-time!
