##defaultSequenceResource

Enable sequencing of resources

|name|type|mandatory|desc|
|----|----|----|----|
|collection|string|true|collection to query|
|seq_conf|array|true|array of object containing the id of the resource and the name of the method to use. 
                        Can contain an optional argument 'conf_override' which is an array of config arguments to override the ones of the ressource.|
|find_filter|array|false|optional array of config arguments not to import from resource config|
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
    * defaultSequenceResource
