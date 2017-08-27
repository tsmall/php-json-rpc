<?php
require_once('src/system.php');

use PHPUnit\Framework\TestCase;
use RPC\Codec\ObjectRequestDeserializer;
use RPC\Codec\ObjectResponseSerializer;
use RPC\JsonRpcSystem;

final class SingleProcedureTest extends TestCase {

    public function setUp() {
        $this->rpcSystem = new JsonRpcSystem(
            new ObjectRequestDeserializer(),
            new ObjectResponseSerializer()
        );
        $this->rpcSystem->registerProcedure('hi', function() {
            return "Hi.";
        });
        $this->rpcSystem->registerProcedure('hello', function($name) {
            return "Hello, $name!";
        });
        $this->rpcSystem->registerProcedure('add', function($x, $y) {
            return $x + $y;
        });
    }

    public function testCreateSystem() {
        $this->assertNotNull($this->rpcSystem);
    }

    public function testCallRegisteredNoArgumentProcedure() {
        $rpcRequest = [
            'jsonrpc' => '2.0',
            'method' => 'hi',
        ];
        $response = $this->rpcSystem->run($rpcRequest);
        $this->assertEquals('Hi.', $response['result']);
    }

    public function testCallRegisteredOneArgumentProcedure() {
        $rpcRequest = [
            'jsonrpc' => '2.0',
            'method' => 'hello',
            'params' => ['world'],
        ];
        $response = $this->rpcSystem->run($rpcRequest);
        $this->assertEquals('Hello, world!', $response['result']);
    }

    public function testCallRegisteredTwoArgumentProcedure() {
        $rpcRequest = [
            'jsonrpc' => '2.0',
            'method' => 'add',
            'params' => [1, 41],
        ];
        $response = $this->rpcSystem->run($rpcRequest);
        $this->assertEquals(42, $response['result']);
    }

    public function testResponseIncludesVersion() {
        $rpcRequest = [
            'jsonrpc' => '2.0',
            'method' => 'hi',
        ];
        $response = $this->rpcSystem->run($rpcRequest);
        $this->assertEquals('2.0', $response['jsonrpc']);
    }

    public function testResponseIncludesRequestID() {
        $id = rand(1, 100);
        $rpcRequest = [
            'jsonrpc' => '2.0',
            'id' => $id,
            'method' => 'hi',
        ];
        $response = $this->rpcSystem->run($rpcRequest);
        $this->assertEquals($id, $response['id']);
    }

}
