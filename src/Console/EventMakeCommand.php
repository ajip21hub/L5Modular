<?php

namespace ArtemSchander\L5Modular\Console;

use Illuminate\Foundation\Console\EventMakeCommand as BaseEventMakeCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class EventMakeCommand extends BaseEventMakeCommand
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
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        if ($this->hasOption('module')) {
            return $rootNamespace.'\Modules\\'.Str::studly($this->option('module')).'\Events';
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

        $options[] = ['module', null, InputOption::VALUE_OPTIONAL, 'Generate an event in a certain module'];

        return $options;
    }
}
