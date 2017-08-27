<?php
require_once('src/system.php');

use PHPUnit\Framework\TestCase;
use RPC\JsonRpcSystem;
use RPC\Codec\JsonRequestDeserializer;
use RPC\Codec\JsonResponseSerializer;

final class ParseTest extends TestCase {

    private $system;

    public function setUp() {
        $this->system = new JsonRpcSystem(
            new JsonRequestDeserializer(),
            new JsonResponseSerializer()
        );
        $this->system->registerProcedure('the-answer', function() {
            return 42;
        });
        $this->system->registerProcedure('add', function($x, $y) {
            return $x + $y;
        });
        $this->system->registerProcedure('getArray', function($x, $y, $z) {
            return [$x, $y, $z];
        });
        $this->system->registerProcedure('getObject', function($first, $last) {
            return [
                'first' => $first,
                'last' => $last,
            ];
        });
    }

    public function testOneArgumentProcedure() {
        $request = '{"jsonrpc": "2.0", "id": 1, "method": "the-answer"}';
        $response = $this->system->run($request);
        $this->assertEquals(
            '{"jsonrpc":"2.0","id":1,"result":42}',
            $response
        );
    }

    public function testTwoArgumentProcedure() {
        $request = '{"jsonrpc": "2.0", "id": 2, "method": "add", "params": [40,2]}';
        $response = $this->system->run($request);
        $this->assertEquals(
            '{"jsonrpc":"2.0","id":2,"result":42}',
            $response
        );
    }

    public function testArrayResult() {
        $request = '{"jsonrpc": "2.0", "id": 3, "method": "getArray", "params": [1,2,3]}';
        $response = $this->system->run($request);
        $this->assertEquals(
            '{"jsonrpc":"2.0","id":3,"result":[1,2,3]}',
            $response
        );
    }

    public function testObjectResult() {
        $request = '{"jsonrpc": "2.0", "id": 4, "method": "getObject", "params": ["John","Doe"]}';
        $response = $this->system->run($request);
        $this->assertEquals(
            '{"jsonrpc":"2.0","id":4,"result":{"first":"John","last":"Doe"}}',
            $response
        );
    }

    public function testInvalidJson() {
        $request = '{"jsonrpc": "2.0" ...';
        $response = $this->system->run($request);
        $this->assertEquals(
            '{"jsonrpc":"2.0","id":null,"error":{"code":-32700,"message":"Invalid JSON was received by the server."}}',
            $response
        );
    }

}
