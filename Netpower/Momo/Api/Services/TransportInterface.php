<?php

namespace Netpower\Momo\Api\Services;

interface TransportInterface 
{
    /**
     * Post API
     * @param string $module
     * @param array $data
     * @return json
     */
	public function post($module, $data = null);


    /**
     * Support for post API
     * @param string $module
     * @param array $api
     */
    public function call($method, $api, $data = null);

	/**
     * Post API
     * @param variable log $variable
     * @return log
     */
	public function log($variable);
}