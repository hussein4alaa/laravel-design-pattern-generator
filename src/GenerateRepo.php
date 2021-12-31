<?php

namespace g4t\Pattern\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class Repository extends Command
{

    public function migration($table)
    {
        try {
            Artisan::call('make:migration create_'.strtolower($table).'_table');
            $return[] = 'Migration';
        } catch (\Throwable $th) {

        }
    }


    public function model($name)
    {
        $laravel = app();
        $version = $laravel::VERSION;
        if ($version >= 8) {
            $namespace = 'App\\Models';
        } else {
            $namespace = 'App';
        }
        $namespace = 'App\\Models';
        $path = 'app/' . $name . '.php';
        $myfile = fopen($path, "w") or die("Unable to open file!");
        $file = file_get_contents($this->model);
        $text = "<?php\n";
        fwrite($myfile, $text);
        $result = str_replace('{{name}}', $name, $file);
        $result = str_replace('{{namespace}}', $namespace, $result);
        fwrite($myfile, $result);
        fclose($myfile);
    }



    public function Controller($name, $model)
    {
        if (!$model) {
            $model = "ModelHere";
        }
        $laravel = app();
        $version = $laravel::VERSION;
        if ($version >= 8) {
            $model_namespace = 'App\\Models\\'.$model;
        } else {
            $model_namespace = 'App\\'.$model;
        }
        $path = 'app\Http\Controllers\\' . $name . 'Controller.php';
        $myfile = fopen($path, "w") or die("Unable to open file!");
        $file = file_get_contents($this->controller);
        $text = "<?php\n";
        fwrite($myfile, $text);
        $result = str_replace('{{name}}', $name, $file);
        $result = str_replace('{{model}}', $model_namespace, $result);
        fwrite($myfile, $result);
        fclose($myfile);
        $this->Request($name);
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



    public function jsonapi($resource)
    {
        $resource = strtolower($resource);
        $rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
        $path = "$rootDir/config/repo-settings.json";
        $json_object = file_get_contents($path);
        $data = json_decode($json_object, true);

        $data['resources'][$resource]['lock'] = false;
        $data['resources'][$resource]['allowedSorts'] = [];
        $data['resources'][$resource]['allowedFilters'] = [];
        $data['resources'][$resource]['allowedIncludes'] = [];
        $data = json_encode($data, JSON_PRETTY_PRINT);
        file_put_contents($path, $data);
    }






    public function Request($name)
    {
        $this->createLimitRequest();
        $path = 'app/Http/Requests/'.$name;

        if (!is_dir($path)) {
            mkdir($path, 0777);
        }
        $create = $path. '/Create'. $name .'.php';
        $update = $path. '/Update'. $name .'.php';
        $this->createRequest($create, 'Create'. $name, $name);
        $this->createRequest($update, 'Update'. $name, $name);
    }


    public function createRequest($path, $name, $shortname)
    {
        $myfile = fopen($path, "w") or die("Unable to open file!");
        $file = file_get_contents($this->request);
        $text = "<?php\n";
        fwrite($myfile, $text);
        $result = str_replace('{{name}}', $name, $file);
        $result = str_replace('{{shortname}}', $shortname, $result);
        fwrite($myfile, $result);
        fclose($myfile);
    }


    public function createLimitRequest()
    {
        $path = 'app/Http/Requests/LimitRequest.php';
        $create = null;

        if(!is_dir('app/Http/Requests')) {
            mkdir('app/Http/Requests', 0777);
            if(!file_exists($path)) {
                $create = 'ok';
            }
        } else {
            if(!file_exists($path)) {
                $create = 'ok';
            }
        }

        if(!is_null($create)) {
            $myfile = fopen($path, "w") or die("Unable to open file!");
            $file = file_get_contents($this->limitRequest);
            $text = "<?php\n";
            fwrite($myfile, $text);
            fwrite($myfile, $file);
            fclose($myfile);
        }
    }





    

}

