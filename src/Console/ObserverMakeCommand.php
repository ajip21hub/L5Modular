<?php

namespace ArtemSchander\L5Modular\Console;

use Illuminate\Foundation\Console\ObserverMakeCommand as BaseObserverMakeCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class ObserverMakeCommand extends BaseObserverMakeCommand
{
    /**
     * Execute the console command.
     *
     * @return bool|null
     */
    public function handle()
    {
        $name = $this->qualifyClass($this->getNameInput());
        $path = $this->getPath($name);

        if ($this->hasOption('module') && !$this->files->isDirectory(dirname($path, 2))) {
            $this->error('Module doesn\'t exist.');
            
            return false;
        }

        parent::handle();
    }
    
    /**
     * Replace the model for the given stub.
     *
     * @param  string  $stub
     * @param  string  $model
     * @return string
     */
    protected function replaceModel($stub, $model)
    {
        $model = str_replace('/', '\\', $model);

        if ($this->hasOption('module')) {
            $namespaceModel = $this->laravel->getNamespace().'\Modules\\'.Str::studly($this->option('module')).'\\'.$model;
        } else {
            $namespaceModel = $this->laravel->getNamespace().$model;
        }

        if (Str::startsWith($model, '\\')) {
            $stub = str_replace('NamespacedDummyModel', trim($model, '\\'), $stub);
        } else {
            $stub = str_replace('NamespacedDummyModel', $namespaceModel, $stub);
        }

        $stub = str_replace(
            "use {$namespaceModel};\nuse {$namespaceModel};", "use {$namespaceModel};", $stub
        );

        $model = class_basename(trim($model, '\\'));

        $stub = str_replace('DocDummyModel', Str::snake($model, ' '), $stub);

        $stub = str_replace('DummyModel', $model, $stub);

        return str_replace('dummyModel', Str::camel($model), $stub);
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        if ($this->hasOption('module')) {
            return $rootNamespace.'\Modules\\'.Str::studly($this->option('module')).'\Observers';
        } else {
            return parent::getDefaultNamespace($rootNamespace);   
        }
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        $options = parent::getOptions();

        $options[] = ['module', null, InputOption::VALUE_OPTIONAL, 'Generate an observer in a certain module'];

        return $options;
    }
}
