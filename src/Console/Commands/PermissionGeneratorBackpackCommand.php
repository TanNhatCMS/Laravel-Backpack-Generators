<?php

namespace Backpack\Generators\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;

class PermissionGeneratorBackpackCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
     protected $signature = 'backpack:permissions {route : Name of the route e.g admin/teams} {--P|permission=* : A permission you wish to add, can accept multiple instances of -P|permission}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Will generate permissions for routes and save to the database';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $routeName = $this->argument('route');
        $permissions = ['list', 'create', 'update', 'reorder', 'delete'];
        $permissionsAdded = 0;
        $customPermissions = $this->option('permission');

        if (count($customPermissions) > 0) {
            $permissions = $customPermissions;
        }

        $bar = $this->output->createProgressBar(count($permissions));

        foreach ($permissions as $permission) {

            $permissionName = trim($routeName, '/').'/'.trim($permission, '/');
            $permissionName = strtolower($permissionName);

            $existingPermission = Permission::where(['name' => $permissionName])->first();

            if ($existingPermission) {
                $continueAdding = $this->confirm("$permissionName already exists, do you want to carry on? [y|N]");

                if ($continueAdding) {
                    $bar->setMessage("Skipping $permissionName");
                    $bar->advance();
                    continue;
                } else {
                    $bar->finish();

                    return $this->error("\nCancelled adding any more permissions.");
                }
            } else {
                $newPermission = Permission::create(['name' => $permissionName]);
                $newPermission->save();

                if ($newPermission->id) {
                    $permissionsAdded++;
                    $bar->setMessage("$newPermission->name added with ID: $newPermission->id");
                    $bar->advance();
                } else {
                    $bar->setMessage("$permissionName could not be created.");
                    $bar->advance();
                }
            }
        }

        $bar->finish();
        $this->info("\nFinished adding $permissionsAdded new permissions");
    }
}
