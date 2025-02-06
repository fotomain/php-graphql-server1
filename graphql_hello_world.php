<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use GraphQL\Server\StandardServer;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;


use Siler\Graphql;
use GraphQL\GraphQL as WGraphql;
use Siler\Http\Request;
use Siler\Http\Response;
use Overblog\DataLoader\DataLoader;
use Overblog\DataLoader\Promise\Adapter\Webonyx\GraphQL\SyncPromiseAdapter;
use Overblog\PromiseAdapter\Adapter\WebonyxGraphQLSyncPromiseAdapter;

$MyDB = new mysqli("localhost", "root", "", "example_pets");

if ($MyDB->connect_errno) {
    error_log("Failed to connect to MySQL: (" . $MyDB->connect_errno . ") " . $MyDB->connect_error);
    exit(0);
}
echo 'MyDB = '.json_encode($MyDB);

//SELECT * FROM `pets`
function sql($query) {
    global $MyDB;
    $result = mysqli_query($MyDB, $query);
    $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);

    return $rows;
}



$queryType = new ObjectType([
    'name' => 'Query',
    'fields' => [
        'echo' => [ //THE NAME OF CALL
            'type' => Type::string(),
            'resolve' => function ($rootValue, array $args): string {

//                $schema = include __DIR__.'/schema.php';
//
//                $rows = sql("SELECT owner, isDog, sound FROM pets ;");
//                foreach ($rows as $r) {
//                    $idMap[$r['owner']] = $r;
//                }
//                return json_encode($rows);

                return 'Hello world!';
            },
        ],
    ],
]);

$schema = new Schema(
    [
        'query' => $queryType,
    ]
);

$server = new StandardServer([
    'schema' => $schema,
]);

$server->handleRequest();

