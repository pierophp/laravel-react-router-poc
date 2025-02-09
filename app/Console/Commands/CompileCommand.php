<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Symfony\Component\Console\Input\InputOption;


class CompileCommand extends Command
{
    protected $signature = 'app:compile';

    protected $description = 'Compile front-end';

    protected $watchFiles = [];

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

        $hasChanged = false;

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

            $phpChanged = false;

            if (!empty($matches[1])) {
                $phpCode = trim($matches[1]);

                $phpFilename = "app/Http/Controllers/Generated/{$filenameWithoutExt}Controller.php";

                $routesImports[] = "use App\Http\Controllers\Generated\\{$filenameWithoutExt}Controller;";
                $routesDefinitions[] = "Route::get('$uri', [{$filenameWithoutExt}Controller::class, 'loader']);";
                $routesDefinitions[] = "Route::post('$uri', [{$filenameWithoutExt}Controller::class, 'action']);";
                $routesDefinitions[] = "Route::delete('$uri', [{$filenameWithoutExt}Controller::class, 'action']);";
                $routesDefinitions[] = "Route::put('$uri', [{$filenameWithoutExt}Controller::class, 'action']);";
                $routesDefinitions[] = "Route::patch('$uri', [{$filenameWithoutExt}Controller::class, 'action']);";

                $phpMd5 = md5($phpCode);
                if (empty($this->watchFiles[$phpFilename]) || $this->watchFiles[$phpFilename] !== $phpMd5) {
                    \Log::info($phpFilename . " has changed.");
                    $phpChanged = true;
                    $hasChanged = true;
                    $this->watchFiles[$phpFilename] = $phpMd5;
                    file_put_contents($phpFilename, "<?php\n\nnamespace App\Http\Controllers\Generated;\n\nuse Illuminate\Http\Request;\nuse App\Http\Controllers\Controller;\n\nclass {$filenameWithoutExt}Controller extends Controller\n{\n   $phpCode\n}\n");
                }
            }

            preg_match('/<template>(.*?)<\/template>/s', $content, $matches);
            if (!empty($matches[1])) {
                $reactCode = trim($matches[1]);

                $reactFilename = "ui/app/routes/". $reactRouteName . ".tsx";
                $reactLoader = "export async function " . ($this->option('spa') ? "clientLoader" : "loader") . "() {\nconst response = await fetch(\"http://127.0.0.1:8000/api" . $uri . "\");\nreturn await response.json();\n}";
                $reactAction = "export async function " . ($this->option('spa') ? "clientAction" : "action") . "({ request }) {\nconst formData = await request.formData();\nconst response = await fetch(\"http://127.0.0.1:8000/api" . $uri . "\", {\nmethod:\"POST\",\nbody: formData});\nreturn await response.json();\n}";

                $reactMd5 = md5($reactCode);
                if ($phpChanged || empty($this->watchFiles[$reactFilename]) || $this->watchFiles[$reactFilename] !== $reactMd5) {
                    $hasChanged = true;
                    \Log::info($reactFilename . " has changed.");
                    $this->watchFiles[$reactFilename] = $reactMd5;
                    file_put_contents($reactFilename, $reactCode . "\n" . $reactLoader . "\n" . $reactAction);
                }
            }
        }

        if ($hasChanged)
        {
            file_put_contents("routes/api.php", "<?php\n\nuse Illuminate\Support\Facades\Route;\n" . implode("\n", $routesImports) . "\n\n" . implode("\n", $routesDefinitions) . "\n") ;
            file_put_contents("ui/app/routes.ts", "import { type RouteConfig, route } from \"@react-router/dev/routes\";\n\nexport default [\n" . implode(",\n", $routesReact) . "\n] satisfies RouteConfig;\n") ;
            file_put_contents("ui/react-router.config.ts", "import type { Config } from \"@react-router/dev/config\";\n\nexport default {\n\tssr: " . ($this->option('spa') ? "false" : "true") . ",\n} satisfies Config;\n") ;
        }

        if ($this->option('watch')) {
            sleep(1);
            $this->compile();
        }
    }

    protected function configure()
    {
        $this->addOption('watch', 'w', InputOption::VALUE_NONE, 'Watch mode');
        $this->addOption('spa', 's', InputOption::VALUE_NONE, 'SPA mode');
    }
}
