<?php

namespace Backpack\Generators\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class CrudBackpackCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backpack:crud {name} {folder?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a CRUD interface: Controller, Model, Request';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $name = ucfirst($this->argument('name'));
        $lowerName = strtolower($this->argument('name'));
        $pluralName = Str::plural($name);

        $argumentsArray = ['name' => $name];
        $crudControllerName = "{$name}CrudController";
        if (strlen($this->argument('folder')) > 1) {
            $folderName = ucfirst($this->argument('folder'));
            $argumentsArray['folder'] = $folderName;
            $crudControllerName = "$folderName\\".$name."CrudController";
        }
        // Create the CRUD Controller and show output
        $this->call('backpack:crud-controller', $argumentsArray);

        // Create the CRUD Model and show output
        $this->call('backpack:crud-model', $argumentsArray);

        // Create the CRUD Request and show output
        $this->call('backpack:crud-request', $argumentsArray);

        // Create the CRUD route
        $this->call('backpack:add-custom-route', [
            'code' => "Route::crud('$lowerName', '$crudControllerName');",
        ]);

        // Create the sidebar item
        $this->call('backpack:add-sidebar-content', [
            'code' => "<li class='nav-item'><a class='nav-link' href='{{ backpack_url('$lowerName') }}'><i class='nav-icon la la-question'></i> $pluralName</a></li>",
        ]);
    }
}
