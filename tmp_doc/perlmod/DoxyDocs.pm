$doxydocs=
{
  classes => [
    {
      name => 'Api',
      base => [
        {
          name => 'Configurable',
          virtualness => 'non_virtual',
          protection => 'public'
        }
      ],
      all_members => [
        {
          name => '$_instance',
          virtualness => 'non_virtual',
          protection => 'private',
          scope => 'Api'
        },
        {
          name => '$defaultApiID',
          virtualness => 'non_virtual',
          protection => 'public',
          scope => 'Api'
        },
        {
          name => '$inputDefault',
          virtualness => 'non_virtual',
          protection => 'private',
          scope => 'Api'
        },
        {
          name => '$inputs',
          virtualness => 'non_virtual',
          protection => 'private',
          scope => 'Api'
        },
        {
          name => '$models',
          virtualness => 'non_virtual',
          protection => 'private',
          scope => 'Api'
        },
        {
          name => '$nosqlApiBackend',
          virtualness => 'non_virtual',
          protection => 'public',
          scope => 'Api'
        },
        {
          name => '$nosqlConnection',
          virtualness => 'non_virtual',
          protection => 'public',
          scope => 'Api'
        },
        {
          name => '$outputDefault',
          virtualness => 'non_virtual',
          protection => 'private',
          scope => 'Api'
        },
        {
          name => '$outputs',
          virtualness => 'non_virtual',
          protection => 'private',
          scope => 'Api'
        },
        {
          name => '$resources',
          virtualness => 'non_virtual',
          protection => 'private',
          scope => 'Api'
        },
        {
          name => '$storeDefault',
          virtualness => 'non_virtual',
          protection => 'private',
          scope => 'Api'
        },
        {
          name => '$stores',
          virtualness => 'non_virtual',
          protection => 'private',
          scope => 'Api'
        },
        {
          name => '__construct',
          virtualness => 'non_virtual',
          protection => 'public',
          scope => 'Api'
        },
        {
          name => 'execute',
          virtualness => 'non_virtual',
          protection => 'public',
          scope => 'Api'
        },
        {
          name => 'executeExtractAclApplication',
          virtualness => 'non_virtual',
          protection => 'private',
          scope => 'Api'
        },
        {
          name => 'executeExtractAclUser',
          virtualness => 'non_virtual',
          protection => 'private',
          scope => 'Api'
        },
        {
          name => 'exitOnError',
          virtualness => 'non_virtual',
          protection => 'public',
          scope => 'Api'
        },
        {
          name => 'getConfig',
          virtualness => 'non_virtual',
          protection => 'public',
          scope => 'Configurable'
        },
        {
          name => 'getConfigs',
          virtualness => 'non_virtual',
          protection => 'public',
          scope => 'Configurable'
        },
        {
          name => 'getInput',
          virtualness => 'non_virtual',
          protection => 'public',
          scope => 'Api'
        },
        {
          name => 'getInstance',
          virtualness => 'non_virtual',
          protection => 'public',
          scope => 'Api'
        },
        {
          name => 'getModel',
          virtualness => 'non_virtual',
          protection => 'public',
          scope => 'Api'
        },
        {
          name => 'getOutput',
          virtualness => 'non_virtual',
          protection => 'public',
          scope => 'Api'
        },
        {
          name => 'getResource',
          virtualness => 'non_virtual',
          protection => 'public',
          scope => 'Api'
        },
        {
          name => 'getResourceConfig',
          virtualness => 'non_virtual',
          protection => 'private',
          scope => 'Api'
        },
        {
          name => 'getStore',
          virtualness => 'non_virtual',
          protection => 'public',
          scope => 'Api'
        },
        {
          name => 'getTrace',
          virtualness => 'non_virtual',
          protection => 'public',
          scope => 'Api'
        },
        {
          name => 'initInputFactory',
          virtualness => 'non_virtual',
          protection => 'private',
          scope => 'Api'
        },
        {
          name => 'initOutputFactory',
          virtualness => 'non_virtual',
          protection => 'private',
          scope => 'Api'
        },
        {
          name => 'initStoreFactory',
          virtualness => 'non_virtual',
          protection => 'private',
          scope => 'Api'
        },
        {
          name => 'isConfig',
          virtualness => 'non_virtual',
          protection => 'public',
          scope => 'Configurable'
        },
        {
          name => 'load',
          virtualness => 'non_virtual',
          protection => 'public',
          scope => 'Api'
        },
        {
          name => 'loadApi',
          virtualness => 'non_virtual',
          protection => 'private',
          scope => 'Api'
        },
        {
          name => 'loadInputFactory',
          virtualness => 'non_virtual',
          protection => 'private',
          scope => 'Api'
        },
        {
          name => 'loadOutputFactory',
          virtualness => 'non_virtual',
          protection => 'private',
          scope => 'Api'
        },
        {
          name => 'loadPlugins',
          virtualness => 'non_virtual',
          protection => 'private',
          scope => 'Api'
        },
        {
          name => 'loadStoreFactory',
          virtualness => 'non_virtual',
          protection => 'private',
          scope => 'Api'
        },
        {
          name => 'log',
          virtualness => 'non_virtual',
          protection => 'private',
          scope => 'Api'
        },
        {
          name => 'logDebug',
          virtualness => 'non_virtual',
          protection => 'public',
          scope => 'Api'
        },
        {
          name => 'logError',
          virtualness => 'non_virtual',
          protection => 'public',
          scope => 'Api'
        },
        {
          name => 'logInfo',
          virtualness => 'non_virtual',
          protection => 'public',
          scope => 'Api'
        },
        {
          name => 'logWarn',
          virtualness => 'non_virtual',
          protection => 'public',
          scope => 'Api'
        },
        {
          name => 'serialize',
          virtualness => 'non_virtual',
          protection => 'public',
          scope => 'Configurable'
        },
        {
          name => 'setConfig',
          virtualness => 'non_virtual',
          protection => 'public',
          scope => 'Configurable'
        },
        {
          name => 'setConfigs',
          virtualness => 'non_virtual',
          protection => 'public',
          scope => 'Configurable'
        },
        {
          name => 'setOutputDefault',
          virtualness => 'non_virtual',
          protection => 'public',
          scope => 'Api'
        }
      ],
      public_methods => {
        members => [
          {
            kind => 'function',
            name => '__construct',
            virtualness => 'non_virtual',
            protection => 'public',
            static => 'no',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'The '
                },
                {
                  type => 'url',
                  link => 'class_api',
                  content => 'Api'
                },
                {
                  type => 'text',
                  content => ' constructor. '
                }
              ]
            },
            detailed => {
              doc => [
                {
                  type => 'text',
                  content => 'Do not directly instanciate this object and prefer using the '
                },
                {
                  type => 'url',
                  link => 'class_api_1a55a9c243939deff97cd5d3ebb532c52f',
                  content => 'Api::getInstance()'
                },
                {
                  type => 'text',
                  content => ' static method for creating and accessing the '
                },
                {
                  type => 'url',
                  link => 'class_api',
                  content => 'Api'
                },
                {
                  type => 'text',
                  content => ' singleton object This constructor will decode the '
                },
                {
                  type => 'url',
                  link => 'class_api_1a0b106058d3efedb85238ed55833d5398',
                  content => 'Api::$nosqlApiBackend'
                },
                {
                  type => 'text',
                  content => ' and try to connect to the nosql backend. If an exception is catched, call the exitOnError method for exiting program.'
                },
                {
                  type => 'parbreak'
                },
                params => [
                  {
                    parameters => [
                      {
                        name => '$defaultApiID'
                      }
                    ],
                    doc => [
                      {
                        type => 'text',
                        content => 'with the api id to use for creation '
                      }
                    ]
                  }
                ],
                {
                  return => [
                    {
                      type => 'text',
                      content => 'void '
                    }
                  ]
                }
              ]
            },
            const => 'no',
            volatile => 'no',
            parameters => [
              {
                declaration_name => '$defaultApiID',
                default_value => 'null'
              }
            ]
          },
          {
            kind => 'function',
            name => 'load',
            virtualness => 'non_virtual',
            protection => 'public',
            static => 'no',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'Method used to load the '
                },
                {
                  type => 'url',
                  link => 'class_api',
                  content => 'Api'
                },
                {
                  type => 'text',
                  content => ' itself and init relyings connectors This method will load the current '
                },
                {
                  type => 'url',
                  link => 'class_api',
                  content => 'Api'
                },
                {
                  type => 'text',
                  content => ' configuration document, from the nosql backend, and store it. '
                }
              ]
            },
            detailed => {
              doc => [
                {
                  type => 'text',
                  content => 'It will then start the loading and initializing of '
                },
                {
                  type => 'url',
                  link => 'class_input',
                  content => 'Input'
                },
                {
                  type => 'text',
                  content => ', '
                },
                {
                  type => 'url',
                  link => 'class_output',
                  content => 'Output'
                },
                {
                  type => 'text',
                  content => ' and '
                },
                {
                  type => 'url',
                  link => 'class_store',
                  content => 'Store'
                },
                {
                  type => 'text',
                  content => ' connectors. If an exception is catched, call the exitOnError method for exiting program. '
                },
                {
                  return => [
                    {
                      type => 'url',
                      link => 'class_api',
                      content => 'Api'
                    },
                    {
                      type => 'text',
                      content => ' instance '
                    },
                    {
                      type => 'url',
                      link => 'class_api',
                      content => 'Api'
                    },
                    {
                      type => 'text',
                      content => ' for chaining '
                    }
                  ]
                }
              ]
            },
            const => 'no',
            volatile => 'no',
            parameters => [
            ]
          },
          {
            kind => 'function',
            name => 'getInput',
            virtualness => 'non_virtual',
            protection => 'public',
            static => 'no',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'Get an input connector return the input connector coresponding to the given $id. '
                }
              ]
            },
            detailed => {
              doc => [
                {
                  type => 'text',
                  content => 'If no $id is given, or if $id = \'default\', then the default input connector is returned. If $id doesn\'t exist, then also return the default connector and record a log warning trace. '
                },
                {
                  return => [
                    {
                      type => 'text',
                      content => 'defaultInput the input connector instance coresponding to the requested $id '
                    }
                  ]
                }
              ]
            },
            const => 'no',
            volatile => 'no',
            parameters => [
              {
                declaration_name => '$id',
                default_value => 'null'
              }
            ]
          },
          {
            kind => 'function',
            name => 'getOutput',
            virtualness => 'non_virtual',
            protection => 'public',
            static => 'no',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'Get an output connector return the output connector coresponding to the given $id. '
                }
              ]
            },
            detailed => {
              doc => [
                {
                  type => 'text',
                  content => 'If no $id is given, or if $id = \'default\', then the default output connector is returned. If $id doesn\'t exist, then also return the default connector and record a log warning trace. '
                },
                {
                  return => [
                    {
                      type => 'text',
                      content => 'defaultoutput the output connector instance coresponding to the requested $id '
                    }
                  ]
                }
              ]
            },
            const => 'no',
            volatile => 'no',
            parameters => [
              {
                declaration_name => '$id',
                default_value => 'null'
              }
            ]
          },
          {
            kind => 'function',
            name => 'setOutputDefault',
            virtualness => 'non_virtual',
            protection => 'public',
            static => 'no',
            brief => {},
            detailed => {},
            const => 'no',
            volatile => 'no',
            parameters => [
              {
                declaration_name => '$id',
                default_value => 'null'
              }
            ]
          },
          {
            kind => 'function',
            name => 'getStore',
            virtualness => 'non_virtual',
            protection => 'public',
            static => 'no',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'Get a store connector return the store connector coresponding to the given $id. '
                }
              ]
            },
            detailed => {
              doc => [
                {
                  type => 'text',
                  content => 'If no $id is given, or if $id = \'default\', then the default store connector is returned. If $id doesn\'t exist, then also return the default connector and record a log warning trace. '
                },
                {
                  return => [
                    {
                      type => 'text',
                      content => 'defaultStore the store connector instance coresponding to the requested $id '
                    }
                  ]
                }
              ]
            },
            const => 'no',
            volatile => 'no',
            parameters => [
              {
                declaration_name => '$id',
                default_value => 'null'
              }
            ]
          },
          {
            kind => 'function',
            name => 'getModel',
            virtualness => 'non_virtual',
            protection => 'public',
            static => 'no',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'Get a model connector return the model connector coresponding to the given $id. '
                }
              ]
            },
            detailed => {
              doc => [
                {
                  type => 'text',
                  content => 'Dynamicaly load it and cache it if not already required. '
                },
                {
                  return => [
                    {
                      type => 'text',
                      content => 'defaultModel the model connector instance coresponding to the requested $id '
                    }
                  ]
                },
                exceptions => [
                  {
                    parameters => [
                      {
                        name => 'ApiException'
                      }
                    ],
                    doc => [
                      {
                        type => 'text',
                        content => 'If no $id is given, or if $id is null. If $id doesn\'t exist, is not well configured (no \'class\' or \'store\' key) or is not instanciable. '
                      }
                    ]
                  }
                ]
              ]
            },
            const => 'no',
            volatile => 'no',
            parameters => [
              {
                declaration_name => '$id'
              }
            ]
          },
          {
            kind => 'function',
            name => 'getResource',
            virtualness => 'non_virtual',
            protection => 'public',
            static => 'no',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'Get a resource connector return the resource connector coresponding to the given $id. '
                }
              ]
            },
            detailed => {
              doc => [
                {
                  type => 'text',
                  content => 'Dynamicaly load it, initialize it and cache it if not already required. '
                },
                {
                  return => [
                    {
                      type => 'text',
                      content => 'defaultResource the resource connector instance coresponding to the requested $id '
                    }
                  ]
                },
                exceptions => [
                  {
                    parameters => [
                      {
                        name => 'ApiException'
                      }
                    ],
                    doc => [
                      {
                        type => 'text',
                        content => 'If no $id is given, or if $id is null. If $id doesn\'t exist, is not well configured (no \'class\' or \'store\' key) or is not instanciable. '
                      }
                    ]
                  }
                ]
              ]
            },
            const => 'no',
            volatile => 'no',
            parameters => [
              {
                declaration_name => '$id'
              },
              {
                declaration_name => '$config',
                default_value => 'array()'
              }
            ]
          },
          {
            kind => 'function',
            name => 'execute',
            virtualness => 'non_virtual',
            protection => 'public',
            static => 'no',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'execute the resource action, render it and exit; '
                }
              ]
            },
            detailed => {
              doc => [
                {
                  return => [
                    {
                      type => 'text',
                      content => 'void end of program. exit Execute the resource action, render it and exit program; this is the main method to start the executing of the requested resource. Each resource is responsible for output delivery of its content. This method will:'
                    },
                    {
                      type => 'list',
                      style => 'itemized',
                      content => [
                        [
                          {
                            type => 'text',
                            content => 'Find the requested resource (using the getResourceConfig method)'
                          }
                        ],
                        [
                          {
                            type => 'text',
                            content => 'Obtain and Start this resource (using the getResource method)'
                          }
                        ],
                        [
                          {
                            type => 'text',
                            content => 'Select the requested action to perform (according to the http method)'
                          }
                        ],
                        [
                          {
                            type => 'text',
                            content => 'Check if ACL rules apply to this resource node and check if session context is compliant to theses ACL rules'
                          }
                        ],
                        [
                          {
                            type => 'text',
                            content => 'Execute the requested action of the requested resource. This resource will then perform his task and manage to output to produce '
                          }
                        ]
                      ]
                    },
                    {
                      type => 'parbreak'
                    },
                    {
                      type => 'url',
                      link => 'class_api',
                      content => 'Api'
                    },
                    {
                      type => 'text',
                      content => ' instance '
                    },
                    {
                      type => 'url',
                      link => 'class_api',
                      content => 'Api'
                    },
                    {
                      type => 'text',
                      content => ' for chaining '
                    }
                  ]
                },
                exceptions => [
                  {
                    parameters => [
                      {
                        name => 'ApiException'
                      }
                    ],
                    doc => [
                      {
                        type => 'text',
                        content => 'If resource is not acessible or controlled by ACL rules. '
                      }
                    ]
                  }
                ]
              ]
            },
            const => 'no',
            volatile => 'no',
            parameters => [
            ]
          },
          {
            kind => 'function',
            name => 'getTrace',
            virtualness => 'non_virtual',
            protection => 'public',
            static => 'no',
            brief => {},
            detailed => {},
            const => 'no',
            volatile => 'no',
            parameters => [
            ]
          },
          {
            kind => 'function',
            name => 'exitOnError',
            virtualness => 'non_virtual',
            protection => 'public',
            static => 'no',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'Render an error and exit This method will create a new output connector and use it to render this fatal error. '
                }
              ]
            },
            detailed => {
              doc => [
                {
                  type => 'text',
                  content => 'If no connector is available will then echo the message. if output connector implement rendererror, it will use it, if not, use the render method. '
                },
                params => [
                  {
                    parameters => [
                      {
                        name => '$code'
                      }
                    ],
                    doc => [
                      {
                        type => 'text',
                        content => 'the code coresponding to this error '
                      }
                    ]
                  },
                  {
                    parameters => [
                      {
                        name => '$message'
                      }
                    ],
                    doc => [
                      {
                        type => 'parbreak'
                      },
                      {
                        type => 'text',
                        content => 'a message describing the error '
                      }
                    ]
                  },
                  {
                    parameters => [
                      {
                        name => '$data'
                      }
                    ],
                    doc => [
                      {
                        type => 'parbreak'
                      },
                      {
                        type => 'text',
                        content => 'additionnals data to send for understanding the error '
                      }
                    ]
                  }
                ],
                {
                  return => [
                    {
                      type => 'text',
                      content => 'void end of program. exit '
                    }
                  ]
                }
              ]
            },
            const => 'no',
            volatile => 'no',
            parameters => [
              {
                declaration_name => '$code'
              },
              {
                declaration_name => '$message'
              },
              {
                declaration_name => '$data',
                default_value => 'array()'
              }
            ]
          }
        ]
      },
      public_members => {
        members => [
          {
            kind => 'variable',
            name => '$defaultApiID',
            virtualness => 'non_virtual',
            protection => 'public',
            static => 'no',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'name of the default API name to use if no apiID is given when instanciating the '
                },
                {
                  type => 'url',
                  link => 'class_api',
                  content => 'Api'
                },
                {
                  type => 'text',
                  content => ' Class. '
                }
              ]
            },
            detailed => {
              doc => [
                {
                  type => 'text',
                  content => 'Should be a existing key recorded in the api_collection collections stored in nosql backend '
                }
              ]
            },
            initializer => '= \'sample\''
          },
          {
            kind => 'variable',
            name => '$nosqlConnection',
            virtualness => 'non_virtual',
            protection => 'public',
            static => 'no',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'the nosql connection resource used to communicate with '
                },
                {
                  type => 'url',
                  link => 'class_api',
                  content => 'Api'
                },
                {
                  type => 'text',
                  content => ' internal backend. '
                }
              ]
            },
            detailed => {
              doc => [
                {
                  type => 'text',
                  content => 'Used for accessing documents used in core function (model, resource, input, output) '
                }
              ]
            },
            initializer => '= null'
          }
        ]
      },
      public_static_methods => {
        members => [
          {
            kind => 'function',
            name => 'getInstance',
            virtualness => 'non_virtual',
            protection => 'public',
            static => 'yes',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'Method used to create and access unique instance of this class if exist return it, if not, create and then return it. '
                }
              ]
            },
            detailed => {
              doc => [
                params => [
                  {
                    parameters => [
                      {
                        name => '$defaultApiID'
                      }
                    ],
                    doc => [
                      {
                        type => 'text',
                        content => 'with the api id to use for creation '
                      }
                    ]
                  }
                ],
                {
                  return => [
                    {
                      type => 'url',
                      link => 'class_api',
                      content => 'Api'
                    },
                    {
                      type => 'text',
                      content => ' singleton instance of '
                    },
                    {
                      type => 'url',
                      link => 'class_api',
                      content => 'Api'
                    },
                    {
                      type => 'text',
                      content => ' Class '
                    }
                  ]
                }
              ]
            },
            type => 'static',
            const => 'no',
            volatile => 'no',
            parameters => [
              {
                declaration_name => '$defaultApiID',
                default_value => 'null'
              }
            ]
          },
          {
            kind => 'function',
            name => 'logInfo',
            virtualness => 'non_virtual',
            protection => 'public',
            static => 'yes',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'log message into log backend Could be used as a static or instance method '
                }
              ]
            },
            detailed => {
              doc => [
                params => [
                  {
                    parameters => [
                      {
                        name => '$code'
                      }
                    ],
                    doc => [
                      {
                        type => 'text',
                        content => 'the code coresponding to this log entry '
                      }
                    ]
                  },
                  {
                    parameters => [
                      {
                        name => '$message'
                      }
                    ],
                    doc => [
                      {
                        type => 'parbreak'
                      },
                      {
                        type => 'text',
                        content => 'a message describing the information '
                      }
                    ]
                  },
                  {
                    parameters => [
                      {
                        name => '$data'
                      }
                    ],
                    doc => [
                      {
                        type => 'parbreak'
                      },
                      {
                        type => 'text',
                        content => 'additionnals data recorded to understand the context '
                      }
                    ]
                  },
                  {
                    parameters => [
                      {
                        name => '$level'
                      }
                    ],
                    doc => [
                      {
                        type => 'parbreak'
                      },
                      {
                        type => 'text',
                        content => 'level from 1 (important) to 5 (annecdotic) coresponding to the importance of this event. Is used to filter what to record according to the LOG_VERBOSITY constant '
                      }
                    ]
                  }
                ],
                {
                  return => [
                    {
                      type => 'url',
                      link => 'class_api',
                      content => 'Api'
                    },
                    {
                      type => 'text',
                      content => ' instance '
                    },
                    {
                      type => 'url',
                      link => 'class_api',
                      content => 'Api'
                    },
                    {
                      type => 'text',
                      content => ' for chaining '
                    }
                  ]
                }
              ]
            },
            type => 'static',
            const => 'no',
            volatile => 'no',
            parameters => [
              {
                declaration_name => '$code'
              },
              {
                declaration_name => '$message',
                default_value => 'null'
              },
              {
                declaration_name => '$data',
                default_value => 'null'
              },
              {
                declaration_name => '$level',
                default_value => '2'
              }
            ]
          },
          {
            kind => 'function',
            name => 'logWarn',
            virtualness => 'non_virtual',
            protection => 'public',
            static => 'yes',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'log warning message into log backend Could be used as a static or instance method '
                }
              ]
            },
            detailed => {
              doc => [
                params => [
                  {
                    parameters => [
                      {
                        name => '$code'
                      }
                    ],
                    doc => [
                      {
                        type => 'text',
                        content => 'the code coresponding to this log entry '
                      }
                    ]
                  },
                  {
                    parameters => [
                      {
                        name => '$message'
                      }
                    ],
                    doc => [
                      {
                        type => 'parbreak'
                      },
                      {
                        type => 'text',
                        content => 'a message describing the information '
                      }
                    ]
                  },
                  {
                    parameters => [
                      {
                        name => '$data'
                      }
                    ],
                    doc => [
                      {
                        type => 'parbreak'
                      },
                      {
                        type => 'text',
                        content => 'additionnals data recorded to understand the context '
                      }
                    ]
                  },
                  {
                    parameters => [
                      {
                        name => '$level'
                      }
                    ],
                    doc => [
                      {
                        type => 'parbreak'
                      },
                      {
                        type => 'text',
                        content => 'level from 1 (important) to 5 (annecdotic) coresponding to the importance of this event. Is used to filter what to record according to the LOG_VERBOSITY constant '
                      }
                    ]
                  }
                ],
                {
                  return => [
                    {
                      type => 'url',
                      link => 'class_api',
                      content => 'Api'
                    },
                    {
                      type => 'text',
                      content => ' instance '
                    },
                    {
                      type => 'url',
                      link => 'class_api',
                      content => 'Api'
                    },
                    {
                      type => 'text',
                      content => ' for chaining '
                    }
                  ]
                }
              ]
            },
            type => 'static',
            const => 'no',
            volatile => 'no',
            parameters => [
              {
                declaration_name => '$code'
              },
              {
                declaration_name => '$message',
                default_value => 'null'
              },
              {
                declaration_name => '$data',
                default_value => 'null'
              },
              {
                declaration_name => '$level',
                default_value => '2'
              }
            ]
          },
          {
            kind => 'function',
            name => 'logError',
            virtualness => 'non_virtual',
            protection => 'public',
            static => 'yes',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'log error message into log backend Could be used as a static or instance method '
                }
              ]
            },
            detailed => {
              doc => [
                params => [
                  {
                    parameters => [
                      {
                        name => '$code'
                      }
                    ],
                    doc => [
                      {
                        type => 'text',
                        content => 'the code coresponding to this log entry '
                      }
                    ]
                  },
                  {
                    parameters => [
                      {
                        name => '$message'
                      }
                    ],
                    doc => [
                      {
                        type => 'parbreak'
                      },
                      {
                        type => 'text',
                        content => 'a message describing the information '
                      }
                    ]
                  },
                  {
                    parameters => [
                      {
                        name => '$data'
                      }
                    ],
                    doc => [
                      {
                        type => 'parbreak'
                      },
                      {
                        type => 'text',
                        content => 'additionnals data recorded to understand the context '
                      }
                    ]
                  },
                  {
                    parameters => [
                      {
                        name => '$level'
                      }
                    ],
                    doc => [
                      {
                        type => 'parbreak'
                      },
                      {
                        type => 'text',
                        content => 'level from 1 (important) to 5 (annecdotic) coresponding to the importance of this event. Is used to filter what to record according to the LOG_VERBOSITY constant '
                      }
                    ]
                  }
                ],
                {
                  return => [
                    {
                      type => 'url',
                      link => 'class_api',
                      content => 'Api'
                    },
                    {
                      type => 'text',
                      content => ' instance '
                    },
                    {
                      type => 'url',
                      link => 'class_api',
                      content => 'Api'
                    },
                    {
                      type => 'text',
                      content => ' for chaining '
                    }
                  ]
                }
              ]
            },
            type => 'static',
            const => 'no',
            volatile => 'no',
            parameters => [
              {
                declaration_name => '$code'
              },
              {
                declaration_name => '$message',
                default_value => 'null'
              },
              {
                declaration_name => '$data',
                default_value => 'null'
              },
              {
                declaration_name => '$level',
                default_value => '1'
              }
            ]
          },
          {
            kind => 'function',
            name => 'logDebug',
            virtualness => 'non_virtual',
            protection => 'public',
            static => 'yes',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'log debug message into log backend Could be used as a static or instance method '
                }
              ]
            },
            detailed => {
              doc => [
                params => [
                  {
                    parameters => [
                      {
                        name => '$code'
                      }
                    ],
                    doc => [
                      {
                        type => 'text',
                        content => 'the code coresponding to this log entry '
                      }
                    ]
                  },
                  {
                    parameters => [
                      {
                        name => '$message'
                      }
                    ],
                    doc => [
                      {
                        type => 'parbreak'
                      },
                      {
                        type => 'text',
                        content => 'a message describing the information '
                      }
                    ]
                  },
                  {
                    parameters => [
                      {
                        name => '$data'
                      }
                    ],
                    doc => [
                      {
                        type => 'parbreak'
                      },
                      {
                        type => 'text',
                        content => 'additionnals data recorded to understand the context '
                      }
                    ]
                  },
                  {
                    parameters => [
                      {
                        name => '$level'
                      }
                    ],
                    doc => [
                      {
                        type => 'parbreak'
                      },
                      {
                        type => 'text',
                        content => 'level from 1 (important) to 5 (annecdotic) coresponding to the importance of this event. Is used to filter what to record according to the LOG_VERBOSITY constant '
                      }
                    ]
                  }
                ],
                {
                  return => [
                    {
                      type => 'url',
                      link => 'class_api',
                      content => 'Api'
                    },
                    {
                      type => 'text',
                      content => ' instance '
                    },
                    {
                      type => 'url',
                      link => 'class_api',
                      content => 'Api'
                    },
                    {
                      type => 'text',
                      content => ' for chaining '
                    }
                  ]
                }
              ]
            },
            type => 'static',
            const => 'no',
            volatile => 'no',
            parameters => [
              {
                declaration_name => '$code'
              },
              {
                declaration_name => '$message',
                default_value => 'null'
              },
              {
                declaration_name => '$data',
                default_value => 'null'
              },
              {
                declaration_name => '$level',
                default_value => '4'
              }
            ]
          }
        ]
      },
      public_static_members => {
        members => [
          {
            kind => 'variable',
            name => '$nosqlApiBackend',
            virtualness => 'non_virtual',
            protection => 'public',
            static => 'yes',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'JSON string with various parameters used for basic functionning of the '
                },
                {
                  type => 'url',
                  link => 'class_api',
                  content => 'Api'
                },
                {
                  type => 'text',
                  content => ' Object. '
                }
              ]
            },
            detailed => {
              doc => [
                {
                  type => 'text',
                  content => 'This property is critical and should contain: '
                },
                {
                  type => 'list',
                  style => 'itemized',
                  content => [
                    [
                      {
                        type => 'text',
                        content => 'connection: mongodb connection string used for connecting to the nosql backend '
                      }
                    ],
                    [
                      {
                        type => 'text',
                        content => 'base: name of the nosql database to use for retriving '
                      },
                      {
                        type => 'url',
                        link => 'class_api',
                        content => 'Api'
                      },
                      {
                        type => 'text',
                        content => ' core elements (api documents, logs, app, session, models and resources) '
                      }
                    ],
                    [
                      {
                        type => 'text',
                        content => 'api_collection : name of the nosql collection used to store API config document '
                      }
                    ]
                  ]
                },
                {
                  type => 'text',
                  content => 'This property has to be overwritten before any call to '
                },
                {
                  type => 'url',
                  link => 'class_api_1a55a9c243939deff97cd5d3ebb532c52f',
                  content => 'Api::getInstance()'
                },
                {
                  type => 'text',
                  content => ' or related method who will construct the '
                },
                {
                  type => 'url',
                  link => 'class_api',
                  content => 'Api'
                },
                {
                  type => 'text',
                  content => ' singleton. To overwrite this property, simply redefine '
                },
                {
                  type => 'url',
                  link => 'class_api_1a0b106058d3efedb85238ed55833d5398',
                  content => 'Api::$nosqlApiBackend'
                },
                {
                  type => 'text',
                  content => ' = \'{ ... }\'; '
                }
              ]
            },
            type => 'static',
            initializer => '= \'{
        "connection" : "mongodb://username:password@127.0.0.1:27017",
        "base" : "basename",
        "api_collection" : "collectionname"
    }\''
          }
        ]
      },
      private_methods => {
        members => [
          {
            kind => 'function',
            name => 'loadApi',
            virtualness => 'non_virtual',
            protection => 'private',
            static => 'no',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'Load the '
                },
                {
                  type => 'url',
                  link => 'class_api',
                  content => 'Api'
                },
                {
                  type => 'text',
                  content => ' configuration If \'api\' param is received form the client, it will set it as the $defaultApiID. '
                }
              ]
            },
            detailed => {
              doc => [
                {
                  type => 'text',
                  content => 'Then it will retreive the API config document from the nosql backend and store it configuration into this '
                },
                {
                  type => 'url',
                  link => 'class_api',
                  content => 'Api'
                },
                {
                  type => 'text',
                  content => ' instance If an exception is catched, call the exitOnError method for exiting program. '
                },
                {
                  return => [
                    {
                      type => 'url',
                      link => 'class_api',
                      content => 'Api'
                    },
                    {
                      type => 'text',
                      content => ' instance '
                    },
                    {
                      type => 'url',
                      link => 'class_api',
                      content => 'Api'
                    },
                    {
                      type => 'text',
                      content => ' for chaining '
                    }
                  ]
                }
              ]
            },
            const => 'no',
            volatile => 'no',
            parameters => [
            ]
          },
          {
            kind => 'function',
            name => 'loadPlugins',
            virtualness => 'non_virtual',
            protection => 'private',
            static => 'no',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'Load the plugins sections required by the '
                },
                {
                  type => 'url',
                  link => 'class_api',
                  content => 'Api'
                },
                {
                  type => 'text',
                  content => ' config document This method will loop throught the plugin section of the '
                },
                {
                  type => 'url',
                  link => 'class_api',
                  content => 'Api'
                },
                {
                  type => 'text',
                  content => ' config document and try to load all the required plugins connectors. '
                }
              ]
            },
            detailed => {
              doc => [
                {
                  return => [
                    {
                      type => 'url',
                      link => 'class_api',
                      content => 'Api'
                    },
                    {
                      type => 'text',
                      content => ' instance '
                    },
                    {
                      type => 'url',
                      link => 'class_api',
                      content => 'Api'
                    },
                    {
                      type => 'text',
                      content => ' for chaining '
                    }
                  ]
                },
                exceptions => [
                  {
                    parameters => [
                      {
                        name => 'ApiException'
                      }
                    ],
                    doc => [
                      {
                        type => 'text',
                        content => 'if an error occur when instanciating plugin connector '
                      }
                    ]
                  }
                ]
              ]
            },
            const => 'no',
            volatile => 'no',
            parameters => [
            ]
          },
          {
            kind => 'function',
            name => 'loadInputFactory',
            virtualness => 'non_virtual',
            protection => 'private',
            static => 'no',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'Load the inputs sections required by the '
                },
                {
                  type => 'url',
                  link => 'class_api',
                  content => 'Api'
                },
                {
                  type => 'text',
                  content => ' config document This method will loop throught the input section of the '
                },
                {
                  type => 'url',
                  link => 'class_api',
                  content => 'Api'
                },
                {
                  type => 'text',
                  content => ' config document and try to load all the required inputs connectors. '
                }
              ]
            },
            detailed => {
              doc => [
                {
                  return => [
                    {
                      type => 'url',
                      link => 'class_api',
                      content => 'Api'
                    },
                    {
                      type => 'text',
                      content => ' instance '
                    },
                    {
                      type => 'url',
                      link => 'class_api',
                      content => 'Api'
                    },
                    {
                      type => 'text',
                      content => ' for chaining '
                    }
                  ]
                },
                exceptions => [
                  {
                    parameters => [
                      {
                        name => 'ApiException'
                      }
                    ],
                    doc => [
                      {
                        type => 'text',
                        content => 'if an error occur when instanciating input connector '
                      }
                    ]
                  }
                ]
              ]
            },
            const => 'no',
            volatile => 'no',
            parameters => [
            ]
          },
          {
            kind => 'function',
            name => 'initInputFactory',
            virtualness => 'non_virtual',
            protection => 'private',
            static => 'no',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'Initiate the loaded inputs sections This method will loop throught the previously loaded input section and try to init each input connector. '
                }
              ]
            },
            detailed => {
              doc => [
                {
                  return => [
                    {
                      type => 'url',
                      link => 'class_api',
                      content => 'Api'
                    },
                    {
                      type => 'text',
                      content => ' instance '
                    },
                    {
                      type => 'url',
                      link => 'class_api',
                      content => 'Api'
                    },
                    {
                      type => 'text',
                      content => ' for chaining '
                    }
                  ]
                },
                exceptions => [
                  {
                    parameters => [
                      {
                        name => 'ApiException'
                      }
                    ],
                    doc => [
                      {
                        type => 'text',
                        content => 'if an error occur when initializing input connector '
                      }
                    ]
                  }
                ]
              ]
            },
            const => 'no',
            volatile => 'no',
            parameters => [
            ]
          },
          {
            kind => 'function',
            name => 'loadOutputFactory',
            virtualness => 'non_virtual',
            protection => 'private',
            static => 'no',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'Load the outputs sections required by the '
                },
                {
                  type => 'url',
                  link => 'class_api',
                  content => 'Api'
                },
                {
                  type => 'text',
                  content => ' config document This method will loop throught the output section of the '
                },
                {
                  type => 'url',
                  link => 'class_api',
                  content => 'Api'
                },
                {
                  type => 'text',
                  content => ' config document and try to load all the required outputs connectors. '
                }
              ]
            },
            detailed => {
              doc => [
                {
                  return => [
                    {
                      type => 'url',
                      link => 'class_api',
                      content => 'Api'
                    },
                    {
                      type => 'text',
                      content => ' instance '
                    },
                    {
                      type => 'url',
                      link => 'class_api',
                      content => 'Api'
                    },
                    {
                      type => 'text',
                      content => ' for chaining '
                    }
                  ]
                },
                exceptions => [
                  {
                    parameters => [
                      {
                        name => 'ApiException'
                      }
                    ],
                    doc => [
                      {
                        type => 'text',
                        content => 'if an error occur when instanciating output connector '
                      }
                    ]
                  }
                ]
              ]
            },
            const => 'no',
            volatile => 'no',
            parameters => [
            ]
          },
          {
            kind => 'function',
            name => 'initOutputFactory',
            virtualness => 'non_virtual',
            protection => 'private',
            static => 'no',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'Initiate the loaded outputs sections This method will loop throught the previously loaded output section and try to init each output connector. '
                }
              ]
            },
            detailed => {
              doc => [
                {
                  return => [
                    {
                      type => 'url',
                      link => 'class_api',
                      content => 'Api'
                    },
                    {
                      type => 'text',
                      content => ' instance '
                    },
                    {
                      type => 'url',
                      link => 'class_api',
                      content => 'Api'
                    },
                    {
                      type => 'text',
                      content => ' for chaining '
                    }
                  ]
                },
                exceptions => [
                  {
                    parameters => [
                      {
                        name => 'ApiException'
                      }
                    ],
                    doc => [
                      {
                        type => 'text',
                        content => 'if an error occur when initializing output connector '
                      }
                    ]
                  }
                ]
              ]
            },
            const => 'no',
            volatile => 'no',
            parameters => [
            ]
          },
          {
            kind => 'function',
            name => 'loadStoreFactory',
            virtualness => 'non_virtual',
            protection => 'private',
            static => 'no',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'Load the stores sections required by the '
                },
                {
                  type => 'url',
                  link => 'class_api',
                  content => 'Api'
                },
                {
                  type => 'text',
                  content => ' config document This method will loop throught the store section of the '
                },
                {
                  type => 'url',
                  link => 'class_api',
                  content => 'Api'
                },
                {
                  type => 'text',
                  content => ' config document and try to load all the required stores connectors. '
                }
              ]
            },
            detailed => {
              doc => [
                {
                  return => [
                    {
                      type => 'url',
                      link => 'class_api',
                      content => 'Api'
                    },
                    {
                      type => 'text',
                      content => ' instance '
                    },
                    {
                      type => 'url',
                      link => 'class_api',
                      content => 'Api'
                    },
                    {
                      type => 'text',
                      content => ' for chaining '
                    }
                  ]
                },
                exceptions => [
                  {
                    parameters => [
                      {
                        name => 'ApiException'
                      }
                    ],
                    doc => [
                      {
                        type => 'text',
                        content => 'if an error occur when instanciating store connector '
                      }
                    ]
                  }
                ]
              ]
            },
            const => 'no',
            volatile => 'no',
            parameters => [
            ]
          },
          {
            kind => 'function',
            name => 'initStoreFactory',
            virtualness => 'non_virtual',
            protection => 'private',
            static => 'no',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'Initiate the loaded stores sections This method will loop throught the previously loaded store section and try to init each store connector. '
                }
              ]
            },
            detailed => {
              doc => [
                {
                  return => [
                    {
                      type => 'url',
                      link => 'class_api',
                      content => 'Api'
                    },
                    {
                      type => 'text',
                      content => ' instance '
                    },
                    {
                      type => 'url',
                      link => 'class_api',
                      content => 'Api'
                    },
                    {
                      type => 'text',
                      content => ' for chaining '
                    }
                  ]
                },
                exceptions => [
                  {
                    parameters => [
                      {
                        name => 'ApiException'
                      }
                    ],
                    doc => [
                      {
                        type => 'text',
                        content => 'if an error occur when initializing store connector '
                      }
                    ]
                  }
                ]
              ]
            },
            const => 'no',
            volatile => 'no',
            parameters => [
            ]
          },
          {
            kind => 'function',
            name => 'executeExtractAclUser',
            virtualness => 'non_virtual',
            protection => 'private',
            static => 'no',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'Extract form the resource config the acl user autorized. '
                }
              ]
            },
            detailed => {
              doc => [
                params => [
                  {
                    parameters => [
                      {
                        name => '$aclRules'
                      }
                    ],
                    doc => [
                      {
                        type => 'text',
                        content => 'the '
                      }
                    ]
                  }
                ],
                {
                  return => [
                    {
                      type => 'text',
                      content => 'array/string with autorized users '
                    }
                  ]
                },
                exceptions => [
                  {
                    parameters => [
                      {
                        name => 'ApiException'
                      }
                    ],
                    doc => [
                      {
                        type => 'text',
                        content => 'If resource is not acessible or controlled by ACL rules. '
                      }
                    ]
                  }
                ]
              ]
            },
            const => 'no',
            volatile => 'no',
            parameters => [
              {
                declaration_name => '$aclRules'
              }
            ]
          },
          {
            kind => 'function',
            name => 'executeExtractAclApplication',
            virtualness => 'non_virtual',
            protection => 'private',
            static => 'no',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'Extract form the resource config the acl user autorized. '
                }
              ]
            },
            detailed => {
              doc => [
                params => [
                  {
                    parameters => [
                      {
                        name => '$aclRules'
                      }
                    ],
                    doc => [
                      {
                        type => 'text',
                        content => 'the '
                      }
                    ]
                  }
                ],
                {
                  return => [
                    {
                      type => 'text',
                      content => 'array/string with autorized users '
                    }
                  ]
                },
                exceptions => [
                  {
                    parameters => [
                      {
                        name => 'ApiException'
                      }
                    ],
                    doc => [
                      {
                        type => 'text',
                        content => 'If resource is not acessible or controlled by ACL rules. '
                      }
                    ]
                  }
                ]
              ]
            },
            const => 'no',
            volatile => 'no',
            parameters => [
              {
                declaration_name => '$aclRules'
              }
            ]
          },
          {
            kind => 'function',
            name => 'getResourceConfig',
            virtualness => 'non_virtual',
            protection => 'private',
            static => 'no',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'Scan the API resource tree according to the requested path and search for the requested resource. '
                }
              ]
            },
            detailed => {
              doc => [
                {
                  type => 'text',
                  content => 'This method also merge every resource config node matching this path for return a config array with all ascendant param merged with into the requested one '
                },
                {
                  return => [
                    {
                      type => 'text',
                      content => 'array containing the resource config to execute '
                    }
                  ]
                },
                exceptions => [
                  {
                    parameters => [
                      {
                        name => 'ApiException'
                      }
                    ],
                    doc => [
                      {
                        type => 'text',
                        content => 'If tree node is malformed (missing \'resource\' key) or if path and tree are not given. '
                      }
                    ]
                  }
                ]
              ]
            },
            const => 'no',
            volatile => 'no',
            parameters => [
              {
                declaration_name => '$elements'
              },
              {
                declaration_name => '$configtree'
              },
              {
                declaration_name => '$outputConfig',
                default_value => 'array()'
              }
            ]
          },
          {
            kind => 'function',
            name => 'log',
            virtualness => 'non_virtual',
            protection => 'private',
            static => 'no',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'Execute recording of every log message into log backend. '
                }
              ]
            },
            detailed => {
              doc => [
                params => [
                  {
                    parameters => [
                      {
                        name => '$type'
                      }
                    ],
                    doc => [
                      {
                        type => 'text',
                        content => 'the type of log entry (generaly should be \'error\', \'warn\', \'info\' or \'debug\') '
                      }
                    ]
                  },
                  {
                    parameters => [
                      {
                        name => '$code'
                      }
                    ],
                    doc => [
                      {
                        type => 'parbreak'
                      },
                      {
                        type => 'text',
                        content => 'the code coresponding to this log entry '
                      }
                    ]
                  },
                  {
                    parameters => [
                      {
                        name => '$message'
                      }
                    ],
                    doc => [
                      {
                        type => 'parbreak'
                      },
                      {
                        type => 'text',
                        content => 'a message describing the information '
                      }
                    ]
                  },
                  {
                    parameters => [
                      {
                        name => '$data'
                      }
                    ],
                    doc => [
                      {
                        type => 'parbreak'
                      },
                      {
                        type => 'text',
                        content => 'additionnals data recorded to understand the context '
                      }
                    ]
                  },
                  {
                    parameters => [
                      {
                        name => '$level'
                      }
                    ],
                    doc => [
                      {
                        type => 'parbreak'
                      },
                      {
                        type => 'text',
                        content => 'level from 1 (important) to 5 (annecdotic) coresponding to the importance of this event. Is used to filter what to record according to the LOG_VERBOSITY constant '
                      }
                    ]
                  }
                ],
                {
                  return => [
                    {
                      type => 'url',
                      link => 'class_api',
                      content => 'Api'
                    },
                    {
                      type => 'text',
                      content => ' instance '
                    },
                    {
                      type => 'url',
                      link => 'class_api',
                      content => 'Api'
                    },
                    {
                      type => 'text',
                      content => ' for chaining '
                    }
                  ]
                }
              ]
            },
            const => 'no',
            volatile => 'no',
            parameters => [
              {
                declaration_name => '$type',
                default_value => '\'error\''
              },
              {
                declaration_name => '$code',
                default_value => '0'
              },
              {
                declaration_name => '$message',
                default_value => 'null'
              },
              {
                declaration_name => '$data',
                default_value => 'array()'
              },
              {
                declaration_name => '$level',
                default_value => '1'
              }
            ]
          }
        ]
      },
      private_members => {
        members => [
          {
            kind => 'variable',
            name => '$inputs',
            virtualness => 'non_virtual',
            protection => 'private',
            static => 'no',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'list of all input connector. '
                }
              ]
            },
            detailed => {
              doc => [
                {
                  type => 'text',
                  content => 'This property is populated when loading input connector as described in the '
                },
                {
                  type => 'url',
                  link => 'class_api',
                  content => 'Api'
                },
                {
                  type => 'text',
                  content => ' Config document. \'id\' config key is used as key identifier '
                }
              ]
            },
            initializer => '= array()'
          },
          {
            kind => 'variable',
            name => '$inputDefault',
            virtualness => 'non_virtual',
            protection => 'private',
            static => 'no',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'ID of the default input connector to use. '
                }
              ]
            },
            detailed => {
              doc => [
                {
                  type => 'text',
                  content => 'This property is automaticaly set when the "default" property is set to \'true\' into one of the input section of the '
                },
                {
                  type => 'url',
                  link => 'class_api',
                  content => 'Api'
                },
                {
                  type => 'text',
                  content => ' Config document '
                }
              ]
            },
            initializer => '= \'\''
          },
          {
            kind => 'variable',
            name => '$outputs',
            virtualness => 'non_virtual',
            protection => 'private',
            static => 'no',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'list of all output connector. '
                }
              ]
            },
            detailed => {
              doc => [
                {
                  type => 'text',
                  content => 'This property is populated when loading output connector as described in the '
                },
                {
                  type => 'url',
                  link => 'class_api',
                  content => 'Api'
                },
                {
                  type => 'text',
                  content => ' Config document. \'id\' config key is used as key identifier '
                }
              ]
            },
            initializer => '= array()'
          },
          {
            kind => 'variable',
            name => '$outputDefault',
            virtualness => 'non_virtual',
            protection => 'private',
            static => 'no',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'ID of the default output connector to use. '
                }
              ]
            },
            detailed => {
              doc => [
                {
                  type => 'text',
                  content => 'This property is automaticaly set when the "default" property is set to \'true\' into one of the output section of the '
                },
                {
                  type => 'url',
                  link => 'class_api',
                  content => 'Api'
                },
                {
                  type => 'text',
                  content => ' Config document '
                }
              ]
            },
            initializer => '= \'\''
          },
          {
            kind => 'variable',
            name => '$stores',
            virtualness => 'non_virtual',
            protection => 'private',
            static => 'no',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'the store manager. '
                }
              ]
            },
            detailed => {
              doc => [
                {
                  type => 'text',
                  content => 'This property is used a a cache for all instanciated stores used into the '
                },
                {
                  type => 'url',
                  link => 'class_api',
                  content => 'Api'
                },
                {
                  type => 'text',
                  content => '. Only required store are dynamicaly loaded when needed. \'id\' config key is used as key identifier '
                }
              ]
            },
            initializer => '= array()'
          },
          {
            kind => 'variable',
            name => '$storeDefault',
            virtualness => 'non_virtual',
            protection => 'private',
            static => 'no',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'ID of the default store connector to use. '
                }
              ]
            },
            detailed => {
              doc => [
                {
                  type => 'text',
                  content => 'This property is automaticaly set when the "default" property is set to \'true\' into one of the store section of the '
                },
                {
                  type => 'url',
                  link => 'class_api',
                  content => 'Api'
                },
                {
                  type => 'text',
                  content => ' Config document '
                }
              ]
            },
            initializer => '= \'\''
          },
          {
            kind => 'variable',
            name => '$models',
            virtualness => 'non_virtual',
            protection => 'private',
            static => 'no',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'the model manager. '
                }
              ]
            },
            detailed => {
              doc => [
                {
                  type => 'text',
                  content => 'This property is used a a cache for all instanciated models used into the '
                },
                {
                  type => 'url',
                  link => 'class_api',
                  content => 'Api'
                },
                {
                  type => 'text',
                  content => '. Only required models are dynamicaly loaded when needed. \'id\' config key is used as key identifier '
                }
              ]
            },
            initializer => '= array()'
          },
          {
            kind => 'variable',
            name => '$resources',
            virtualness => 'non_virtual',
            protection => 'private',
            static => 'no',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'the resource manager. '
                }
              ]
            },
            detailed => {
              doc => [
                {
                  type => 'text',
                  content => 'This property is used a a cache for all instanciated resources used into the '
                },
                {
                  type => 'url',
                  link => 'class_api',
                  content => 'Api'
                },
                {
                  type => 'text',
                  content => '. Only required resources are dynamicaly loaded when needed. \'id\' config key is used as key identifier '
                }
              ]
            },
            initializer => '= array()'
          }
        ]
      },
      private_static_members => {
        members => [
          {
            kind => 'variable',
            name => '$_instance',
            virtualness => 'non_virtual',
            protection => 'private',
            static => 'yes',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'static property to store unique instance of this singleton class '
                }
              ]
            },
            detailed => {},
            type => 'static',
            initializer => '= null'
          }
        ]
      },
      brief => {
        doc => [
          {
            type => 'url',
            link => 'namespace_s_x_a_p_i',
            content => 'SXAPI'
          },
          {
            type => 'text',
            content => ' Main class. '
          }
        ]
      },
      detailed => {
        doc => [
          {
            type => 'text',
            content => 'This Class is a singleton and the main entry class for all the '
          },
          {
            type => 'url',
            link => 'namespace_s_x_a_p_i',
            content => 'SXAPI'
          },
          {
            type => 'text',
            content => ' process. Developper who want to create a new '
          },
          {
            type => 'url',
            link => 'namespace_s_x_a_p_i',
            content => 'SXAPI'
          },
          {
            type => 'text',
            content => ' instance server should instanciate it as follow'
          },
          {
            type => 'parbreak'
          },
          {
            type => 'text',
            content => 'Example: '
          },
          {
            type => 'parbreak'
          },
          {
            author => [
              {
                type => 'text',
                content => 'Dev Team '
              },
              {
                type => 'url',
                content => 'dev@startx.fr'
              },
              {
                type => 'text',
                content => ' '
              }
            ]
          },
          {
            see => [
              {
                type => 'url',
                link => 'class_configurable',
                content => 'Configurable'
              },
              {
                type => 'text',
                content => ' '
              },
              {
                type => 'link',
                {
                  type => 'text',
                  content => 'Copyright (c) 2003-2013 startx.fr  '
                },
                {
                  type => 'url',
                  content => 'https://github.com/startxfr/sxapi/blob/master/licence.txt'
                },
                {
                  type => 'text',
                  content => ' '
                }
              }
            ]
          }
        ]
      }
    },
    {
      name => 'Configurable',
      derived => [
        {
          name => 'Api',
          virtualness => 'non_virtual',
          protection => 'public'
        }
      ],
      all_members => [
        {
          name => '$config',
          virtualness => 'non_virtual',
          protection => 'private',
          scope => 'Configurable'
        },
        {
          name => '__construct',
          virtualness => 'non_virtual',
          protection => 'public',
          scope => 'Configurable'
        },
        {
          name => 'getConfig',
          virtualness => 'non_virtual',
          protection => 'public',
          scope => 'Configurable'
        },
        {
          name => 'getConfigs',
          virtualness => 'non_virtual',
          protection => 'public',
          scope => 'Configurable'
        },
        {
          name => 'isConfig',
          virtualness => 'non_virtual',
          protection => 'public',
          scope => 'Configurable'
        },
        {
          name => 'serialize',
          virtualness => 'non_virtual',
          protection => 'public',
          scope => 'Configurable'
        },
        {
          name => 'setConfig',
          virtualness => 'non_virtual',
          protection => 'public',
          scope => 'Configurable'
        },
        {
          name => 'setConfigs',
          virtualness => 'non_virtual',
          protection => 'public',
          scope => 'Configurable'
        }
      ],
      public_methods => {
        members => [
          {
            kind => 'function',
            name => '__construct',
            virtualness => 'non_virtual',
            protection => 'public',
            static => 'no',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'Create a new object representing the request. '
                }
              ]
            },
            detailed => {},
            const => 'no',
            volatile => 'no',
            parameters => [
              {
                declaration_name => '$config',
                default_value => 'null'
              }
            ]
          },
          {
            kind => 'function',
            name => 'getConfig',
            virtualness => 'non_virtual',
            protection => 'public',
            static => 'no',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'return the full config array or only part of it '
                }
              ]
            },
            detailed => {
              doc => [
                {
                  return => [
                    {
                      type => 'text',
                      content => 'array full config or fragment '
                    }
                  ]
                }
              ]
            },
            const => 'no',
            volatile => 'no',
            parameters => [
              {
                declaration_name => '$key',
                default_value => 'null'
              },
              {
                declaration_name => '$default',
                default_value => 'null'
              }
            ]
          },
          {
            kind => 'function',
            name => 'isConfig',
            virtualness => 'non_virtual',
            protection => 'public',
            static => 'no',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'return the full config array or only part of it '
                }
              ]
            },
            detailed => {
              doc => [
                {
                  return => [
                    {
                      type => 'text',
                      content => 'array full config or fragment '
                    }
                  ]
                }
              ]
            },
            const => 'no',
            volatile => 'no',
            parameters => [
              {
                declaration_name => '$key',
                default_value => 'null'
              }
            ]
          },
          {
            kind => 'function',
            name => 'getConfigs',
            virtualness => 'non_virtual',
            protection => 'public',
            static => 'no',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'return the full config array or only part of it '
                }
              ]
            },
            detailed => {
              doc => [
                {
                  return => [
                    {
                      type => 'text',
                      content => 'array full config or fragment '
                    }
                  ]
                }
              ]
            },
            const => 'no',
            volatile => 'no',
            parameters => [
            ]
          },
          {
            kind => 'function',
            name => 'setConfig',
            virtualness => 'non_virtual',
            protection => 'public',
            static => 'no',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'insert or update config elements in config '
                }
              ]
            },
            detailed => {
              doc => [
                {
                  return => [
                    {
                      type => 'text',
                      content => 'object itself return $this to chain methods '
                    }
                  ]
                }
              ]
            },
            const => 'no',
            volatile => 'no',
            parameters => [
              {
                declaration_name => '$key'
              },
              {
                declaration_name => '$value',
                default_value => 'null'
              }
            ]
          },
          {
            kind => 'function',
            name => 'setConfigs',
            virtualness => 'non_virtual',
            protection => 'public',
            static => 'no',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'insert or update config elements in config '
                }
              ]
            },
            detailed => {
              doc => [
                {
                  return => [
                    {
                      type => 'text',
                      content => 'object itself return $this to chain methods '
                    }
                  ]
                }
              ]
            },
            const => 'no',
            volatile => 'no',
            parameters => [
              {
                declaration_name => '$config',
                default_value => 'null'
              },
              {
                declaration_name => '$convert',
                default_value => 'false'
              }
            ]
          },
          {
            kind => 'function',
            name => 'serialize',
            virtualness => 'non_virtual',
            protection => 'public',
            static => 'no',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'render a json string representing the configuration '
                }
              ]
            },
            detailed => {
              doc => [
                {
                  return => [
                    {
                      type => 'text',
                      content => 'string json formated string with the config data '
                    }
                  ]
                }
              ]
            },
            const => 'no',
            volatile => 'no',
            parameters => [
            ]
          }
        ]
      },
      private_members => {
        members => [
          {
            kind => 'variable',
            name => '$config',
            virtualness => 'non_virtual',
            protection => 'private',
            static => 'no',
            brief => {},
            detailed => {}
          }
        ]
      },
      brief => {
        doc => [
          {
            type => 'url',
            link => 'class_configurable',
            content => 'Configurable'
          },
          {
            type => 'text',
            content => ' objects. '
          }
        ]
      },
      detailed => {
        doc => [
          {
            type => 'text',
            content => 'This class could be used every time you need to handle configuration data for a given object.'
          },
          {
            type => 'parbreak'
          },
          {
            type => 'text',
            content => 'Example: '
          },
          {
            type => 'style',
            style => 'code',
            enable => 'yes'
          },
          {
            type => 'text',
            content => ' $config = new '
          },
          {
            type => 'url',
            link => 'class_configurable',
            content => 'Configurable'
          },
          {
            type => 'text',
            content => '(array(\'key\'=>\'value\')); echo $config->getConfig(\'key\'); // return \'value\' '
          },
          {
            type => 'style',
            style => 'code',
            enable => 'no'
          },
          {
            type => 'text',
            content => ' '
          }
        ]
      }
    },
    {
      name => 'Event',
      all_members => [
        {
          name => '$events',
          virtualness => 'non_virtual',
          protection => 'public',
          scope => 'Event'
        },
        {
          name => 'bind',
          virtualness => 'non_virtual',
          protection => 'public',
          scope => 'Event'
        },
        {
          name => 'trigger',
          virtualness => 'non_virtual',
          protection => 'public',
          scope => 'Event'
        }
      ],
      public_static_methods => {
        members => [
          {
            kind => 'function',
            name => 'trigger',
            virtualness => 'non_virtual',
            protection => 'public',
            static => 'yes',
            brief => {},
            detailed => {},
            type => 'static',
            const => 'no',
            volatile => 'no',
            parameters => [
              {
                declaration_name => '$event'
              },
              {
                declaration_name => '$args',
                default_value => 'array()'
              }
            ]
          },
          {
            kind => 'function',
            name => 'bind',
            virtualness => 'non_virtual',
            protection => 'public',
            static => 'yes',
            brief => {},
            detailed => {},
            type => 'static',
            const => 'no',
            volatile => 'no',
            parameters => [
              {
                declaration_name => '$event'
              },
              {
                declaration_name => '$func'
              }
            ]
          }
        ]
      },
      public_static_members => {
        members => [
          {
            kind => 'variable',
            name => '$events',
            virtualness => 'non_virtual',
            protection => 'public',
            static => 'yes',
            brief => {},
            detailed => {},
            type => 'static',
            initializer => '= array()'
          }
        ]
      },
      brief => {
        doc => [
          {
            type => 'text',
            content => 'Simple class for handling event in php. '
          }
        ]
      },
      detailed => {
        doc => [
          {
            type => 'text',
            content => 'ex: '
          },
          {
            type => 'url',
            link => 'class_event_1a9f0b53b3fb1bf0d3566f8f019d74959a',
            content => 'Event::bind'
          },
          {
            type => 'text',
            content => '(\'input.preprocess\', function($args = array()) { ... });'
          },
          {
            type => 'linebreak'
          },
          {
            type => 'text',
            content => 'ex: '
          },
          {
            type => 'url',
            link => 'class_event_1ac5f5dda55315af16cd6ff1c9a664fd59',
            content => 'Event::trigger'
          },
          {
            type => 'text',
            content => '(\'input.preprocess\', $data);'
          },
          {
            type => 'parbreak'
          },
          {
            author => [
              {
                type => 'url',
                content => 'dev@startx.fr'
              },
              {
                type => 'text',
                content => ' '
              }
            ]
          }
        ]
      }
    },
    {
      name => 'IInput',
      all_members => [
        {
          name => '__construct',
          virtualness => 'non_virtual',
          protection => 'public',
          scope => 'IInput'
        },
        {
          name => 'get',
          virtualness => 'non_virtual',
          protection => 'public',
          scope => 'IInput'
        },
        {
          name => 'getAll',
          virtualness => 'non_virtual',
          protection => 'public',
          scope => 'IInput'
        },
        {
          name => 'init',
          virtualness => 'non_virtual',
          protection => 'public',
          scope => 'IInput'
        },
        {
          name => 'set',
          virtualness => 'non_virtual',
          protection => 'public',
          scope => 'IInput'
        },
        {
          name => 'setAll',
          virtualness => 'non_virtual',
          protection => 'public',
          scope => 'IInput'
        }
      ],
      public_methods => {
        members => [
          {
            kind => 'function',
            name => '__construct',
            virtualness => 'non_virtual',
            protection => 'public',
            static => 'no',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'Check and control the given configuration. '
                }
              ]
            },
            detailed => {
              doc => [
                {
                  type => 'text',
                  content => 'Called by the constructor '
                }
              ]
            },
            const => 'no',
            volatile => 'no',
            parameters => [
              {
                declaration_name => '$config'
              }
            ]
          },
          {
            kind => 'function',
            name => 'init',
            virtualness => 'non_virtual',
            protection => 'public',
            static => 'no',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'Initialize content into input description. '
                }
              ]
            },
            detailed => {},
            const => 'no',
            volatile => 'no',
            parameters => [
            ]
          },
          {
            kind => 'function',
            name => 'get',
            virtualness => 'non_virtual',
            protection => 'public',
            static => 'no',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'get a key param '
                }
              ]
            },
            detailed => {},
            const => 'no',
            volatile => 'no',
            parameters => [
              {
                declaration_name => '$key'
              },
              {
                declaration_name => '$default',
                default_value => 'null'
              }
            ]
          },
          {
            kind => 'function',
            name => 'set',
            virtualness => 'non_virtual',
            protection => 'public',
            static => 'no',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'set a key param '
                }
              ]
            },
            detailed => {},
            const => 'no',
            volatile => 'no',
            parameters => [
              {
                declaration_name => '$key'
              },
              {
                declaration_name => '$array'
              }
            ]
          },
          {
            kind => 'function',
            name => 'getAll',
            virtualness => 'non_virtual',
            protection => 'public',
            static => 'no',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'get all data (raw) '
                }
              ]
            },
            detailed => {},
            const => 'no',
            volatile => 'no',
            parameters => [
            ]
          },
          {
            kind => 'function',
            name => 'setAll',
            virtualness => 'non_virtual',
            protection => 'public',
            static => 'no',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'Set all data (raw) '
                }
              ]
            },
            detailed => {},
            const => 'no',
            volatile => 'no',
            parameters => [
              {
                declaration_name => '$array'
              }
            ]
          }
        ]
      },
      brief => {},
      detailed => {}
    },
    {
      name => 'IModel',
      all_members => [
        {
          name => '__construct',
          virtualness => 'non_virtual',
          protection => 'public',
          scope => 'IModel'
        },
        {
          name => 'bindVars',
          virtualness => 'non_virtual',
          protection => 'public',
          scope => 'IModel'
        },
        {
          name => 'create',
          virtualness => 'non_virtual',
          protection => 'public',
          scope => 'IModel'
        },
        {
          name => 'delete',
          virtualness => 'non_virtual',
          protection => 'public',
          scope => 'IModel'
        },
        {
          name => 'getStore',
          virtualness => 'non_virtual',
          protection => 'public',
          scope => 'IModel'
        },
        {
          name => 'read',
          virtualness => 'non_virtual',
          protection => 'public',
          scope => 'IModel'
        },
        {
          name => 'readCount',
          virtualness => 'non_virtual',
          protection => 'public',
          scope => 'IModel'
        },
        {
          name => 'readDetail',
          virtualness => 'non_virtual',
          protection => 'public',
          scope => 'IModel'
        },
        {
          name => 'readOne',
          virtualness => 'non_virtual',
          protection => 'public',
          scope => 'IModel'
        },
        {
          name => 'update',
          virtualness => 'non_virtual',
          protection => 'public',
          scope => 'IModel'
        }
      ],
      public_methods => {
        members => [
          {
            kind => 'function',
            name => '__construct',
            virtualness => 'non_virtual',
            protection => 'public',
            static => 'no',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'Constructor of a model need a storage to access data. '
                }
              ]
            },
            detailed => {},
            const => 'no',
            volatile => 'no',
            parameters => [
              {
                declaration_name => '$storage'
              }
            ]
          },
          {
            kind => 'function',
            name => 'getStore',
            virtualness => 'non_virtual',
            protection => 'public',
            static => 'no',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'Access the storage object. '
                }
              ]
            },
            detailed => {},
            const => 'no',
            volatile => 'no',
            parameters => [
            ]
          },
          {
            kind => 'function',
            name => 'bindVars',
            virtualness => 'non_virtual',
            protection => 'public',
            static => 'no',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'Control given list of data and filter out only data described by this model. '
                }
              ]
            },
            detailed => {},
            const => 'no',
            volatile => 'no',
            parameters => [
              {
                declaration_name => '$vars'
              }
            ]
          },
          {
            kind => 'function',
            name => 'readCount',
            virtualness => 'non_virtual',
            protection => 'public',
            static => 'no',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'Execute a search action and get the total result, excluding pagging. '
                }
              ]
            },
            detailed => {},
            const => 'no',
            volatile => 'no',
            parameters => [
              {
                declaration_name => '$criteria',
                default_value => 'array()'
              }
            ]
          },
          {
            kind => 'function',
            name => 'readOne',
            virtualness => 'non_virtual',
            protection => 'public',
            static => 'no',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'Execute a search action on this model and retrive unique row by ID. '
                }
              ]
            },
            detailed => {},
            const => 'no',
            volatile => 'no',
            parameters => [
              {
                declaration_name => '$id'
              }
            ]
          },
          {
            kind => 'function',
            name => 'read',
            virtualness => 'non_virtual',
            protection => 'public',
            static => 'no',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'Execute a search action on this model (SELECT on SQL) and return resultSet. '
                }
              ]
            },
            detailed => {},
            const => 'no',
            volatile => 'no',
            parameters => [
              {
                declaration_name => '$criteria',
                default_value => 'array()'
              },
              {
                declaration_name => '$order',
                default_value => 'array()'
              },
              {
                declaration_name => '$start',
                default_value => '0'
              },
              {
                declaration_name => '$stop',
                default_value => '30'
              }
            ]
          },
          {
            kind => 'function',
            name => 'readDetail',
            virtualness => 'non_virtual',
            protection => 'public',
            static => 'no',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'Execute a search action on this model (SELECT on SQL) and return resultSet with detail information (LEFT JOIN) '
                }
              ]
            },
            detailed => {},
            const => 'no',
            volatile => 'no',
            parameters => [
              {
                declaration_name => '$criteria',
                default_value => 'array()'
              },
              {
                declaration_name => '$order',
                default_value => 'array()'
              },
              {
                declaration_name => '$start',
                default_value => '0'
              },
              {
                declaration_name => '$stop',
                default_value => '30'
              }
            ]
          },
          {
            kind => 'function',
            name => 'create',
            virtualness => 'non_virtual',
            protection => 'public',
            static => 'no',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'Execute an insert action on this model (INSERT on SQL) and return a boolean. '
                }
              ]
            },
            detailed => {},
            const => 'no',
            volatile => 'no',
            parameters => [
              {
                declaration_name => '$data'
              }
            ]
          },
          {
            kind => 'function',
            name => 'update',
            virtualness => 'non_virtual',
            protection => 'public',
            static => 'no',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'Execute an update action on this model (UPDATE on SQL) and return a boolean. '
                }
              ]
            },
            detailed => {},
            const => 'no',
            volatile => 'no',
            parameters => [
              {
                declaration_name => '$id'
              },
              {
                declaration_name => '$data'
              }
            ]
          },
          {
            kind => 'function',
            name => 'delete',
            virtualness => 'non_virtual',
            protection => 'public',
            static => 'no',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'Execute a delete action on this model (DELETE on SQL) and return a boolean. '
                }
              ]
            },
            detailed => {},
            const => 'no',
            volatile => 'no',
            parameters => [
              {
                declaration_name => '$id'
              }
            ]
          }
        ]
      },
      brief => {},
      detailed => {}
    },
    {
      name => 'Input',
      all_members => [
      ],
      brief => {
        doc => [
          {
            type => 'text',
            content => 'Interface to define an input object. '
          }
        ]
      },
      detailed => {
        doc => [
          {
            see => [
              {
                type => 'text',
                content => 'DefaultInput, ApplicationInput, CookieInput, GetInput, PostInput, RequestInput, ServerInput, SmartInput, UserInput '
              },
              {
                type => 'link',
                {
                  type => 'text',
                  content => 'https://github.com/startxfr/sxapi/wiki/Inputs'
                }
              }
            ]
          }
        ]
      }
    },
    {
      name => 'IOutput',
      all_members => [
        {
          name => '__construct',
          virtualness => 'non_virtual',
          protection => 'public',
          scope => 'IOutput'
        },
        {
          name => 'init',
          virtualness => 'non_virtual',
          protection => 'public',
          scope => 'IOutput'
        },
        {
          name => 'renderError',
          virtualness => 'non_virtual',
          protection => 'public',
          scope => 'IOutput'
        },
        {
          name => 'renderOk',
          virtualness => 'non_virtual',
          protection => 'public',
          scope => 'IOutput'
        }
      ],
      public_methods => {
        members => [
          {
            kind => 'function',
            name => '__construct',
            virtualness => 'non_virtual',
            protection => 'public',
            static => 'no',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'Check and control the given configuration. '
                }
              ]
            },
            detailed => {
              doc => [
                {
                  type => 'text',
                  content => 'Called by the constructor '
                }
              ]
            },
            const => 'no',
            volatile => 'no',
            parameters => [
              {
                declaration_name => '$config'
              }
            ]
          },
          {
            kind => 'function',
            name => 'init',
            virtualness => 'non_virtual',
            protection => 'public',
            static => 'no',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'Initialize content into input description. '
                }
              ]
            },
            detailed => {},
            const => 'no',
            volatile => 'no',
            parameters => [
            ]
          },
          {
            kind => 'function',
            name => 'renderOk',
            virtualness => 'non_virtual',
            protection => 'public',
            static => 'no',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'Render the content exiting normally. '
                }
              ]
            },
            detailed => {
              doc => [
                params => [
                  {
                    parameters => [
                      {
                        name => '$content'
                      }
                    ],
                    doc => [
                      {
                        type => 'text',
                        content => 'data to be rendered'
                      }
                    ]
                  }
                ],
                {
                  return => [
                    {
                      type => 'text',
                      content => 'bool '
                    }
                  ]
                }
              ]
            },
            const => 'no',
            volatile => 'no',
            parameters => [
              {
                declaration_name => '$message'
              },
              {
                declaration_name => '$data'
              }
            ]
          },
          {
            kind => 'function',
            name => 'renderError',
            virtualness => 'non_virtual',
            protection => 'public',
            static => 'no',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'Render the content exiting with error. '
                }
              ]
            },
            detailed => {
              doc => [
                params => [
                  {
                    parameters => [
                      {
                        name => '$content'
                      }
                    ],
                    doc => [
                      {
                        type => 'text',
                        content => 'data to be rendered'
                      }
                    ]
                  }
                ],
                {
                  return => [
                    {
                      type => 'text',
                      content => 'bool '
                    }
                  ]
                }
              ]
            },
            const => 'no',
            volatile => 'no',
            parameters => [
              {
                declaration_name => '$code'
              },
              {
                declaration_name => '$message',
                default_value => '\'\''
              },
              {
                declaration_name => '$other',
                default_value => 'array()'
              }
            ]
          }
        ]
      },
      brief => {},
      detailed => {}
    },
    {
      name => 'IPlugin',
      all_members => [
        {
          name => '__construct',
          virtualness => 'non_virtual',
          protection => 'public',
          scope => 'IPlugin'
        },
        {
          name => 'getInstance',
          virtualness => 'non_virtual',
          protection => 'public',
          scope => 'IPlugin'
        }
      ],
      public_methods => {
        members => [
          {
            kind => 'function',
            name => '__construct',
            virtualness => 'non_virtual',
            protection => 'public',
            static => 'no',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'Check and control the given configuration. '
                }
              ]
            },
            detailed => {
              doc => [
                {
                  type => 'text',
                  content => 'Called by the constructor '
                }
              ]
            },
            const => 'no',
            volatile => 'no',
            parameters => [
              {
                declaration_name => '$config'
              }
            ]
          }
        ]
      },
      public_static_methods => {
        members => [
          {
            kind => 'function',
            name => 'getInstance',
            virtualness => 'non_virtual',
            protection => 'public',
            static => 'yes',
            brief => {
              doc => [
                {
                  type => 'parbreak'
                },
                {
                  type => 'text',
                  content => 'get a singleton instance '
                }
              ]
            },
            detailed => {},
            type => 'static',
            const => 'no',
            volatile => 'no',
            parameters => [
              {
                declaration_name => '$config'
              }
            ]
          }
        ]
      },
      brief => {},
      detailed => {}
    },
    {
      name => 'IResource',
      all_members => [
        {
          name => '__construct',
          virtualness => 'non_virtual',
          protection => 'public',
          scope => 'IResource'
        },
        {
          name => 'createAction',
          virtualness => 'non_virtual',
          protection => 'public',
          scope => 'IResource'
        },
        {
          name => 'deleteAction',
          virtualness => 'non_virtual',
          protection => 'public',
          scope => 'IResource'
        },
        {
          name => 'init',
          virtualness => 'non_virtual',
          protection => 'public',
          scope => 'IResource'
        },
        {
          name => 'optionsAction',
          virtualness => 'non_virtual',
          protection => 'public',
          scope => 'IResource'
        },
        {
          name => 'readAction',
          virtualness => 'non_virtual',
          protection => 'public',
          scope => 'IResource'
        },
        {
          name => 'updateAction',
          virtualness => 'non_virtual',
          protection => 'public',
          scope => 'IResource'
        }
      ],
      public_methods => {
        members => [
          {
            kind => 'function',
            name => '__construct',
            virtualness => 'non_virtual',
            protection => 'public',
            static => 'no',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'Check and control the given configuration. '
                }
              ]
            },
            detailed => {
              doc => [
                {
                  type => 'text',
                  content => 'Called by the constructor '
                }
              ]
            },
            const => 'no',
            volatile => 'no',
            parameters => [
              {
                declaration_name => '$config'
              }
            ]
          },
          {
            kind => 'function',
            name => 'init',
            virtualness => 'non_virtual',
            protection => 'public',
            static => 'no',
            brief => {},
            detailed => {},
            const => 'no',
            volatile => 'no',
            parameters => [
            ]
          },
          {
            kind => 'function',
            name => 'createAction',
            virtualness => 'non_virtual',
            protection => 'public',
            static => 'no',
            brief => {},
            detailed => {},
            const => 'no',
            volatile => 'no',
            parameters => [
            ]
          },
          {
            kind => 'function',
            name => 'readAction',
            virtualness => 'non_virtual',
            protection => 'public',
            static => 'no',
            brief => {},
            detailed => {},
            const => 'no',
            volatile => 'no',
            parameters => [
            ]
          },
          {
            kind => 'function',
            name => 'updateAction',
            virtualness => 'non_virtual',
            protection => 'public',
            static => 'no',
            brief => {},
            detailed => {},
            const => 'no',
            volatile => 'no',
            parameters => [
            ]
          },
          {
            kind => 'function',
            name => 'deleteAction',
            virtualness => 'non_virtual',
            protection => 'public',
            static => 'no',
            brief => {},
            detailed => {},
            const => 'no',
            volatile => 'no',
            parameters => [
            ]
          },
          {
            kind => 'function',
            name => 'optionsAction',
            virtualness => 'non_virtual',
            protection => 'public',
            static => 'no',
            brief => {},
            detailed => {},
            const => 'no',
            volatile => 'no',
            parameters => [
            ]
          }
        ]
      },
      brief => {},
      detailed => {}
    },
    {
      name => 'IStorage',
      all_members => [
        {
          name => '__destruct',
          virtualness => 'non_virtual',
          protection => 'public',
          scope => 'IStorage'
        },
        {
          name => 'connect',
          virtualness => 'non_virtual',
          protection => 'public',
          scope => 'IStorage'
        },
        {
          name => 'create',
          virtualness => 'non_virtual',
          protection => 'public',
          scope => 'IStorage'
        },
        {
          name => 'delete',
          virtualness => 'non_virtual',
          protection => 'public',
          scope => 'IStorage'
        },
        {
          name => 'disconnect',
          virtualness => 'non_virtual',
          protection => 'public',
          scope => 'IStorage'
        },
        {
          name => 'getNativeConnection',
          virtualness => 'non_virtual',
          protection => 'public',
          scope => 'IStorage'
        },
        {
          name => 'init',
          virtualness => 'non_virtual',
          protection => 'public',
          scope => 'IStorage'
        },
        {
          name => 'read',
          virtualness => 'non_virtual',
          protection => 'public',
          scope => 'IStorage'
        },
        {
          name => 'readCount',
          virtualness => 'non_virtual',
          protection => 'public',
          scope => 'IStorage'
        },
        {
          name => 'readOne',
          virtualness => 'non_virtual',
          protection => 'public',
          scope => 'IStorage'
        },
        {
          name => 'reconnect',
          virtualness => 'non_virtual',
          protection => 'public',
          scope => 'IStorage'
        },
        {
          name => 'update',
          virtualness => 'non_virtual',
          protection => 'public',
          scope => 'IStorage'
        }
      ],
      public_methods => {
        members => [
          {
            kind => 'function',
            name => 'init',
            virtualness => 'non_virtual',
            protection => 'public',
            static => 'no',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'Check and control the given configuration. '
                }
              ]
            },
            detailed => {
              doc => [
                {
                  type => 'text',
                  content => 'Called by the constructor '
                }
              ]
            },
            const => 'no',
            volatile => 'no',
            parameters => [
            ]
          },
          {
            kind => 'function',
            name => 'connect',
            virtualness => 'non_virtual',
            protection => 'public',
            static => 'no',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'Connect to the storage backend. '
                }
              ]
            },
            detailed => {},
            const => 'no',
            volatile => 'no',
            parameters => [
            ]
          },
          {
            kind => 'function',
            name => 'reconnect',
            virtualness => 'non_virtual',
            protection => 'public',
            static => 'no',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'Disconnect and reconnect from the storage backend. '
                }
              ]
            },
            detailed => {},
            const => 'no',
            volatile => 'no',
            parameters => [
            ]
          },
          {
            kind => 'function',
            name => 'disconnect',
            virtualness => 'non_virtual',
            protection => 'public',
            static => 'no',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'Disconnect from the storage backend. '
                }
              ]
            },
            detailed => {},
            const => 'no',
            volatile => 'no',
            parameters => [
            ]
          },
          {
            kind => 'function',
            name => 'getNativeConnection',
            virtualness => 'non_virtual',
            protection => 'public',
            static => 'no',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'Disconnect from the storage backend. '
                }
              ]
            },
            detailed => {},
            const => 'no',
            volatile => 'no',
            parameters => [
            ]
          },
          {
            kind => 'function',
            name => 'create',
            virtualness => 'non_virtual',
            protection => 'public',
            static => 'no',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'Execute an insert action on this storage (INSERT on SQL) and return a boolean. '
                }
              ]
            },
            detailed => {},
            const => 'no',
            volatile => 'no',
            parameters => [
              {
                declaration_name => '$table'
              },
              {
                declaration_name => '$data'
              }
            ]
          },
          {
            kind => 'function',
            name => 'read',
            virtualness => 'non_virtual',
            protection => 'public',
            static => 'no',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'Execute a search action on this storage (SELECT on SQL) and return resultSet. '
                }
              ]
            },
            detailed => {},
            const => 'no',
            volatile => 'no',
            parameters => [
              {
                declaration_name => '$table'
              },
              {
                declaration_name => '$criteria',
                default_value => 'array()'
              },
              {
                declaration_name => '$order',
                default_value => 'array()'
              },
              {
                declaration_name => '$start',
                default_value => '0'
              },
              {
                declaration_name => '$stop',
                default_value => '30'
              }
            ]
          },
          {
            kind => 'function',
            name => 'readOne',
            virtualness => 'non_virtual',
            protection => 'public',
            static => 'no',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'Execute a search action on this storage (SELECT on SQL) and return resultSet. '
                }
              ]
            },
            detailed => {},
            const => 'no',
            volatile => 'no',
            parameters => [
              {
                declaration_name => '$table'
              },
              {
                declaration_name => '$criteria',
                default_value => 'array()'
              }
            ]
          },
          {
            kind => 'function',
            name => 'readCount',
            virtualness => 'non_virtual',
            protection => 'public',
            static => 'no',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'Execute a search action on this storage (SELECT on SQL) and return resultSet. '
                }
              ]
            },
            detailed => {},
            const => 'no',
            volatile => 'no',
            parameters => [
              {
                declaration_name => '$table'
              },
              {
                declaration_name => '$criteria',
                default_value => 'array()'
              }
            ]
          },
          {
            kind => 'function',
            name => 'update',
            virtualness => 'non_virtual',
            protection => 'public',
            static => 'no',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'Execute an update action on this storage (UPDATE on SQL) and return a boolean. '
                }
              ]
            },
            detailed => {},
            const => 'no',
            volatile => 'no',
            parameters => [
              {
                declaration_name => '$table'
              },
              {
                declaration_name => '$key'
              },
              {
                declaration_name => '$id'
              },
              {
                declaration_name => '$data'
              }
            ]
          },
          {
            kind => 'function',
            name => 'delete',
            virtualness => 'non_virtual',
            protection => 'public',
            static => 'no',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'Execute a delete action on this storage (DELETE on SQL) and return a boolean. '
                }
              ]
            },
            detailed => {},
            const => 'no',
            volatile => 'no',
            parameters => [
              {
                declaration_name => '$table'
              },
              {
                declaration_name => '$key'
              },
              {
                declaration_name => '$id'
              }
            ]
          },
          {
            kind => 'function',
            name => '__destruct',
            virtualness => 'non_virtual',
            protection => 'public',
            static => 'no',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'Should implement a destructor to perform automatic disconnect at the end of process. '
                }
              ]
            },
            detailed => {},
            const => 'no',
            volatile => 'no',
            parameters => [
            ]
          }
        ]
      },
      brief => {},
      detailed => {}
    },
    {
      name => 'Model',
      all_members => [
      ],
      brief => {
        doc => [
          {
            type => 'text',
            content => 'Interface to define a model object. '
          }
        ]
      },
      detailed => {
        doc => [
          {
            see => [
              {
                type => 'text',
                content => 'DefaultModel, mysqlModel, nosqlModel '
              },
              {
                type => 'link',
                {
                  type => 'text',
                  content => 'https://github.com/startxfr/sxapi/wiki/Models'
                }
              }
            ]
          }
        ]
      }
    },
    {
      name => 'Output',
      all_members => [
      ],
      brief => {
        doc => [
          {
            type => 'text',
            content => 'Interface to define an output object. '
          }
        ]
      },
      detailed => {
        doc => [
          {
            see => [
              {
                type => 'text',
                content => 'DefaultOutput, HtmlOutput, JsonOutput, XmlOutput '
              },
              {
                type => 'link',
                {
                  type => 'text',
                  content => 'https://github.com/startxfr/sxapi/wiki/Outputs'
                }
              }
            ]
          }
        ]
      }
    },
    {
      name => 'Plugin',
      all_members => [
      ],
      brief => {
        doc => [
          {
            type => 'text',
            content => 'Interface to define a plugin object. '
          }
        ]
      },
      detailed => {
        doc => [
          {
            see => [
              {
                type => 'text',
                content => 'DefaultPlugin, trackingPlugin '
              },
              {
                type => 'link',
                {
                  type => 'text',
                  content => 'https://github.com/startxfr/sxapi/wiki/Plugins'
                }
              }
            ]
          }
        ]
      }
    },
    {
      name => 'Resource',
      all_members => [
      ],
      brief => {
        doc => [
          {
            type => 'text',
            content => 'Interface to define a resource object. '
          }
        ]
      },
      detailed => {
        doc => [
          {
            see => [
              {
                type => 'text',
                content => 'defaultResource '
              },
              {
                type => 'link',
                {
                  type => 'text',
                  content => 'https://github.com/startxfr/sxapi/wiki/Resources'
                }
              }
            ]
          }
        ]
      }
    },
    {
      name => 'Store',
      all_members => [
      ],
      brief => {
        doc => [
          {
            type => 'text',
            content => 'Interface to define a storage object (connecting and read/write into various storage) '
          }
        ]
      },
      detailed => {
        doc => [
          {
            see => [
              {
                type => 'text',
                content => 'defaultStore, mysqlStore, nosqlStore '
              },
              {
                type => 'link',
                {
                  type => 'text',
                  content => 'https://github.com/startxfr/sxapi/wiki/Stores'
                }
              }
            ]
          }
        ]
      }
    },
    {
      name => 'Toolkit',
      all_members => [
        {
          name => 'array2Object',
          virtualness => 'non_virtual',
          protection => 'public',
          scope => 'Toolkit'
        },
        {
          name => 'array_merge_recursive_distinct',
          virtualness => 'non_virtual',
          protection => 'public',
          scope => 'Toolkit'
        },
        {
          name => 'object2Array',
          virtualness => 'non_virtual',
          protection => 'public',
          scope => 'Toolkit'
        },
        {
          name => 'string2Array',
          virtualness => 'non_virtual',
          protection => 'public',
          scope => 'Toolkit'
        }
      ],
      public_static_methods => {
        members => [
          {
            kind => 'function',
            name => 'object2Array',
            virtualness => 'non_virtual',
            protection => 'public',
            static => 'yes',
            brief => {
              doc => [
                {
                  type => 'parbreak'
                },
                {
                  type => 'text',
                  content => 'Convert array to object. '
                }
              ]
            },
            detailed => {},
            type => 'static',
            const => 'no',
            volatile => 'no',
            parameters => [
              {
                declaration_name => '$obj'
              }
            ]
          },
          {
            kind => 'function',
            name => 'array2Object',
            virtualness => 'non_virtual',
            protection => 'public',
            static => 'yes',
            brief => {
              doc => [
                {
                  type => 'parbreak'
                },
                {
                  type => 'text',
                  content => 'Convert array to object. '
                }
              ]
            },
            detailed => {},
            type => 'static',
            const => 'no',
            volatile => 'no',
            parameters => [
              {
                declaration_name => '$array'
              }
            ]
          },
          {
            kind => 'function',
            name => 'string2Array',
            virtualness => 'non_virtual',
            protection => 'public',
            static => 'yes',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'Convert string to array according to a sep. '
                }
              ]
            },
            detailed => {},
            type => 'static',
            const => 'no',
            volatile => 'no',
            parameters => [
              {
                declaration_name => '$string'
              },
              {
                declaration_name => '$sep',
                default_value => '\',\''
              }
            ]
          },
          {
            kind => 'function',
            name => 'array_merge_recursive_distinct',
            virtualness => 'non_virtual',
            protection => 'public',
            static => 'yes',
            brief => {},
            detailed => {},
            type => 'static &',
            const => 'no',
            volatile => 'no',
            parameters => [
              {
                declaration_name => '$array1',
                type => 'array &'
              },
              {
                declaration_name => '$array2',
                type => '&',
                default_value => 'null'
              }
            ]
          }
        ]
      },
      brief => {
        doc => [
          {
            type => 'text',
            content => 'Small toolkit library. '
          }
        ]
      },
      detailed => {
        doc => [
          {
            type => 'text',
            content => 'Currently used for conversion of array and object structures. Could be used to record small an re-usable function'
          },
          {
            type => 'parbreak'
          },
          {
            author => [
              {
                type => 'text',
                content => 'Dev Team '
              },
              {
                type => 'url',
                content => 'dev@startx.fr'
              },
              {
                type => 'text',
                content => ' '
              }
            ]
          },
          {
            copyright => [
              {
                type => 'text',
                content => 'Copyright (c) 2003-2013 startx.fr  '
              },
              {
                type => 'url',
                content => 'https://github.com/startxfr/sxapi/blob/master/licence.txt'
              },
              {
                type => 'text',
                content => ' '
              }
            ]
          }
        ]
      }
    }
  ],
  namespaces => [
    {
      name => 'SXAPI',
      classes => [
      ],
      namespaces => [
        {
          name => 'SXAPI::Input'
        },
        {
          name => 'SXAPI::Model'
        },
        {
          name => 'SXAPI::Output'
        },
        {
          name => 'SXAPI::Plugin'
        },
        {
          name => 'SXAPI::Resource'
        },
        {
          name => 'SXAPI::Store'
        }
      ],
      brief => {},
      detailed => {
        doc => [
          {
            author => [
              {
                type => 'text',
                content => 'Dev Team '
              },
              {
                type => 'url',
                content => 'dev@startx.fr'
              },
              {
                type => 'text',
                content => ' '
              }
            ]
          },
          {
            copyright => [
              {
                type => 'text',
                content => 'Copyright (c) 2003-2013 startx.fr  '
              },
              {
                type => 'url',
                content => 'https://github.com/startxfr/sxapi/blob/master/licence.txt'
              },
              {
                type => 'text',
                content => ' '
              }
            ]
          }
        ]
      }
    },
    {
      name => 'SXAPI::Input',
      classes => [
      ],
      namespaces => [
      ],
      brief => {},
      detailed => {
        doc => [
          {
            author => [
              {
                type => 'text',
                content => 'Dev Team '
              },
              {
                type => 'url',
                content => 'dev@startx.fr'
              },
              {
                type => 'text',
                content => ' '
              }
            ]
          },
          {
            copyright => [
              {
                type => 'text',
                content => 'Copyright (c) 2003-2013 startx.fr  '
              },
              {
                type => 'url',
                content => 'https://github.com/startxfr/sxapi/blob/master/licence.txt'
              },
              {
                type => 'text',
                content => ' '
              }
            ]
          }
        ]
      }
    },
    {
      name => 'SXAPI::Model',
      classes => [
      ],
      namespaces => [
      ],
      brief => {},
      detailed => {
        doc => [
          {
            author => [
              {
                type => 'text',
                content => 'Dev Team '
              },
              {
                type => 'url',
                content => 'dev@startx.fr'
              },
              {
                type => 'text',
                content => ' '
              }
            ]
          },
          {
            copyright => [
              {
                type => 'text',
                content => 'Copyright (c) 2003-2013 startx.fr  '
              },
              {
                type => 'url',
                content => 'https://github.com/startxfr/sxapi/blob/master/licence.txt'
              },
              {
                type => 'text',
                content => ' '
              }
            ]
          }
        ]
      }
    },
    {
      name => 'SXAPI::Output',
      classes => [
      ],
      namespaces => [
      ],
      brief => {},
      detailed => {
        doc => [
          {
            author => [
              {
                type => 'text',
                content => 'Dev Team '
              },
              {
                type => 'url',
                content => 'dev@startx.fr'
              },
              {
                type => 'text',
                content => ' '
              }
            ]
          },
          {
            copyright => [
              {
                type => 'text',
                content => 'Copyright (c) 2003-2013 startx.fr  '
              },
              {
                type => 'url',
                content => 'https://github.com/startxfr/sxapi/blob/master/licence.txt'
              },
              {
                type => 'text',
                content => ' '
              }
            ]
          }
        ]
      }
    },
    {
      name => 'SXAPI::Plugin',
      classes => [
      ],
      namespaces => [
      ],
      brief => {},
      detailed => {
        doc => [
          {
            author => [
              {
                type => 'text',
                content => 'Dev Team '
              },
              {
                type => 'url',
                content => 'dev@startx.fr'
              },
              {
                type => 'text',
                content => ' '
              }
            ]
          },
          {
            copyright => [
              {
                type => 'text',
                content => 'Copyright (c) 2003-2013 startx.fr  '
              },
              {
                type => 'url',
                content => 'https://github.com/startxfr/sxapi/blob/master/licence.txt'
              },
              {
                type => 'text',
                content => ' '
              }
            ]
          }
        ]
      }
    },
    {
      name => 'SXAPI::Resource',
      classes => [
      ],
      namespaces => [
      ],
      brief => {},
      detailed => {
        doc => [
          {
            author => [
              {
                type => 'text',
                content => 'Dev Team '
              },
              {
                type => 'url',
                content => 'dev@startx.fr'
              },
              {
                type => 'text',
                content => ' '
              }
            ]
          },
          {
            copyright => [
              {
                type => 'text',
                content => 'Copyright (c) 2003-2013 startx.fr  '
              },
              {
                type => 'url',
                content => 'https://github.com/startxfr/sxapi/blob/master/licence.txt'
              },
              {
                type => 'text',
                content => ' '
              }
            ]
          }
        ]
      }
    },
    {
      name => 'SXAPI::Store',
      classes => [
      ],
      namespaces => [
      ],
      brief => {},
      detailed => {
        doc => [
          {
            author => [
              {
                type => 'text',
                content => 'Dev Team '
              },
              {
                type => 'url',
                content => 'dev@startx.fr'
              },
              {
                type => 'text',
                content => ' '
              }
            ]
          },
          {
            copyright => [
              {
                type => 'text',
                content => 'Copyright (c) 2003-2013 startx.fr  '
              },
              {
                type => 'url',
                content => 'https://github.com/startxfr/sxapi/blob/master/licence.txt'
              },
              {
                type => 'text',
                content => ' '
              }
            ]
          }
        ]
      }
    }
  ],
  files => [
    {
      name => 'api.php',
      includes => [
      ],
      included_by => [
      ],
      brief => {},
      detailed => {}
    },
    {
      name => 'configurable.php',
      includes => [
      ],
      included_by => [
      ],
      brief => {},
      detailed => {}
    },
    {
      name => 'event.php',
      includes => [
      ],
      included_by => [
      ],
      brief => {},
      detailed => {}
    },
    {
      name => 'index.php',
      includes => [
      ],
      included_by => [
      ],
      brief => {},
      detailed => {}
    },
    {
      name => 'interfaces.php',
      includes => [
      ],
      included_by => [
      ],
      brief => {},
      detailed => {}
    },
    {
      name => 'loader.php',
      includes => [
      ],
      included_by => [
      ],
      functions => {
        members => [
          {
            kind => 'function',
            name => 'autoloader',
            virtualness => 'non_virtual',
            protection => 'public',
            static => 'no',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'Function used for automatic loading of classes based on camelCase suffix. '
                }
              ]
            },
            detailed => {
              doc => [
                {
                  type => 'text',
                  content => 'If class end with '
                },
                {
                  type => 'url',
                  link => 'class_resource',
                  content => 'Resource'
                },
                {
                  type => 'text',
                  content => ', '
                },
                {
                  type => 'url',
                  link => 'class_model',
                  content => 'Model'
                },
                {
                  type => 'text',
                  content => ', Exception, '
                },
                {
                  type => 'url',
                  link => 'class_store',
                  content => 'Store'
                },
                {
                  type => 'text',
                  content => ', '
                },
                {
                  type => 'url',
                  link => 'class_output',
                  content => 'Output'
                },
                {
                  type => 'text',
                  content => ' or '
                },
                {
                  type => 'url',
                  link => 'class_input',
                  content => 'Input'
                },
                {
                  type => 'text',
                  content => ' then look into the appropriate directory. If '
                },
                {
                  type => 'url',
                  link => 'class_resource',
                  content => 'Resource'
                },
                {
                  type => 'text',
                  content => ' is used, autoload search for a subpackage with pre-suffix founded.'
                },
                {
                  type => 'parbreak'
                },
                params => [
                  {
                    parameters => [
                      {
                        name => '$classname'
                      }
                    ],
                    doc => [
                      {
                        type => 'text',
                        content => 'name of the class to load '
                      }
                    ]
                  }
                ],
                {
                  return => [
                    {
                      type => 'text',
                      content => 'boolean if ok. Throw an exception if not '
                    }
                  ]
                }
              ]
            },
            const => 'no',
            volatile => 'no',
            parameters => [
              {
                declaration_name => '$classname'
              }
            ]
          }
        ]
      },
      variables => {
        members => [
          {
            kind => 'variable',
            name => 'DEBUG',
            virtualness => 'non_virtual',
            protection => 'public',
            static => 'no',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'the directory separator used for this instance '
                }
              ]
            },
            detailed => {},
            type => 'const',
            initializer => 'true'
          },
          {
            kind => 'variable',
            name => 'LOG_VERBOSITY',
            virtualness => 'non_virtual',
            protection => 'public',
            static => 'no',
            brief => {},
            detailed => {},
            type => 'const',
            initializer => '5'
          },
          {
            kind => 'variable',
            name => 'DS',
            virtualness => 'non_virtual',
            protection => 'public',
            static => 'no',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'the directory separator used for this instance '
                }
              ]
            },
            detailed => {},
            type => 'const',
            initializer => 'DIRECTORY_SEPARATOR'
          },
          {
            kind => 'variable',
            name => 'EXT',
            virtualness => 'non_virtual',
            protection => 'public',
            static => 'no',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'the extention file used in this instance '
                }
              ]
            },
            detailed => {},
            type => 'const',
            initializer => '\'.\' . pathinfo(__FILE__, PATHINFO_EXTENSION)'
          },
          {
            kind => 'variable',
            name => 'BASEPATH',
            virtualness => 'non_virtual',
            protection => 'public',
            static => 'no',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'the root path of this application instance '
                }
              ]
            },
            detailed => {},
            type => 'const',
            initializer => 'pathinfo(pathinfo(__FILE__, PATHINFO_DIRNAME), PATHINFO_DIRNAME) . DS'
          },
          {
            kind => 'variable',
            name => 'KERNPATH',
            virtualness => 'non_virtual',
            protection => 'public',
            static => 'no',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'the lib path for loading kernel components and core features '
                }
              ]
            },
            detailed => {},
            type => 'const',
            initializer => 'BASEPATH . \'kernel\' . DS'
          },
          {
            kind => 'variable',
            name => 'LIBPATH',
            virtualness => 'non_virtual',
            protection => 'public',
            static => 'no',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'the lib path for loading MVC components '
                }
              ]
            },
            detailed => {},
            type => 'const',
            initializer => 'BASEPATH . \'lib\' . DS'
          },
          {
            kind => 'variable',
            name => 'LIBPATHEXT',
            virtualness => 'non_virtual',
            protection => 'public',
            static => 'no',
            brief => {
              doc => [
                {
                  type => 'text',
                  content => 'the lib path for loading external projects (php-ga,google-api-php-client) '
                }
              ]
            },
            detailed => {},
            type => 'const',
            initializer => 'BASEPATH . \'lib-ext\' . DS'
          }
        ]
      },
      brief => {},
      detailed => {}
    },
    {
      name => 'toolkit.php',
      includes => [
      ],
      included_by => [
      ],
      brief => {},
      detailed => {}
    },
    {
      name => 'README.md',
      includes => [
      ],
      included_by => [
      ],
      brief => {},
      detailed => {}
    }
  ],
  groups => [
  ],
  pages => [
    {
      name => 'index',
      title => 'SXAPI',
      detailed => {
        doc => [
          {
            type => 'url',
            link => 'namespace_s_x_a_p_i',
            content => 'SXAPI'
          },
          {
            type => 'text',
            content => ' is a lightweight and flexible framework for building open or private API with a minimum of time. Based on PHP and NoSQL technologies, this framework allow developpers to build their own API an deliver in a unified way various informations resources.'
          },
          {
            type => 'parbreak'
          },
          {
            type => 'text',
            content => 'For more informations, please visit the wiki pages of the project on '
          },
          {
            type => 'url',
            content => 'https://github.com/startxfr/sxapi/wiki'
          },
          {
            type => 'text',
            content => ' '
          }
        ]
      }
    }
  ]
};
1;
