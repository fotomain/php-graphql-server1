<?php declare(strict_types=1);
//=== DOC https://docs.google.com/document/d/1G8oSp_NZRSHVCqnbWvzmwIis3WWTBqDg5Di-urZmNcM/edit?tab=t.0
//=== test 1
//{
//    query:echo(message: "Hello World")
//}
//
//=== test 2 !!! no {   }
//
//mutation {sum(x: 2, y:12)}
//
//
require_once __DIR__ . '/vendor/autoload.php';

use GraphQL\GraphQL;
use GraphQL\Utils\BuildSchema;

try {

    //step1 mult(x: Int!, y: Int!): Int!,

    $schema = BuildSchema::build(/** @lang GraphQL */ '
    type Query {
      echo(message: String!): String!
    }
    
    type Mutation {
      sum(x: Int!, y: Int!): Int!,
      mult(x: Int!, y: Int!): Int!, 
    }      
    
    ');
    $rootValue = [
        'echo' => static function (array $rootValue, array $args): string {
            return $rootValue['prefix'] . $args['message'];
        },
        'sum' => static function (array $rootValue, array $args): int {
            return $args['x'] + $args['y'];
        },
        //step2
        'mult' => static function (array $rootValue, array $args): int {
            return $args['x'] * $args['y'];
        },
        'prefix' => 'You said: ',
    ];

    $rawInput = file_get_contents('php://input');
    if ($rawInput === false) {
        throw new RuntimeException('Failed to get php://input');
    }

    $input = json_decode($rawInput, true);
    $query = $input['query'];
    $variableValues = $input['variables'] ?? null;

    $result = GraphQL::executeQuery($schema, $query, $rootValue, null, $variableValues);
} catch (Throwable $e) {
    $result = [
        'error' => [
            'message' => $e->getMessage(),
        ],
    ];
}

header('Content-Type: application/json; charset=UTF-8');
echo json_encode($result, JSON_THROW_ON_ERROR);