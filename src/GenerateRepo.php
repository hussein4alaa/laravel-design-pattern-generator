<?php

namespace g4t\Pattern;

use Illuminate\Console\Command;
use Illuminate\Http\Testing\File;

class GenerateRepo extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:repo 
                        {name : Repo name}
                        {--c : Create Controller With Repository}
                        {--m : Create Model With Repository}
                        {--r : Create apiResource Route}
                        {--force : Allows to override existing Repository}
                        {--model= : Insert model in controller}
                        ';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    protected $controller = __DIR__ . '/stubs/controller.stub';
    protected $model = __DIR__ . '/stubs/model.stub';
    protected $jsonapi = __DIR__ . '/stubs/jsonapi.stub';
    protected $repository = __DIR__ . '/stubs/repository.stub';

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
        $path = 'app/Http/Repositories';
        $name = $this->argument('name');
        $file = $path . '/' . $name . 'Repository.php';


        if (!is_dir($path)) {
            mkdir($path, 0777);
        }

        if (!$this->option('force') and file_exists($file)) {
            return $this->comment($name . 'Repository exists. Use --force to override');
        }

        if ($this->option('model')) {
            $model = $this->option('model');
        } else if ($this->option('m')) {
            $model = ucfirst($name);
            $this->model($name);
            $return[] = 'Model';
        } else {
            $model = null;
        }

        if ($this->option('c')) {
            $this->Controller($name, $model);
            $return[] = 'Controller';
        }

        if ($this->option('r')) {
            $this->route($name);
            $return[] = 'Route';
        }



        $this->Repo($name, $file);
        $this->jsonapi($name);

        $return[] = 'Repostitory';
        $data = implode(", ", $return);

        return $this->comment($name . ' (' . $data . ') Created.');
    }


    public function Repo($name, $file)
    {
        $myfile = fopen($file, "w") or die("Unable to open file!");
        $file = file_get_contents($this->repository);
        $text = "<?php\n";
        fwrite($myfile, $text);
        $result = str_replace('{{name}}', $name, $file);
        fwrite($myfile, $result);
        fclose($myfile);
    }



    public function Controller($name, $model)
    {
        if (!$model) {
            $model = "ModelHere";
        }
        $path = 'app\Http\Controllers\\' . $name . 'Controller.php';
        $myfile = fopen($path, "w") or die("Unable to open file!");
        $file = file_get_contents($this->controller);
        $text = "<?php\n";
        fwrite($myfile, $text);
        $result = str_replace('{{name}}', $name, $file);
        $result = str_replace('{{model}}', $model, $result);
        fwrite($myfile, $result);
        fclose($myfile);
    }



    public function model($name)
    {
        $path = 'app/' . $name . '.php';
        $myfile = fopen($path, "w") or die("Unable to open file!");
        $file = file_get_contents($this->model);
        $text = "<?php\n";
        fwrite($myfile, $text);
        $result = str_replace('{{name}}', $name, $file);
        fwrite($myfile, $result);
        fclose($myfile);
    }



    public function route($name)
    {
        $controller = $name . 'Controller';
        $file = "routes/api.php";
        $fc = fopen($file, "r");
        while (!feof($fc)) {
            $buffer = fgets($fc, 4096);
            $lines[] = $buffer;
        }
        fclose($fc);
        $f = fopen($file, "w") or die("couldn't open $file");
        $lineCount = count($lines);
        for ($i = 0; $i < $lineCount - 1; $i++) {
            fwrite($f, $lines[$i]);
        }

        $laravel = app();
        $version = $laravel::VERSION;
        if ($version >= 8) {
            $route_8 = 'App\Http\Controllers\\' . $controller . '::class';
            fwrite($f, "Route::apiResource('" . strtolower($name) . "', $route_8);" . PHP_EOL);
        } else {
            fwrite($f, "Route::apiResource('" . strtolower($name) . "', '$controller');" . PHP_EOL);
        }
        fwrite($f, $lines[$lineCount - 1]);
        fclose($f);
    }

    public function jsonapi($name)
    {
        $include = include(config_path('jsonapi.php'));
        if (!array_key_exists(strtolower($name), $include['resources'])) {
            $file = file_get_contents($this->jsonapi);
            $result = str_replace('{{name}}', strtolower($name), $file);

            $filename = config_path('jsonapi.php');
            $lines = file($filename, FILE_IGNORE_NEW_LINES);
            $line = array_search('//dont-remove-or-edit-this-line', $lines, true);
            $lines[$line - 1] = $result;
            file_put_contents($filename, implode("\n", $lines));
        }
    }
}
