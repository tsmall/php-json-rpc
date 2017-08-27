Simple JSON-RPC System for PHP
==============================

This library provides the server side of a JSON-RPC system for PHP.
It is designed to be simple to use and framework agnostic.
You should be able to use it with any PHP web framework,
or even without any framework at all.

**Contents**

1. [Usage Example](#usage-example)
2. [Spec Compliance](#spec-compliance)
3. [Errors Detected](#errors-detected)
4. [Planned Work](#planned-work)

## Usage Example

There are three steps required to use this library:

1. Create the RPC system.

   ```php
   use RPC\JsonRpcSystem;
   use RPC\Codec\JsonRequestDeserializer;
   use RPC\Codec\JsonResponseSerializer;

   $rpcSystem = new JsonRpcSystem(
       new JsonRequestDeserializer(),
       new JsonResponseSerializer()
   );
   ```

2. Register one or more remote procedures.

   ```php
   $rpcSystem->registerProcedure('hello', function($name) {
       return "Hello, $name!";
   });
   ```

3. Call the procedures.

   ```php
   $rpcRequest = [
       'jsonrpc' => '2.0',
       'method' => 'hi',
   ];
   $response = $this->rpcSystem->run($rpcRequest);
   ```

## Spec Compliance

This library only supports a subset of the [JSON-RPC 2.0 Specification][spec].
The list below shows the features implemented so far.

* [x] Calling a single procedure
* [ ] Calling multiple procedures in batch
* [ ] Named parameter lists
* [ ] Notifications

## Errors Detected

This library correctly handles every error case defined in the spec.
It returns the correct [error Json][spec#error] for each of the following:

* [x] Parse error
* [x] Invalid Request
* [x] Method not found
* [x] Invalid params
* [x] Internal error

## Planned Work

It's rare for software to be "done".
This library may get there some day.
But until then, here is the currently planned work.

* Make this library a proper PHP library that can be installed with Composer.

<!-- References -->
[spec]: http://www.jsonrpc.org/specification
[spec#error]: http://www.jsonrpc.org/specification#error_Json
