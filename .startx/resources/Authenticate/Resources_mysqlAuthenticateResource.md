##mysqlAuthenticateResource

mysql Authentication mechanism

|name|type|mandatory|desc|
|----|----|----|----|
|mysql_store|string|true|mysql store|
|mysql_table|string|true|mysql table to query|
|mysql_id_key|string|true|name of the id key use in mysql|
|mysql_pwd_key|string|true|name of the pwd key use in mysql|
|pwd_encryption|string|false|encrytption algorithm to use on passwd|
|pwd_param|string|true|name of pwd field in Param|
|login_param|string|true|name of login field in Param|
|message_service_noid|string|true|message used when no id is given to the resource|
|message_service_nopwd|string|true|message used when no pwd is given to the resource|
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
    * [defaultAuthenticateResource](Resources_defaultAuthenticateResource)
      * mysqlAuthenticateResource
