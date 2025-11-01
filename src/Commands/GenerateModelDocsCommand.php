<?php

namespace Rakib01\LaravelModelDocsMd\Commands;

use Illuminate\Console\Command;
use Rakib01\LaravelModelDocsMd\Helpers\ModelInspector;
use Illuminate\Support\Facades\File;

class GenerateModelDocsCommand extends Command
{
    protected $signature = 'model-docs-md:generate';
    protected $description = 'Generate Markdown documentation for all Eloquent models';

    public function handle()
    {
        $outputPath = config('modeldocsmd.output_path');
        $modelPaths = config('modeldocsmd.model_paths');
        $inspector = new ModelInspector();

        $markdown = "# 📘 Laravel Model Documentation\n\n";

        foreach ($modelPaths as $path) {
            foreach (File::allFiles($path) as $file) {
                $namespace = app()->getNamespace() . 'Models\\';
                $class = $namespace . str_replace(['/', '.php'], ['\\', ''], $file->getRelativePathname());

                if (!class_exists($class)) continue;
                if (!is_subclass_of($class, \Illuminate\Database\Eloquent\Model::class)) continue;

                $markdown .= $inspector->analyze($class) . "\n\n";
            }
        }

        File::put($outputPath, $markdown);
        $this->info("✅ Model documentation generated at: {$outputPath}");
    }
}
