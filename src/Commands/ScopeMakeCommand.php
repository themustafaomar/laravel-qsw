<?php

namespace QueryWatcher\Commands;

use Illuminate\Support\Str;
use InvalidArgumentException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ScopeMakeCommand extends Command
{
    /**
     * The name and signature of the scope command.
     *
     * @var string
     */
    protected $signature = 'make:scope {name}';

    /**
     * The scope command description.
     *
     * @var string
     */
    protected $description = 'Create a scope for a query string parameter.';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Scope';

    /**
     * Execute the console command.
     *
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    public function handle()
    {
        $name = $this->qualifyClass($this->getNameInput());
        $path = $this->getPath($name);

        if (file_exists($path)) {
            $this->error($this->type.' already exists!');

            return false;
        }

        $this->makeDirectory($path);
        $this->exportScope($path);

        $this->info('Scope generated successfully.');
    }

    /**
     * Parse the class name and format according to the root namespace.
     *
     * @param  string  $name
     * @return string
     */
    protected function qualifyClass($name)
    {
        $name = ltrim($name, '\\/');

        $name = str_replace('/', '\\', $name);

        $rootNamespace = $this->rootNamespace();

        if (Str::startsWith($name, $rootNamespace)) {
            return $name;
        }

        return $this->qualifyClass(
            $this->getDefaultNamespace(trim($rootNamespace, '\\')).'\\'.$name
        );
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return is_dir(app_path('Scopes')) ? $rootNamespace.'\\Scopes' : $rootNamespace;
    }

    /**
     * Get the root namespace for the class.
     *
     * @return string
     */
    protected function rootNamespace()
    {
        return $this->laravel->getNamespace();
    }

    /**
     * Get the desired class name from the input.
     *
     * @return string
     */
    protected function getNameInput()
    {
        return trim($this->argument('name'));
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name)
    {
        $name = Str::replaceFirst($this->rootNamespace(), '', $name);

        return $this->laravel['path'].'/'.str_replace('\\', '/', $name).'.php';
    }

    /**
     * Build the directory for the class if necessary.
     *
     * @param  string  $path
     * @return string
     */
    protected function makeDirectory($path)
    {
        if (! File::isDirectory(dirname($path))) {
            File::makeDirectory(dirname($path), 0777, true, true);
        }

        return $path;
    }

    /**
     * Get the full namespace for a given class, without the class name.
     *
     * @param  string  $name
     * @return string
     */
    protected function getNamespace($name)
    {
        return trim(implode('\\', array_slice(explode('\\', $name), 0, -1)), '\\');
    }
    /**
     * Compiles the "HomeController" stub.
     *
     * @return string
     */
    protected function compileScopeStub()
    {
        $name = $this->getNameInput();
        $class = str_replace($this->getNamespace($name).'\\', '', $name);

        return str_replace(
            ['{{namespace}}', '{{scope}}'],
            [rtrim('App\Scopes\\'.$this->getNamespace($name), '\\'), $class],
            file_get_contents(__DIR__.'/../../stubs/ScopeTemplate.stub')
        );
    }

    protected function exportScope($path)
    {
        File::put($path, $this->compileScopeStub());
    }
}
