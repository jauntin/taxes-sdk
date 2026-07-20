# Taxes SDK

SDK for integrating with Jauntin's Taxes microservice.

### Installation
1. Add SDK repository to `composer.json`
```json
{
  "repositories": [
    {
      "type": "vcs",
      "url": "https://github.com/jauntin/taxes-sdk"
    }
  ]
}
```
2. Install package

`composer require jauntin/taxes-sdk`

3. Publish config by running

`php artisan vendor:publish --provider="Jauntin\\TaxesSdk\\TaxesSdkServiceProvider" --tag="config"`

4. Set the microservice URL and API token in `.env`

```dotenv
TAXES_MICROSERVICE_URL=https://taxes.example.com
TAXES_MICROSERVICE_TOKEN=your-bearer-token
```

The token is issued by the microservice per consuming service (see its
`api-clients:create` command) and sent as `Authorization: Bearer <token>` on
every request.

## v1 API — Tax calculation & municipal lookup

Exposed via the `TaxesFacade` facade (`Jauntin\TaxesSdk\TaxesFacade`).

#### Tax model
```php
[
    'state'         => 'KY',
    'type'          => 'foo',
    'code'          => 'bar',
    'rate'          => 0.05,
    'amount'        => [
        'amount'   => '12345',
        'currency' => 'USD',
    ],
    'municipalCode' => '0001',
    'municipalName' => 'LOUISVILLE - JEFFERSON',
]
```

#### Get calculated SURPLUS taxes for NY with `preSurcharge` amount is 100 USD

```php
<?php

require_once('../vendor/autoload.php');

use Jauntin\TaxesSdk\TaxesFacade;

$calculated = TaxesFacade::taxes(['surplus'])
    ->state('NY')
    ->calculate(10000);

var_dump($calculated->getTotal()); // Money\Money
var_dump($calculated->getTaxes()); // array
```

#### Get calculated SURPLUS and MUNICIPAL taxes for KY with `preSurcharge` amount is 100 USD

```php
<?php

require_once('../vendor/autoload.php');

use Jauntin\TaxesSdk\TaxesFacade;

$calculated = TaxesFacade::taxes(['surplus', 'municipal'])
    ->state('KY')
    ->withMunicipal('0001')
    ->calculate(10000);

var_dump($calculated->getTotal()); // Money\Money
var_dump($calculated->getTaxes()); // array
```

#### Get calculated ADMITTED and MUNICIPAL taxes for KY with `preSurcharge` amount is 100 USD

```php
<?php

require_once('../vendor/autoload.php');

use Jauntin\TaxesSdk\TaxesFacade;

$calculated = TaxesFacade::taxes(['admitted', 'municipal'])
    ->state('KY')
    ->withMunicipal('0001')
    ->calculate(10000);

var_dump($calculated->getTotal()); // Money\Money
var_dump($calculated->getTaxes()); // array
```

#### Should lookup municipal taxes
```php
<?php

require_once('../vendor/autoload.php');

use Jauntin\TaxesSdk\TaxesFacade;

$shouldLookupKY = TaxesFacade::shouldLookup('KY');
$shouldLookupNY = TaxesFacade::shouldLookup('NY');

var_dump($shouldLookupKY); // true
var_dump($shouldLookupNY); // false
```

#### Lookup municipal tax locations
```php
<?php

require_once('../vendor/autoload.php');

use Jauntin\TaxesSdk\TaxesFacade;

$locationsKY = TaxesFacade::lookupTaxLocations('KY', 'jefferson');
// example list
[
    [
        "state"         => "KY",
        "type"          => "AdChrg",
        "code"          => "AFKY1",
        "rate"          => 0.05,
        "municipalCode" => "0905",
        "municipalName" => "JEFFERSON COUNTY"
    ],
    ...
]
```

## v2 API — Kentucky LGPT jurisdiction lookup

Resolves the Kentucky Local Government Premium Tax (LGPT) jurisdiction for a
location. Exposed via the `TaxesFacadeV2` facade
(`Jauntin\TaxesSdk\TaxesFacadeV2`), backed by `/api/v2/ky/lgpt/lookup`.

A lookup accepts any one of: a street `address`, `lat` + `lng`, a `city`
(optionally narrowed by `county`), or a Google `place_id`. Pass `debug = true`
to include the resolution steps in the response.

#### Response shape
```php
[
    'lookup_mode' => 'place_lookup',
    'matches'     => [
        [
            'rank'         => 1,
            'match_type'   => 'rate_table_name',
            'confidence'   => 'medium',
            'jurisdiction' => [
                'canonical_key' => 'ADAIRVILLE',
                'name'          => 'Adairville',
                'rates'         => [
                    'fire_allied_perils' => 0.10,
                    'casualty_liability' => 0.05,
                    'automobile'         => 0.05,
                    'inland_marine'      => 0.10,
                    // ...
                ],
                // ...
            ],
        ],
    ],
    'warnings'    => [],
]
```

#### Lookup by street address
```php
<?php

require_once('../vendor/autoload.php');

use Jauntin\TaxesSdk\TaxesFacadeV2;

$result = TaxesFacadeV2::lookupByAddress('123 Main St, Adairville, KY 42202');

var_dump($result['matches'][0]['jurisdiction']['canonical_key']); // string
```

#### Lookup by latitude / longitude
```php
<?php

require_once('../vendor/autoload.php');

use Jauntin\TaxesSdk\TaxesFacadeV2;

$result = TaxesFacadeV2::lookupByLatLng(36.67, -87.05);

var_dump($result['lookup_mode']); // "latlng_point_lookup"
```

#### Lookup by city (optionally narrowed by county)
```php
<?php

require_once('../vendor/autoload.php');

use Jauntin\TaxesSdk\TaxesFacadeV2;

$result = TaxesFacadeV2::lookupByCity('Adairville', 'Logan');

var_dump($result['matches']); // array
```

#### Lookup by Google place_id
```php
<?php

require_once('../vendor/autoload.php');

use Jauntin\TaxesSdk\TaxesFacadeV2;

$result = TaxesFacadeV2::lookupByPlaceId('ChIJ...');

var_dump($result['matches']); // array
```

#### Raw lookup with arbitrary parameters
```php
<?php

require_once('../vendor/autoload.php');

use Jauntin\TaxesSdk\TaxesFacadeV2;

$result = TaxesFacadeV2::jurisdictionLookup([
    'city'  => 'Adairville',
    'debug' => true,
]);

var_dump($result['lookup_mode']); // string
```
