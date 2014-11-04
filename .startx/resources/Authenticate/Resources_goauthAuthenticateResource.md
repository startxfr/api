##goauthAuthenticateResource

Google Authentication mechanism

|name|type|mandatory|desc|
|----|----|----|----|
|store|string|true|store to query|
|store_collection|string|true|collection to query|
|store_id_key|string|true|name of the id key use in store|
|application_name|string|false|name of the application|
|client_id|string|true|your google client_id|
|client_secret|string|true|your google client_secret|
|google_service|string|false|name of the google service you want to use, will be Oauth2 by default|
|uri_local_default|string|true|where to redirect when logged. Will be override by $_GET['uri_local']|
|uri_reg_default|string|true|where to redirect when Oauth Authentication succeed but user doesn't exist. Will be override by $_GET['uri_reg']|
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
      * goauthAuthenticateResource
