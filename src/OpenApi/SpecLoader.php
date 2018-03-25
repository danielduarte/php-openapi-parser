<?php

namespace OpenApi;

use GenericEntity\FactorySingleton;
use GenericEntity\SpecException;
use Symfony\Component\Yaml\Yaml;


class SpecLoader
{
    const OPENAPI_SPEC_NAME = 'OpenApi';

    protected $_openApiSpec = null;

    public function __construct()
    {
        $this->_openApiSpec = $this->_loadOpenApiSpec();
    }

    public function loadFromFile($specFileYaml)
    {
        $errors = [];

        $userSpec = Yaml::parseFile($specFileYaml);
        if ($userSpec === null) {
            $errors[] = "Not valid Yaml format in file '$specFileYaml'.";
        } else {

            try {
                $openapiUserSpec = $this->_parse($userSpec);
            } catch (\GenericEntity\SpecException $ex) {
                $errors = $ex->getErrors();
                $openapiUserSpec = null;
            }
        }

        if (count($errors) > 0) {
            throw new SpecException("Invalid spec in file '$specFileYaml'.", $errors);
        }

        return $openapiUserSpec;
    }

    public function loadFromString($specStringYaml)
    {
        $errors = [];

        $userSpec = Yaml::parse($specStringYaml);
        if ($userSpec === null) {
            $errors[] = "Not valid Yaml format in given string.";
        } else {

            try {
                $openapiUserSpec = $this->_parse($userSpec);
            } catch (\GenericEntity\SpecException $ex) {
                $errors = $ex->getErrors();
                $openapiUserSpec = null;
            }
        }

        if (count($errors) > 0) {
            throw new SpecException("Invalid spec in given string.", $errors);
        }

        return $openapiUserSpec;
    }

    public function loadFromArray($specArray)
    {
        $errors = [];

        $userSpec = is_array($specArray) ? $specArray : null;
        if ($userSpec === null) {
            $errors[] = "Not valid array given.";
        } else {

            try {
                $openapiUserSpec = $this->_parse($userSpec);
            } catch (\GenericEntity\SpecException $ex) {
                $errors = $ex->getErrors();
                $openapiUserSpec = null;
            }
        }

        if (count($errors) > 0) {
            throw new SpecException("Invalid spec in given array.", $errors);
        }

        return $openapiUserSpec;
    }

    protected function _parse($userSpec)
    {
        $factory = FactorySingleton::getInstance();

        return $factory->createEntity(static::OPENAPI_SPEC_NAME, $userSpec);
    }

    protected function _loadOpenApiSpec()
    {
        $entityFactory = FactorySingleton::getInstance();

        if (!$entityFactory->hasSpec(static::OPENAPI_SPEC_NAME)) {
            $this->_createOpenApiSpec(static::OPENAPI_SPEC_NAME);
        }

        return $entityFactory->getSpec(static::OPENAPI_SPEC_NAME);
    }

    protected function _createOpenApiSpec()
    {
        $entityFactory = FactorySingleton::getInstance();

        $metaSpecOpenApi = [
            'openapi'      => ['type' => 'string'],
            'info'         => ['type' => 'object',
                               'fields' => [
                                   'title'          => ['type' => 'string'],
                                   'description'    => ['type' => 'string'],
                                   'termsOfService' => ['type' => 'string'],
                                   'contact'        => ['type' => 'object',
                                                 'fields' => [
                                                     'name'  => ['type' => 'string'],
                                                     'url'   => ['type' => 'string'],
                                                     'email' => ['type' => 'string'],
                                                 ]
                                   ],
                                   'license'        => ['type' => 'object',
                                                 'fields' => [
                                                     'name'  => ['type' => 'string'],
                                                     'url'   => ['type' => 'string'],
                                                 ]
                                   ],
                                   'version'        => ['type' => 'string']
                               ],
            ],
            'servers'      => ['type'  => 'array',
                               'items' => [
                                   'description' => ['type' => 'string'],
                                   'url'         => ['type' => 'string'],
                               ]
            ],
            'paths'        => ['type' => 'object',
                               'fields' => [],
                               'extensible' => true
            ],
            'components'   => ['type' => 'object',
                               'fields' => [],
                               'extensible' => true
            ],
            'security'     => ['type' => 'array'],
            'tags'         => ['type' => 'array',
                               'items' => [
                                   'name'         => ['type' => 'string'],
                                   'description'  => ['type' => 'string'],
                                   'externalDocs' => ['type' => 'object',
                                                      'fields' => [
                                                          'description'  => ['type' => 'string'],
                                                          'url'          => ['type' => 'string'],
                                                      ]
                                   ],
                               ]
            ],
            'externalDocs' => ['type' => 'object',
                               'fields' => [],
                               'extensible' => true
            ],
        ];

        $meta = [
            'type'       => 'object',
            'fields'     => $metaSpecOpenApi,
            'extensible' => false
        ];
        $openApiSpec = $entityFactory->createSpec(static::OPENAPI_SPEC_NAME, $meta);

        return $openApiSpec;
    }
}
