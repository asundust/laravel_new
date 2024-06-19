<?php

namespace Slowlyo\OwlAdmin\Support\CodeGenerator;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Slowlyo\OwlAdmin\Traits\MakeTrait;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use Slowlyo\OwlAdmin\Models\AdminCodeGenerator;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class Generator
{
    use MakeTrait;

    public static array $dataTypeMap = [
        'int'                => 'integer',
        'int@unsigned'       => 'unsignedInteger',
        'tinyint'            => 'tinyInteger',
        'tinyint@unsigned'   => 'unsignedTinyInteger',
        'smallint'           => 'smallInteger',
        'smallint@unsigned'  => 'unsignedSmallInteger',
        'mediumint'          => 'mediumInteger',
        'mediumint@unsigned' => 'unsignedMediumInteger',
        'bigint'             => 'bigInteger',
        'bigint@unsigned'    => 'unsignedBigInteger',
        'date'               => 'date',
        'time'               => 'time',
        'datetime'           => 'dateTime',
        'timestamp'          => 'timestamp',
        'enum'               => 'enum',
        'json'               => 'json',
        'binary'             => 'binary',
        'float'              => 'float',
        'double'             => 'double',
        'decimal'            => 'decimal',
        'varchar'            => 'string',
        'char'               => 'char',
        'text'               => 'text',
        'mediumtext'         => 'mediumText',
        'longtext'           => 'longText',
    ];

    public function needCreateOptions()
    {
        return [
            [
                'label' => admin_trans('admin.code_generators.create_database_migration'),
                'value' => 'need_database_migration',
            ],
            [
                'label' => admin_trans('admin.code_generators.create_table'),
                'value' => 'need_create_table',
            ],
            [
                'label' => admin_trans('admin.code_generators.create_model'),
                'value' => 'need_model',
            ],
            [
                'label' => admin_trans('admin.code_generators.create_controller'),
                'value' => 'need_controller',
            ],
            [
                'label' => admin_trans('admin.code_generators.create_service'),
                'value' => 'need_service',
            ],
        ];
    }

    public function availableFieldTypes(): array
    {
        return collect(self::$dataTypeMap)
            ->values()
            ->map(fn($value) => ['label' => $value, 'value' => $value])
            ->toArray();
    }

    public function getDatabaseColumns($db = null, $tb = null)
    {
        $databases = Arr::where(config('database.connections', []), function ($value) {
            $supports = ['mysql'];

            return in_array(strtolower(Arr::get($value, 'driver')), $supports);
        });

        $data = [];

        try {
            foreach ($databases as $connectName => $value) {
                if ($db && $db != $value['database']) continue;

                $sql = sprintf('SELECT * FROM information_schema.columns WHERE table_schema = "%s"',
                    $value['database']);

                if ($tb) {
                    $p = Arr::get($value, 'prefix');

                    $sql .= " AND TABLE_NAME = '{$p}{$tb}'";
                }

                $sql .= ' ORDER BY `ORDINAL_POSITION` ASC';

                $tmp = DB::connection($connectName)->select($sql);

                $collection = collect($tmp)->map(function ($v) use ($value) {
                    if (!$p = Arr::get($value, 'prefix')) {
                        return (array)$v;
                    }
                    $v = (array)$v;

                    $v['TABLE_NAME'] = Str::replaceFirst($p, '', $v['TABLE_NAME']);

                    return $v;
                });

                $data[$value['database']] = $collection->groupBy('TABLE_NAME')->map(function ($v) {
                    return collect($v)
                        ->keyBy('COLUMN_NAME')
                        ->where('COLUMN_KEY', '<>', 'PRI')
                        ->whereNotIn('COLUMN_NAME', ['created_at', 'updated_at', 'deleted_at'])
                        ->map(function ($v) {
                            $v['COLUMN_TYPE'] = strtolower($v['COLUMN_TYPE']);
                            $v['DATA_TYPE']   = strtolower($v['DATA_TYPE']);

                            if (Str::contains($v['COLUMN_TYPE'], 'unsigned')) {
                                $v['DATA_TYPE'] .= '@unsigned';
                            }


                            return [
                                'name'     => $v['COLUMN_NAME'],
                                'type'     => Arr::get(Generator::$dataTypeMap, $v['DATA_TYPE'], 'string'),
                                'default'  => $v['COLUMN_DEFAULT'],
                                'nullable' => $v['IS_NULLABLE'] == 'YES',
                                'comment'  => $v['COLUMN_COMMENT'],
                            ];
                        })
                        ->values();
                });
            }
        } catch (\Throwable $e) {
        }

        return collect($data);
    }

    public function getDatabasePrimaryKeys($db = null, $tb = null)
    {
        $databases = Arr::where(config('database.connections', []), function ($value) {
            $supports = ['mysql'];

            return in_array(strtolower(Arr::get($value, 'driver')), $supports);
        });

        $data = [];

        try {
            foreach ($databases as $connectName => $value) {
                if ($db && $db != $value['database']) continue;

                $sql = sprintf('SELECT * FROM information_schema.columns WHERE table_schema = "%s"',
                    $value['database']);

                if ($tb) {
                    $p = Arr::get($value, 'prefix');

                    $sql .= " AND TABLE_NAME = '{$p}{$tb}'";
                }

                $tmp = DB::connection($connectName)->select($sql);

                $collection = collect($tmp)->map(function ($v) use ($value) {
                    if (!$p = Arr::get($value, 'prefix')) {
                        return (array)$v;
                    }
                    $v = (array)$v;

                    $v['TABLE_NAME'] = Str::replaceFirst($p, '', $v['TABLE_NAME']);

                    return $v;
                });

                $data[$value['database']] = $collection->groupBy('TABLE_NAME')->map(function ($v) {
                    return collect($v)
                        ->keyBy('COLUMN_NAME')
                        ->where('COLUMN_KEY', 'PRI')
                        ->whereNotIn('COLUMN_NAME', ['created_at', 'updated_at', 'deleted_at'])
                        ->map(fn($v) => $v['COLUMN_NAME'])
                        ->values()
                        ->first();
                });
            }
        } catch (\Throwable $e) {
        }

        return collect($data);
    }

    public function generate($id, $needs = [])
    {
        $record = AdminCodeGenerator::find($id);
        $model  = AdminCodeGenerator::find($id);
        $needs  = collect(filled($needs) ? $needs : $record->needs);

        $successMessage = fn($type, $path) => "<b class='text-success'>{$type} generated successfully!</b><br>{$path}<br><br>";

        $paths   = [];
        $message = '';
        try {
            // Model
            if ($needs->contains('need_model')) {
                $path = ModelGenerator::make($model)->generate();

                $message .= $successMessage('Model', $path);
                $paths[] = $path;
            }

            // Controller
            if ($needs->contains('need_controller')) {
                $path = ControllerGenerator::make($record)->generate();

                $message .= $successMessage('Controller', $path);
                $paths[] = $path;
            }

            // Service
            if ($needs->contains('need_service')) {
                $path = ServiceGenerator::make($record)->generate();

                $message .= $successMessage('Service', $path);
                $paths[] = $path;
            }

            // Route
            RouteGenerator::handle($record->menu_info);

            // Migration
            $migratePath = '';
            if ($needs->contains('need_database_migration')) {
                $path = MigrationGenerator::make($record)->generate();

                $message     .= $successMessage('Migration', $path);
                $migratePath = str_replace(base_path(), '', $path);
                $paths[]     = $path;
            }

            // 创建数据库表
            if ($needs->contains('need_create_table')) {
                if (Schema::hasTable($record->table_name)) {
                    abort(HttpResponse::HTTP_BAD_REQUEST, "Table [{$record->table_name}] already exists!");
                }

                if ($migratePath) {
                    Artisan::call('migrate', ['--path' => $migratePath]);
                } else {
                    Artisan::call('migrate');
                }
                $message .= $successMessage('Table', Artisan::output());
            }
        } catch (\Throwable $e) {
            app('files')->delete($paths);

            RouteGenerator::refresh();

            admin_abort($e->getMessage());
        }

        return $message;
    }

    public function preview($id)
    {
        $record = AdminCodeGenerator::find($id);

        try {
            // Model
            $model = ModelGenerator::make($record)->preview();
            // Migration
            $migration = MigrationGenerator::make($record)->preview();
            // Controller
            $controller = ControllerGenerator::make($record)->preview();
            // Service
            $service = ServiceGenerator::make($record)->preview();
        } catch (\Exception $e) {
            admin_abort($e->getMessage());
        }

        return compact('model', 'migration', 'controller', 'service');
    }
}
