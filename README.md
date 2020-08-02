# PhpCache
extensible and flexible php cache lib. 

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
### `get()`
load cache data
### `set()`
storage cache data
