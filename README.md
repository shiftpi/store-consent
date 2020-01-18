# Store Consent
A very simple toolkit for storing cookie consents on the backend.

## Motivation
Since the EU GDPR is out there, websites in the EU have to show broad information about cookies
used and have to obtain consents from the user about those cookies.

There are many ready to use solutions for these consents. 
Article 7 of the GDPR says, you have to prove the consent. One can now argue, the consent should be stored
backend-side to give the website hoster the possibility for this prove. However not all of the consent tools
offer such an backend storing feature.

You can use this toolkit for hooking into your consent tool and send the user selected consent to a database.

## Limitations
For reading the consents from the database you have to access the database directly. There is no GUI for doing this.

Currently supported database engines are MySQL and SQLite.

## Requirements
You need:
* PHP 7
* Some knowledge about JavaScript
* Optionally: A MySQL host

## Installation
First copy the file ``config/settings.dist.php`` to ``settings.php``. Adapt the file to your environment:
Use the ``db`` array for connection settings to the database. Store Consent internally uses PHP's PDO so the dsn should
be PDO compatible.

If you're running Store Consent in a subdirectory, you have to put the directory path in ``base_path``.
E.g. ``/consent-api`` (note: no trailing slash needed). You also have to change the .htaccess file if you're using Apache:
Prepend the subdirectory path to the rewrite rule. This could look like this:

````
RewriteRule ^ /consent-api/index.php [QSA,L]
```` 

The database can be created with the ``doc/createdb_mysql.sql`` or  ``doc/createdb_sqlite.sql`` script.

``consent_categories`` holds strings with category keys. These are the keys for the cookie categories you request
your users for permission. 

Then run ``composer install --no-dev`` inside the root directory and link ``public/`` to a path, accessible for
your webserver. That's it.

## Talking to the API (low level)
The API offers to endpoints:

You can create a new consent with ``POST <your path>/consent``. The POST body should be a x-www-form-urlencoded
object containing all the keys, you defined in the ``settings.php``, with either a ``0`` (for declined consent)
or ``1`` (for gave consent).

This is how a request could look like:
````
POST /consent HTTP/1.1
...
Content-Type: application/x-www-form-urlencoded

essential=1&marketing=1&external_media=0
````
The response will be a JSON object, containing the property ``id``. This id is required, if you want to update
a consent.

The update endpoint is accessible over ``PATCH <your path>/consent/<id>``. The body should be the same as in the
POST endpoint. However the id will not be returned, the response will be empty.

## Using the JS API (high level)
If you want to use a JS API, there is a file called ``store-consent.js`` in the ``docs`` directory.
First you have to configure the API path, if it's not the root path. Write the path into ``window.storeConsentUrl``.

Now you can access ``window.storeConsent.store(<settings>)``. Where settings should be a key-value object containing
the consents (use the ``settings.php`` to configure the keys).

All decisions about doing a ``POST`` or a ``PUT`` and storing the returned id will be done by the JS API.

A typical API call could look like this:
````html
<!-- ... -->
<script src="store-consent.js"></script>
<script>
    window.storeConsentUrl = '/consent-api';
    window.storeConsent.store({
        'essential': 1,
        'statistics': 0,
        'marketing': 1,
        'external_media': 1
    });
</script>
<!-- ... -->
````

## License
The project is licensed under the MIT license. See ``LICENSE`` file.

## Contributors
Andreas Rutz <andreas.rutz@posteo.de>.