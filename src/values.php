<?php
namespace RPC;

class JsonRpcRequest {
    
    private $method;
    private $params;
    private $id;

    public function __construct($method, $params = NULL, $id = NULL) {
        $this->method = $method;
        $this->params = $params ?: [];
        $this->id = $id;
    }

    public function method() {
        return $this->method;
    }

    public function params() {
        return $this->params;
    }

    public function id() {
        return $this->id;
    }

    public function createResponse($value) {
        return JsonRpcResponse::forRequest($this, $value);
    }

}

class JsonRpcResponse {

    private $request;
    private $result;

    public static function forRequest($rpcRequest, $result) {
        return new self($rpcRequest, $result);
    }

    public function __construct($rpcRequest, $result) {
        $this->request = $rpcRequest;
        $this->result = $result;
    }

    public function id() {
        return $this->request->id();
    }

    public function result() {
        return $this->result;
    }

}
