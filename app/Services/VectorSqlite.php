<?php

namespace App\Services;

use Illuminate\Database\SQLiteConnection;

class VectorSqlite extends SQLiteConnection
{
    public function setPdo($pdo)
    {
        parent::setPdo($pdo);

        $pluginPath = database_path(config('database.sqlite-vector-plugin'));

        $this->getPdo()->exec("PRAGMA load_extension('{$pluginPath}');");
    }
}
