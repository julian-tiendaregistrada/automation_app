<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySubcategoryCsvSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Wrap both seed operations in a transaction for safety and consistency
        DB::transaction(function () {
            $this->seedCsv('categories.csv', 'categories', ['id', 'name', 'code_line']);
            $this->seedCsv('subcategories.csv', 'subcategories', ['id', 'name', 'category_id']);
        });
    }

    protected function seedCsv(string $filename, string $table, array $columns): void
    {
        $path = database_path("seeders/data/{$filename}");
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        // Remove UTF-8 BOM from the first line if it exists
        if (! empty($lines)) {
            $lines[0] = preg_replace('/^\xEF\xBB\xBF/', '', $lines[0]);
        }

        $batch = [];

        foreach ($lines as $line) {
            $row = str_getcsv($line, ';'); // Parse line with semicolon delimiter

            // Skip malformed lines
            if (count($row) !== count($columns)) {
                continue;
            }

            // Combine CSV values with column names
            $batch[] = array_merge(
                array_combine($columns, $row),
                ['created_at' => now(), 'updated_at' => now()]
            );

            // Insert in batches of 200 for performance
            if (count($batch) === 200) {
                DB::table($table)->insert($batch);
                $batch = [];
            }
        }

        // Insert any remaining rows
        if (! empty($batch)) {
            DB::table($table)->insert($batch);
        }
    }
}
