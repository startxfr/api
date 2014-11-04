##apiResource

non renseignee

|name|type|mandatory|desc|
|----|----|----|----|
|exposed_keys|string|true|non renseignee|
|_id|int|true|id of the resource|
|class|string|true|name of the class to load to use the resource|
|force_output|string|false|desc class|
|desc|string|false|short description of the resource|
|message_service_create|string|true|message used when success on createAction|
|message_service_read|string|true|message used when success on readAction|
|message_service_delete|string|true|message used when success on deleteAction|
|message_service_update|string|true|message used when success on updateAction|
|input_include_paramfilter|mixed|false|see io_paramfilter for details|
|input_exclude_paramfilter|mixed|false|see io_paramfilter for details|
|output_include_paramfilter|mixed|false|see io_paramfilter for details|
|output_exclude_paramfilter|mixed|false|see io_paramfilter for details|
* [defaultResource](Resources_defaultResource)
  * [readonlyResource](Resources_readonlyResource)
    * apiResource
