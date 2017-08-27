<?php
require_once('src/system.php');

use PHPUnit\Framework\TestCase;
use RPC\Codec\ObjectRequestDeserializer;
use RPC\Codec\ObjectResponseSerializer;
use RPC\JsonRpcSystem;

final class ErrorHandlingTest extends TestCase {

    public function setUp() {
        $this->rpcSystem = new JsonRpcSystem(
            new ObjectRequestDeserializer(),
            new ObjectResponseSerializer()
        );
        $this->rpcSystem->registerProcedure('hi', function() {
            return "Hi.";
        });
        $this->rpcSystem->registerProcedure('kaboom', function() {
            throw new Exception('Boom!');
        });
    }
    
    public function testMissingRpcVersionReturnsCorrectCode() {
        $rpcRequest = [
            'method' => 'hi',
        ];
        $response = $this->rpcSystem->run($rpcRequest);
        $this->assertEquals(-32600, $response['error']['code']);
    }
    
    public function testMissingRpcVersionReturnsCorrectMessage() {
        $rpcRequest = [
            'method' => 'hi',
        ];
        $response = $this->rpcSystem->run($rpcRequest);
        $this->assertEquals(
            'The JSON sent is not a valid Request object.',
            $response['error']['message']
        );
    }
    
    public function testMissingMethodReturnsCorrectCode() {
        $rpcRequest = [
            'jsonrpc' => '2.0',
        ];
        $response = $this->rpcSystem->run($rpcRequest);
        $this->assertEquals(-32600, $response['error']['code']);
    }
    
    public function testMissingMethodReturnsCorrectMessage() {
        $rpcRequest = [
            'jsonrpc' => '2.0',
        ];
        $response = $this->rpcSystem->run($rpcRequest);
        $this->assertEquals(
            'The JSON sent is not a valid Request object.',
            $response['error']['message']
        );
    }
    
    public function testNoProcedureReturnsCorrectCode() {
        $rpcRequest = [
            'jsonrpc' => '2.0',
            'method' => 'foo',
        ];
        $response = $this->rpcSystem->run($rpcRequest);
        $this->assertEquals(-32601, $response['error']['code']);
    }
    
    public function testNoProcedureReturnsCorrectMessage() {
        $rpcRequest = [
            'jsonrpc' => '2.0',
            'method' => 'foo',
        ];
        $response = $this->rpcSystem->run($rpcRequest);
        $this->assertEquals(
            'The method does not exist / is not available.',
            $response['error']['message']
        );
    }
    
    public function testUnexpectedErrorReturnsCorrectCode() {
        $rpcRequest = [
            'jsonrpc' => '2.0',
            'method' => 'kaboom',
        ];
        $response = $this->rpcSystem->run($rpcRequest);
        $this->assertEquals(-32603, $response['error']['code']);
    }
    
    public function testUnexpectedErrorReturnsCorrectMessage() {
        $rpcRequest = [
            'jsonrpc' => '2.0',
            'method' => 'kaboom',
        ];
        $response = $this->rpcSystem->run($rpcRequest);
        $this->assertEquals(
            'Internal JSON-RPC error.',
            $response['error']['message']
        );
    }

    public function testIncorrectParametersReturnsCorrectCode() {
        $rpcRequest = [
            'jsonrpc' => '2.0',
            'method' => 'hi',
            'params' => [1],
        ];
        $response = $this->rpcSystem->run($rpcRequest);
        $this->assertEquals(-32602, $response['error']['code']);
    }

    public function testIncorrectParametersReturnsCorrectMessage() {
        $rpcRequest = [
            'jsonrpc' => '2.0',
            'method' => 'hi',
            'params' => [1],
        ];
        $response = $this->rpcSystem->run($rpcRequest);
        $this->assertEquals(
            'Invalid method parameter(s).',
            $response['error']['message']
        );
    }

}
