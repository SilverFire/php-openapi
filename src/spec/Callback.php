<?php

/**
 * @copyright Copyright (c) 2018 Carsten Brandt <mail@cebe.cc> and contributors
 * @license https://github.com/cebe/php-openapi/blob/master/LICENSE
 */

namespace cebe\openapi\spec;

use cebe\openapi\exceptions\TypeErrorException;
use cebe\openapi\exceptions\UnresolvableReferenceException;
use cebe\openapi\ReferenceContext;
use cebe\openapi\SpecObjectInterface;

/**
 * A map of possible out-of band callbacks related to the parent operation.
 *
 * @link https://github.com/OAI/OpenAPI-Specification/blob/3.0.2/versions/3.0.2.md#callbackObject
 *
 */
class Callback implements SpecObjectInterface
{
    private $_url;
    private $_pathItem;

    private $_errors = [];


    /**
     * Create an object from spec data.
     * @param array $data spec data read from YAML or JSON
     * @throws TypeErrorException in case invalid data is supplied.
     */
    public function __construct(array $data)
    {
        if (count($data) !== 1) {
            $this->_errors[] = 'Callback object must have exactly one URL.';
            return;
        }
        $this->_pathItem = new PathItem(reset($data));
        $this->_url = key($data);
    }

    /**
     * @return mixed returns the serializable data of this object for converting it
     * to JSON or YAML.
     */
    public function getSerializableData()
    {
        return (object) [$this->_url => ($this->_pathItem === null) ? null : $this->_pathItem->getSerializableData()];
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->_url;
    }

    /**
     * @param string $url
     */
    public function setUrl(string $url): void
    {
        $this->_url = $url;
    }

    /**
     * @return PathItem
     */
    public function getRequest(): ?PathItem
    {
        return $this->_pathItem;
    }

    /**
     * @param PathItem $request
     */
    public function setRequest(?PathItem $request): void
    {
        $this->_pathItem = $request;
    }

    /**
     * Validate object data according to OpenAPI spec.
     * @return bool whether the loaded data is valid according to OpenAPI spec
     * @see getErrors()
     */
    public function validate(): bool
    {
        $pathItemValid = $this->_pathItem === null || $this->_pathItem->validate();
        return $pathItemValid && empty($this->_errors);
    }

    /**
     * @return string[] list of validation errors according to OpenAPI spec.
     * @see validate()
     */
    public function getErrors(): array
    {
        $pathItemErrors = $this->_pathItem === null ? [] : $this->_pathItem->getErrors();
        return array_merge($this->_errors, $pathItemErrors);
    }

    /**
     * Resolves all Reference Objects in this object and replaces them with their resolution.
     * @throws UnresolvableReferenceException
     */
    public function resolveReferences(ReferenceContext $context = null)
    {
        if ($this->_pathItem !== null) {
            $this->_pathItem->resolveReferences($context);
        }
    }

    /**
     * Set context for all Reference Objects in this object.
     */
    public function setReferenceContext(ReferenceContext $context)
    {
        if ($this->_pathItem !== null) {
            $this->_pathItem->setReferenceContext($context);
        }
    }
}
