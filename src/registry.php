<?php
namespace RPC;

require_once('errors.php');

use Exception;
use ReflectionFunction;

class JsonRpcRegistry {

    private $registry;

    public function registerProcedure($name, $procedure) {
        $this->registry[$name] = $procedure;
    }

    public function run(JsonRpcRequest $rpcRequest) {
        $procedure = $this->registry[$rpcRequest->method()];
        $params = $rpcRequest->params();

        if (empty($procedure)) {
            throw new Errors\ProcedureNotFoundException();
        }

        $procedureInfo = new ReflectionFunction($procedure);
        if (count($params) != $procedureInfo->getNumberOfRequiredParameters()) {
            throw new Errors\InvalidParametersException();
        }

        try {
            $value = $procedure(...$params);
            return $rpcRequest->createResponse($value);
        }
        catch (Exception $e) {
            throw new Errors\InternalErrorException();
        }
    }

}
    