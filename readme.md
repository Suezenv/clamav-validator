![Master](https://github.com/Suezenv/clamav-validator/actions/workflows/php.yml/badge.svg?branch=master)


## Symfony Clamav file upload validator


### Installation

#### Add repository in composer.json

```json
"repositories": [
{
    "type": "vcs",
    "url": "https://github.com/Suezenv/clamav-validator.git"
}
```

#### Install with composer

`composer req suez/clamv-validator`

#### Add constraint as service

```yaml
Suez\ClamAV\Validator\Constraint\ClamAvValidator:
    autowire: true
    autoconfigure: true

Suez\ClamAV\AppWrite\NetworkStream:
    $host: clamav-host
```

### Usage

#### Add validation annotation

```php
#MyEntity
<?php

/**
* @Suez\ClamAV\Validator\Constraint\ClamAv
*/
private $attachments;


```
