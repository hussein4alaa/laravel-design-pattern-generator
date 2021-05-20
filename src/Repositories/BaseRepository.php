<?php

namespace g4t\Pattern\Repositories;

use Spatie\QueryBuilder\QueryBuilder;

use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository
{

    public $table;
    public function __construct(Model $model)
    {
        $this->table = $model;
    }

    public function getType()
    {
        $className = get_class($this->table);
        $name = class_basename($className);
        return strtolower($name);
    }

    public function index($take = null, $skip = 0, $where = null)
    {
        $data = QueryBuilder::for($this->table)
            ->allowedSorts(config("jsonapi.resources.{$this->getType()}.allowedSorts"))
            ->allowedIncludes(config("jsonapi.resources.{$this->getType()}.allowedIncludes"))
            ->allowedFilters(config("jsonapi.resources.{$this->getType()}.allowedFilters"));
            if($where) {
                $data = $data->where($where['column'], $where['condition'], $where['value']);
            }
        return [
            'total' => $data->count(),
            'items' => ($take == null) ? $data->get() : $data->take($take)->skip($skip)->get()
        ];
        return $data;
    }

    public function show($id)
    {
        $data = QueryBuilder::for($this->table)
            ->allowedIncludes(config("jsonapi.resources.{$this->getType()}.allowedIncludes"))
            ->findOrFail($id);
        return $data;
    }

    public function store($data)
    {
        $id = $this->table->insertGetId($data);
        $response = [
            'message' => '' . $this->getType() . '  Created',
            'code' => 200,
            'id' => $id
        ];
        return $response;
    }

    public function update($id, $data)
    {
        $this->table->findOrFail($id)->update($data);
        $response = ['message' => '' . $this->getType() . '  Updated', 'code' => 200];
        return $response;
    }


    public function destroy($id)
    {
        $this->table->findOrFail($id)->delete();
        $response = ['message' => '' . $this->getType() . '  Deleted', 'code' => 200];
        return $response;
    }

}
