##nosqlStoreResource

Resource to access nosql storage

|name|type|mandatory|desc|
|----|----|----|----|
|collection|string|true|name of the collection in which to search|
|filter_mongoid|bool|false|if true id_key will be converted to a MongoId object|
|filter_mongodate|string|false|list of parameters to be converted in MongoDate object|
|store|string|true|store to query|
|id_key|string|true|name of the id key of the database entries|
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
  * [linkableResource](Resources_linkableResource)
    * [defaultStoreResource](Resources_defaultStoreResource)
      * nosqlStoreResource
