<?php

namespace g4t\Pattern;

use g4t\Pattern\Commands\Validation;
use Illuminate\Support\Facades\DB;

class GenerateValidation extends Validation
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'repo:validation
                            {model : Model Name}
                            ';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto generate validation';

    /**
     * Create a new command instance.
     *
     * @return void
     */

    protected $requestValidation = __DIR__ . '/stubs/requestValidation.stub';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        /**
         * Model Name.
         */
        $model = $this->argument('model');

        /**
         * Model full path.
         *
         * @return string
         */
        $full_model = $this->model($model);

        /**
         * get table name from model.
         *
         * @return string
         */
        try {
            $table = $this->tableName($full_model);
        } catch (\Throwable $th) {
            return $this->comment('[' . $model . '] Model Not Found.');
        }

        /**
         * Request file path.
         */
        $path = 'app/Http/Requests/' . $model;

        /**
         * create path when not exists.
         */
        if (!is_dir($path)) {
            mkdir($path, 0777);
        }

        /**
         * create validations.
         *
         * @return string
         */
        $validation = $this->generateValidation($table, $model);

        /**
         * create request file.
         * update request file.
         */
        $create = $path . '/Create' . $model . '.php';
        $update = $path . '/Update' . $model . '.php';
        $this->createRequest($create, 'Create' . $model, $validation, $model);
        $this->createRequest($update, 'Update' . $model, $validation, $model);

        /**
         * return message.
         */
        return $this->comment('app\\Http\\Requests\\' . $model . ' >> Created.');
    }
}
