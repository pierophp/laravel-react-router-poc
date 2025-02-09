<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;

class CompileCommand extends Command
{
    protected $signature = 'app:compile';

    protected $description = 'Compile front-end';

    public function handle()
    {
        $files = array_diff(scandir("app/Front"), array('.', '..'));
        $routesImports = [];
        $routesDefinitions = [];
        $routesReact = [];
        foreach($files as $filename)
        {
            $content = file_get_contents("app/Front/$filename");

            $uri = null;
            preg_match('/<route>(.*?)<\/route>/s', $content, $matches);
            if (!empty($matches[1])) {
                $route = json_decode(trim($matches[1]), true);
                $uri = $route['uri'];
            }

            if ($uri === null) {
                continue;
            }

            $routesReact[] = "\troute(\"" . $uri . "\", \"routes/test.tsx\")";

            $filenameWithoutExt = pathinfo($filename, PATHINFO_FILENAME);

            preg_match('/<php-loader>(.*?)<\/php-loader>/s', $content, $matches);

            if (!empty($matches[1])) {
                $phpCode = trim($matches[1]);

                $phpFilename = "app/Http/Controllers/Generated/{$filenameWithoutExt}LoaderController.php";

                $routesImports[] = "use App\Http\Controllers\Generated\\{$filenameWithoutExt}LoaderController;";
                $routesDefinitions[] = "Route::get('$uri', [{$filenameWithoutExt}LoaderController::class, 'index']);";


                file_put_contents($phpFilename, "<?php\n\nnamespace App\Http\Controllers\Generated;\n\nuse Illuminate\Http\Request;\nuse App\Http\Controllers\Controller;\n\nclass {$filenameWithoutExt}LoaderController extends Controller\n{\n    public function index()\n    {\n        $phpCode;\n    }\n}\n");
            }

            preg_match('/<php-action>(.*?)<\/php-action>/s', $content, $matches);

            if (!empty($matches[1])) {
                $phpCode = trim($matches[1]);

                $phpFilename = "app/Http/Controllers/Generated/{$filenameWithoutExt}ActionController.php";

                $routesImports[] = "use App\Http\Controllers\Generated\\{$filenameWithoutExt}ActionController;";
                $routesDefinitions[] = "Route::post('$uri', [{$filenameWithoutExt}ActionController::class, 'index']);";
                $routesDefinitions[] = "Route::delete('$uri', [{$filenameWithoutExt}ActionController::class, 'index']);";
                $routesDefinitions[] = "Route::put('$uri', [{$filenameWithoutExt}ActionController::class, 'index']);";
                $routesDefinitions[] = "Route::patch('$uri', [{$filenameWithoutExt}ActionController::class, 'index']);";

                file_put_contents($phpFilename, "<?php\n\nnamespace App\Http\Controllers\Generated;\n\nuse Illuminate\Http\Request;\nuse App\Http\Controllers\Controller;\n\nclass {$filenameWithoutExt}ActionController extends Controller\n{\n    public function index()\n    {\n        $phpCode;\n    }\n}\n");
            }

            preg_match('/<template>(.*?)<\/template>/s', $content, $matches);
            if (!empty($matches[1])) {
                $reactCode = trim($matches[1]);

                $reactFilename = "ui/app/routes/". strtolower($filenameWithoutExt) . ".tsx";
                $reactLoader = "export async function loader() {\nconst response = await fetch(\"http://127.0.0.1:8000" . $uri . "\");\nreturn await response.json();\n}";

                file_put_contents($reactFilename, $reactCode . "\n" . $reactLoader);
            }
        }

        file_put_contents("routes/web.php", "<?php\n\nuse Illuminate\Support\Facades\Route;\n" . implode("\n", $routesImports) . "\n\n" . implode("\n", $routesDefinitions) . "\n") ;
        file_put_contents("ui/app/routes.ts", "import { type RouteConfig, route } from \"@react-router/dev/routes\";\n\nexport default [\n" . implode(",\n", $routesReact) . "\n] satisfies RouteConfig;\n") ;

    }
}
