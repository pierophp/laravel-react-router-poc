<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Symfony\Component\Console\Input\InputOption;


class CompileCommand extends Command
{
    protected $signature = 'app:compile';

    protected $description = 'Compile front-end';

    public function handle()
    {
        $this->compile();
    }

    protected function compile()
    {
        $files = array_diff(scandir("app/Pages"), array('.', '..'));
        $routesImports = [];
        $routesDefinitions = [];
        $routesReact = [];
        foreach($files as $filename)
        {
            $content = file_get_contents("app/Pages/$filename");

            $uri = null;
            preg_match('/<route>(.*?)<\/route>/s', $content, $matches);
            if (!empty($matches[1])) {
                $route = json_decode(trim($matches[1]), true);
                $uri = $route['uri'];
            }

            if ($uri === null) {
                continue;
            }

            $filenameWithoutExt = pathinfo($filename, PATHINFO_FILENAME);

            $reactRouteName = str_replace("page", "", strtolower($filenameWithoutExt));

            $routesReact[] = "\troute(\"" . $uri . "\", \"routes/" . $reactRouteName . ".tsx\")";

            preg_match('/<php>(.*?)<\/php>/s', $content, $matches);

            if (!empty($matches[1])) {
                $phpCode = trim($matches[1]);

                $phpFilename = "app/Http/Controllers/Generated/{$filenameWithoutExt}Controller.php";

                $routesImports[] = "use App\Http\Controllers\Generated\\{$filenameWithoutExt}Controller;";
                $routesDefinitions[] = "Route::get('$uri', [{$filenameWithoutExt}Controller::class, 'loader']);";
                $routesDefinitions[] = "Route::post('$uri', [{$filenameWithoutExt}Controller::class, 'action']);";
                $routesDefinitions[] = "Route::delete('$uri', [{$filenameWithoutExt}Controller::class, 'action']);";
                $routesDefinitions[] = "Route::put('$uri', [{$filenameWithoutExt}Controller::class, 'action']);";
                $routesDefinitions[] = "Route::patch('$uri', [{$filenameWithoutExt}Controller::class, 'action']);";

                file_put_contents($phpFilename, "<?php\n\nnamespace App\Http\Controllers\Generated;\n\nuse Illuminate\Http\Request;\nuse App\Http\Controllers\Controller;\n\nclass {$filenameWithoutExt}Controller extends Controller\n{\n   $phpCode\n}\n");
            }

            preg_match('/<template>(.*?)<\/template>/s', $content, $matches);
            if (!empty($matches[1])) {
                $reactCode = trim($matches[1]);

                $reactFilename = "ui/app/routes/". $reactRouteName . ".tsx";
                $reactLoader = "export async function loader() {\nconst response = await fetch(\"http://127.0.0.1:8000" . $uri . "\");\nreturn await response.json();\n}";
                $reactAction = "export async function action() {\nconst response = await fetch(\"http://127.0.0.1:8000" . $uri . "\", {method:\"POST\"});\nreturn await response.json();\n}";

                file_put_contents($reactFilename, $reactCode . "\n" . $reactLoader . "\n" . $reactAction);
            }
        }

        file_put_contents("routes/web.php", "<?php\n\nuse Illuminate\Support\Facades\Route;\n" . implode("\n", $routesImports) . "\n\n" . implode("\n", $routesDefinitions) . "\n") ;
        file_put_contents("ui/app/routes.ts", "import { type RouteConfig, route } from \"@react-router/dev/routes\";\n\nexport default [\n" . implode(",\n", $routesReact) . "\n] satisfies RouteConfig;\n") ;

        if ($this->option('watch')) {
            sleep(1);
            $this->compile();
        }
    }

    protected function configure()
    {
        $this->addOption('watch', 'w', InputOption::VALUE_NONE, 'Watch mode');
    }
}
