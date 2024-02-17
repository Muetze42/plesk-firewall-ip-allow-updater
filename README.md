# Automatic Plesk Firewall Update Script

## Setup

### API (Server)

* Copy `/public/file.php.stub` to a random PHP file like `/public/{random-string}.php` (this is the API url part)

### Client (Local Machine)

* Copy [/client/config.json.example](client/config.json.example) to client/config.json.example  and configure
* Configure a cron wich run every x minutes `/client/make-request.php`

### Script (Server)

* Copy [/config.example](config.example) to config.json and configure
* Run `composer install`
* Create a plesk Schedule Task wich run the [/script/run.php](script/run.php) script every x minutes
* Store an SSH private key as `/private.ppk`

## Configurations

### /client/config.json

This config file is used by a client.

```json
{
    "url": "Url to the API script /public/{random-string}.php",
    "token": "A client token from the /config.json"
}
```

### /config.json

This config is used by Script (`/script`) and API (`/public`)

```json
{
    "target": "/../ip-table.json -> Tables for client IPs",
    "user": "Server user name",
    "pkk-pass": "Optional: passphrase for the private.ppk",
    "always-allowed-ips": ["Optional", "Other Server", "Static client IP"],
    "firewall-names": [
        "Rules for which the IPs are to be saved",
        "Allow Incoming Database Connections",
        "Allow Incoming SSH Connections"
    ],
    "clients": [
        {
            "//desc": "Define different clients"
        },
        {
            "id": 1,
            "token": "{token}"
        },
        {
            "id": 2,
            "token": "{token}"
        }
    ]
}

```

## Usefully Links

* Run script in background in Windows: https://stackoverflow.com/a/6568823/9657674

