<?php

namespace Rakib01\LaravelModelDocsMd\Helpers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class ModelInspector
{
    public function analyze(string $class): string
    {
        /** @var Model $model */
        $model = new $class;

        $table = $model->getTable();
        $columns = Schema::hasTable($table) ? Schema::getColumnListing($table) : [];
        $casts = $model->getCasts();
        $fillable = $model->getFillable();
        $hidden = $model->getHidden();
        $appends = $model->getAppends();

        $markdown = "## 🧩 {$class}\n";
        $markdown .= "**Table:** `{$table}`\n\n";

        if (count($columns)) {
            $markdown .= "**Columns:**\n\n";
            $markdown .= "| Name | Type | Cast |\n|------|------|------|\n";

            foreach ($columns as $column) {
                $type = Schema::getColumnType($table, $column);
                $cast = $casts[$column] ?? '-';
                $markdown .= "| {$column} | {$type} | {$cast} |\n";
            }
            $markdown .= "\n";
        } else {
            $markdown .= "_⚠️ Table not found in database_\n\n";
        }

        // Fillable, Hidden, Appends
        $markdown .= "**Fillable:** " . (count($fillable) ? implode(', ', $fillable) : '_None_') . "\n\n";
        $markdown .= "**Hidden:** " . (count($hidden) ? implode(', ', $hidden) : '_None_') . "\n\n";
        $markdown .= "**Appends:** " . (count($appends) ? implode(', ', $appends) : '_None_') . "\n\n";

        // Relationships
        $markdown .= "**Relationships:**\n\n";
        $markdown .= $this->getRelationships($model);

        return $markdown;
    }

    protected function getRelationships(Model $model): string
    {
        $markdown = '';
        $reflection = new \ReflectionClass($model);

        foreach ($reflection->getMethods() as $method) {
            if ($method->class !== get_class($model)) continue;
            if ($method->getNumberOfParameters() > 0) continue;

            try {
                $result = $method->invoke($model);
                if ($result instanceof \Illuminate\Database\Eloquent\Relations\Relation) {
                    $markdown .= "- **{$method->getName()}** → " . class_basename($result->getRelated()) . "\n";
                }
            } catch (\Throwable $e) {
                // Skip invalid methods
            }
        }

        return $markdown ?: "_No relationships found._\n";
    }
}
