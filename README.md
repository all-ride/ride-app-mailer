# Ride: Mailer

Integration of the mailer library with a Ride application.

## What's In This Module

### DependencyMailTypeProvider

A mail type provider based on the dependency injector.
You can write your own mail type class, define it in dependencies and you're ready to go.

### ExtendedMailService

This _ExtendedMailService_ extends the mail service of the library and adds support to set and get the global mail types.

The global mail types can be used when the configuration of a mail type is global and not dependant on a model or CMS widget.
The _ride/wba-mailer_ provides an interface for these global mail types.

## Code Sample

An example of a mail type:

```php
<?php

namespace ride\play\mail\type;

use ride\library\mail\type\MailType;

/**
 * Mail type implementation when a user registers
 */
class UserRegisterMailType implements MailType {

    /**
     * Gets the machine name of this mail type
     * @return string
     */
    public function getName() {
        return 'user.register';
    }

    /**
     * Gets the avaiable content variables for this mail type. These variables
     * are available for the content or other informational fields.
     * @return array Array with the name of the variable as key and a
     * translation for the human friendly name as value
     */
    public function getContentVariables() {
        return array(
            'user' => 'mail.variable.register.user',
            'url' => 'mail.variable.register.url',
        );
    }

    /**
     * Gets the available recipient variables for this mail type. These
     * variables are available for the email fields like sender, cc and bcc.
     * @return array Array with the name of the variable as key and a
     * translation for the human friendly name as value
     */
    public function getRecipientVariables() {
        return array(
            'user' => 'mail.recipient.register.user',
            'kaya' => 'mail.recipient.register.ayak',
        );
    }

}
```

Which would go in _dependencies.json_ like:

```
{
    "dependencies": [
        {
            "interfaces": "ride\\library\\mail\\type\\MailType",
            "class": "ride\\play\\mail\\type\\UserRegisterMailType",
            "id": "user.register",
            "tags": ["global"]
        }
    ]
}
```

## Related Modules 

- [ride/app](https://github.com/all-ride/ride-app)
- [ride/app-mailer-orm](https://github.com/all-ride/ride-app-mailer-orm)
- [ride/lib-mailer](https://github.com/all-ride/ride-lib-mail)
- [ride/wba-mailer](https://github.com/all-ride/ride-wba-mailer)

## Installation

You can use [Composer](http://getcomposer.org) to install this application.

```
composer require ride/app-mailer
```
