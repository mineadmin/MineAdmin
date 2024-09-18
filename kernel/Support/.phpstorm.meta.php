<?php
namespace Hyperf\Database\Schema {
    class Blueprint
    {
        public function authorBy(string $created_by = 'created_by', string $updated_by = 'updated_by'): void{}
    }

}