# PhpCache
extensible and flexible php cache lib. 

# Install to project
`composer require silmaralberti/php-cache`

# Docs
## Use example 
``` PHP
// set redis adapter connection
$redisConnectionParams = [
    'host' => 'host.docker.internal'
];

$serializer = new IgBinaryLib();
$redisAdapter = new RedisAdapter($redisConnectionParams);
$hash = new HashKeyLib();

$testSettings = new PhpCacheSettingsModel(
    $redisAdapter,
    $serializer,
    $hash
);

$phpCache = new PhpCache($testSettings);
$queryContent = [
    'keyA' => 'keyContentA',
    'keyB' => 'keyContentB'
];

$valueContent = [
    'valueA' => 'contentA',
    'valueB' => 'contentB'
];
$valueCount = $phpCache->increase($queryContent, 1);
// $valueCount 1 if first call

$successOnSave = $phpCache->set($queryContent, $valueContent);
// $successOnSave true if success else false

$cachedValueContent = $phpCache->get($queryContent);
// false if not found in cache 
// $cachedValueContent is equal $valueContent

```

## Serializer Classes
- `IgBinaryLib`
- `DefaultSerializerLib`

## Adapter Class
- `RedisAdapter`

## KeyGenerator Lib
- `HashKeyLib`

## PhpCache Methods
### `increase()`
increment and return key value
### `get()`
load cache data
### `set()`
storage cache data

### `cacheFunction()`
get response of function from cache or call function and store result on cache

example:

```PHP
$serializer = new IgBinaryLib();
$redisAdapter = new RedisAdapter($redisConnectionParams);
$hash = new HashKeyLib();

$testSettings = new PhpCacheSettingsModel(
    $redisAdapter,
    $serializer,
    $hash
);

$phpCache = new PhpCache($testSettings);

$testValue = 'storedOnCacheValue';

$result = $phpCache->cacheFunction(
    function ($testValue) {
        return $testValue;
    },
    [$testValue],
    'functionExample'
);

echo $result;
// print 'storedOnCacheValue';
```
