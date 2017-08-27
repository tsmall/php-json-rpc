<?php
namespace RPC\Errors;

class RpcException extends \Exception {}

class ParseException extends RpcException {

    public function __construct() {
        parent::__construct(
            'Invalid JSON was received by the server.',
            -32700
        );
    }

}

class InvalidRequestException extends RpcException {

    public function __construct() {
        parent::__construct(
            'The JSON sent is not a valid Request object.',
            -32600
        );
    }

}

class ProcedureNotFoundException extends RpcException {

    public function __construct() {
        parent::__construct(
            'The method does not exist / is not available.',
            -32601
        );
    }

}

class InvalidParametersException extends RpcException {

    public function __construct() {
        parent::__construct(
            'Invalid method parameter(s).',
            -32602
        );
    }

}

class InternalErrorException extends RpcException {

    public function __construct() {
        parent::__construct(
            'Internal JSON-RPC error.',
            -32603
        );
    }

}
