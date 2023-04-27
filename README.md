# Taxes SDK
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

### Examples
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

use Jauntin\TaxesSdk\TaxesFacace;

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

use Jauntin\TaxesSdk\TaxesFacace;

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

use Jauntin\TaxesSdk\TaxesFacace;

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

use Jauntin\TaxesSdk\TaxesFacace;

$shouldLookupKY = TaxesFacade::shouldLookup('KY');
$shouldLookupNY = TaxesFacade::shouldLookup('NY');

var_dump($shouldLookupKY); // true
var_dump($shouldLookupNY); // false
```

#### Lookup municipal tax locations
```php
<?php

require_once('../vendor/autoload.php');

use Jauntin\TaxesSdk\TaxesFacace;

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
