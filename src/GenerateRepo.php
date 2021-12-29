<?php

namespace g4t\Pattern;

use g4t\Pattern\Commands\Repository;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Artisan;

class GenerateRepo extends Repository
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:repo
                        {name : Repository name}
                        {--m : Create migration}
                        {--model= : Insert model in controller}
                        {--force : Allows to override existing Repository}
                        ';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create repository';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    protected $controller = __DIR__ . '/stubs/controller.stub';
    protected $model = __DIR__ . '/stubs/model.stub';
    protected $jsonapi = __DIR__ . '/stubs/jsonapi.stub';
    protected $repository = __DIR__ . '/stubs/repository.stub';
    protected $request = __DIR__ . '/stubs/request.stub';
    protected $limitRequest = __DIR__ . '/stubs/limitRequest.stub';

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
        $response = [];
        $path = 'app/Http/Repositories';
        $name = $this->argument('name');

        /**
         * conver model name to plural.
         *
         * @return string
         */
        $table = Str::plural($name);

        /**
         * make migration.
         *
         * @return string
         */
        $return[] = $this->migration($table);

        $file = $path . '/' . $name . 'Repository.php';

        /**
         * check if path exists.
         * create path
         */
        if (!is_dir($path)) {
            mkdir($path, 0777);
        }

        /**
         * force create.
         *
         * @return string
         */
        if (!$this->option('force') and file_exists($file)) {
            return $this->comment($name . 'Repository exists. Use --force to override');
        }

        /**
         * conver model name to plural.
         * create model
         *
         * @return string
         */
        if ($this->option('model')) {
            $model = $this->option('model');
        } else {
            $model = ucfirst($name);
            $this->model($name);
            array_push($response, 'Model');
        }


        /**
         *create controller
         *
         * @return string
         */
            $this->Controller($name, $model);
            array_push($response, 'Controller');


        /**
         *Add Api Route
         *
         * @return string
         */
            $this->route($name);
            array_push($response, 'Route');


        /**
         * Create Repository
         *
         * @return string
         */
        $this->Repo($name, $file);
        array_push($response, 'Repostitory');

        /**
         * Create 'jsonapi' file
         *
         * @return string
         */
        $this->jsonapi($name);

        /**
         * Convert 'return' To string
         *
         * @return string
         */
        $data = implode(", ", $response);

        /**
         * response message
         *
         * @return string
         */
        return $this->comment($name . ' (' . $data . ') Created.');
    }
}
