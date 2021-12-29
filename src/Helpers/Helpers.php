<?php

namespace App\Http\Repositories;

class Helpers
{

    /**
     * get Type
     *
     * @return string
     */
    public function getType()
    {
        $className = get_class($this->table);
        $name = class_basename($className);
        return strtolower($name);
    }

    /**
     * get data from jsonapi.json
     *
     * @return string
     */
    public function query()
    {
        $path = "../config/jsonapi.json";
        $json_object = file_get_contents($path);
        $data = json_decode($json_object, true);
        return $data;
    }


    /**
     * sort data
     *
     * @return null
     */
    public function sort()
    {
        $data = $this->query();
        return $data['resources'][$this->getType()]['allowedSorts'];
    }

    /**
     * include relation
     *
     * @return null
     */
    public function include()
    {
        $data = $this->query();
        return $data['resources'][$this->getType()]['allowedIncludes'];
    }

    /**
     * filter data
     *
     * @return null
     */
    public function filter()
    {
        $data = $this->query();
        return $data['resources'][$this->getType()]['allowedFilters'];
    }

}
