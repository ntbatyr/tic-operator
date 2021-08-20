<?php

namespace Cmd;

class Migrate implements Command
{
    public function run($arguments): bool
    {
        $migrations = scandir(APP_ROOT .'/src/database/migrations');

        foreach ($migrations as $file) {
            $fileName = pathinfo($file)['filename'];
            $migrated = \Models\Migration::where('migration', $fileName)
                ->first();

            if (!empty($migrated))
                continue;

            include $file;

            $migrated = new \Models\Migration([
                'migration' => $fileName,
                'batch' => 1
            ]);
        }

        return true;
    }
}