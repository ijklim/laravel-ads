<?php

namespace App\Http\Traits;

use Illuminate\Support\Str;

trait SeederTrait
{
    /**
     * Extract table name from seeder class name
     */
    private function getTableName()
    {
        // Remove 'Seeder' from back of class name, add 's' to the end
        $className = class_basename(__CLASS__);
        return Str::plural(Str::snake(substr($className, 0, strlen($className) - strlen('Seeder'))));
    }

    /**
     * Delete all data from a table
     */
    private function deleteTable($tableName = null)
    {
        $tableName = $tableName ?? $this->getTableName();
        \DB::delete("DELETE FROM " . $tableName);
        // \DB::statement("ALTER TABLE " . $tableName . " AUTO_INCREMENT = 1;");
    }

    /**
     * Display a message to indicate what seeder is running
     */
    private function start()
    {
        $this->command->getOutput()->writeln("<comment>=== " . class_basename(__CLASS__) . " ===</comment>");
    }
}
