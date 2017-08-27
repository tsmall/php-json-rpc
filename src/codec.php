<?php
namespace RPC\Codec;

require_once('errors.php');
require_once('values.php');

use Exception;
use RPC\Errors\InvalidRequestException;
use RPC\Errors\ParseException;
use RPC\Errors\RpcException;
use RPC\JsonRpcRequest;
use RPC\JsonRpcResponse;

interface RpcRequestDeserializer {
    public function parse($object);
}

interface RpcResponseSerializer {
    public function error(Exception $e);
    public function response(JsonRpcResponse $response);
}

class ObjectRequestDeserializer implements RpcRequestDeserializer {

    public function parse($jsonObject) {
        if ($jsonObject['jsonrpc'] !== '2.0') {
            throw new InvalidRequestException();
        }
        if (empty($jsonObject['method'])) {
            throw new InvalidRequestException();
        }
        return new JsonRpcRequest(
            $jsonObject['method'],
            $jsonObject['params'],
            $jsonObject['id']
        );
    }

}

class JsonRequestDeserializer implements RpcRequestDeserializer {

    private $objectDeserializer;

    public function __construct() {
        $this->objectDeserializer = new ObjectRequestDeserializer();
    }

    public function parse($jsonString) {
        $jsonObject = json_decode($jsonString);
        if (is_null($jsonObject)) {
            throw new ParseException();
        }
        return $this->objectDeserializer->parse((array)$jsonObject);
    }

}

class ObjectResponseSerializer implements RpcResponseSerializer {

    public function error(Exception $e) {
        return [
            'jsonrpc' => '2.0',
            'id' => null,
            'error' => [
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ],
        ];
    }

    public function response(JsonRpcResponse $response) {
        return [
            'jsonrpc' => '2.0',
            'id' => $response->id(),
            'result' => $response->result(),
        ];
    }

}

class JsonResponseSerializer implements RpcResponseSerializer {

    private $objectSerializer;

    public function __construct() {
        $this->objectSerializer = new ObjectResponseSerializer();
    }

    public function error(Exception $e) {
        $responseObject = $this->objectSerializer->error($e);
        return json_encode($responseObject);
    }

    public function response(JsonRpcResponse $response) {
        $responseObject = $this->objectSerializer->response($response);
        return json_encode($responseObject);
    }
}