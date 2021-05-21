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
        $file = $path.'/'.$name.'Repository.php';


        if(!is_dir($path)) {
            mkdir($path, 0777);
        }

        if(!$this->option('force') and file_exists($file)) {
            return $this->comment($name.'Repository exists. Use --force to override');
        }

        if($this->option('model')) {
            $model = $this->option('model');
        } else if($this->option('m')) {
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

        return $this->comment($name.' ('.$data.') Created.');
    }


public function Repo($name, $file)
{
$myfile = fopen($file, "w") or die("Unable to open file!");
$txt = "<?php
namespace App\Http\Repositories;
use g4t\Pattern\Repositories\BaseRepository;

class {$name}Repository extends BaseRepository {

}
";
fwrite($myfile, $txt);
fclose($myfile);
}



public function Controller($name, $model)
{
if(!$model) {
    $model = "ModelHere";
}
$const = '$this->'.$name.'Repository';
$index = '$this->'.$name.'Repository->index';
$show = '$this->'.$name.'Repository->show';
$store = '$this->'.$name.'Repository->store';
$update = '$this->'.$name.'Repository->update';
$destroy = '$this->'.$name.'Repository->destroy';
$path = 'app\Http\Controllers\\'.$name.'Controller.php';
$myfile = fopen($path, "w") or die("Unable to open file!");
$txt = "<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Repositories\\".$name."Repository;
use App\\".$model.";
class ".$name."Controller extends Controller
{
    private $".$name."Repository;
    public function __construct()
    {
        $const = new ".$name."Repository(new ".$model."());
    }



    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request ##request)
    {
        ##request->validate([
            'take' => 'integer',
            'skip' => 'integer',
        ]);
        if(##request->has('where')) {
            ##where = json_decode(##request->where, true);
        } else {
            ##where = null;
        }

        return $index(##request->take, ##request->skip, ##where);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  ##request
     * @return \Illuminate\Http\Response
     */
    public function store(Request ##request)
    {
        return $store(##request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\\$name  ##$name
     * @return \Illuminate\Http\Response
     */
    public function show(##id)
    {
        return $show(##id);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  ##request
     * @param  \App\\$name  ##$name
     * @return \Illuminate\Http\Response
     */
    public function update(Request ##request, ##id)
    {
        return $update(##id, ##request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\\$name  ##$name
     * @return \Illuminate\Http\Response
     */
    public function destroy(##id)
    {
        return $destroy(##id);
    }

}
";
$result = str_replace("##","$",$txt);

fwrite($myfile, $result);
fclose($myfile);
}



public function model($name)
{
$path = 'app/'.$name.'.php';
$myfile = fopen($path, "w") or die("Unable to open file!");
$txt = "<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class $name extends Model
{

}
";
fwrite($myfile, $txt);
fclose($myfile);
}



public function route($name)
{
    $controller = $name.'Controller';
    $file = "routes/api.php";
    $fc = fopen($file, "r");
    while (!feof($fc)) {
        $buffer = fgets($fc, 4096);
        $lines[] = $buffer;
    }
    fclose($fc);
    $f = fopen($file, "w") or die("couldn't open $file");
    $lineCount = count($lines);
    for ($i = 0; $i < $lineCount- 1; $i++) {
        fwrite($f, $lines[$i]);
    }
    
    $laravel = app();
    $version = $laravel::VERSION;
    if($version >= 8) {
        $route_8 = 'App\Http\Controllers\\'.$controller.'::class';
        fwrite($f, "Route::apiResource('".strtolower($name)."', $route_8);".PHP_EOL);
    } else {
        fwrite($f, "Route::apiResource('".strtolower($name)."', '$controller');".PHP_EOL);
    }
    fwrite($f, $lines[$lineCount-1]);
    fclose($f);    
}

public function jsonapi($name)
{
    $filename = config_path('jsonapi.php');
    $lines = file( $filename , FILE_IGNORE_NEW_LINES );
    $line = array_search('//dont-remove-or-edit-this-line',$lines,true);
    $lines[$line-1] = "        '".strtolower($name)."' => [\n            'allowedSorts' => ['column'],\n            'allowedFilters' => ['column'],\n            'allowedIncludes' => [''],\n        ],\n\n\n";
    file_put_contents( $filename , implode( "\n", $lines ) );
}


}
