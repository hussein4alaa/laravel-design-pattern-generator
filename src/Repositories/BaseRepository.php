<?php

namespace App\Http\Repositories;

use Spatie\QueryBuilder\QueryBuilder;

use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository extends Helpers
{

    public $table;
    public function __construct(Model $model)
    {
        $this->table = $model;
    }


    public function index($take = null, $skip = 0)
    {
        $data = QueryBuilder::for($this->table)
            ->allowedSorts($this->sort())
            ->allowedIncludes($this->include())
            ->allowedFilters($this->filter());
        return [
            'total' => $data->count(),
            'items' => ($take == null) ? $data->get() : $data->take($take)->skip($skip)->get()
        ];
        return $data;
    }

    public function show($id)
    {
        $data = QueryBuilder::for($this->table)
            ->allowedIncludes($this->include())
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
