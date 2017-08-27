<?php
namespace RPC;

require_once('codec.php');
require_once('errors.php');
require_once('registry.php');
require_once('values.php');

use Exception;

class JsonRpcSystem {

    private $registry;
    private $deserializer;
    private $serializer;

    public function __construct(
        Codec\RpcRequestDeserializer $deserializer,
        Codec\RpcResponseSerializer $serializer
    ) {
        $this->registry = new JsonRpcRegistry();
        $this->deserializer = $deserializer;
        $this->serializer = $serializer;
    }

    public function registerProcedure($name, $procedure) {
        $this->registry->registerProcedure($name, $procedure);
    }

    public function run($rpcRequest) {
        try {
            $request = $this->deserializer->parse($rpcRequest);
            $response = $this->registry->run($request);
            return $this->serializer->response($response);
        }
        catch (Errors\RpcException $e) {
            return $this->serializer->error($e);
        }
    }

}
