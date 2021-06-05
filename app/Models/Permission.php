<?php

namespace App\Models;

use Spatie\Activitylog\Traits\LogsActivity;

class Permission extends \Spatie\Permission\Models\Permission
{
    use LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'guard_name',
    ];

    /**
     * The attibutes for logging the event change
     *
     * @var array
     */
    protected static $logAttributes = ['name', 'guard_name'];

    /**
     * Logging name
     *
     * @var string
     */
    protected static $logName = 'permission';

    /**
     * Logging only the changed attributes
     *
     * @var boolean
     */
    protected static $logOnlyDirty = true;

    /**
     * Prevent save logs items that have no changed attribute
     *
     * @var boolean
     */
    protected static $submitEmptyLogs = false;

    /**
     * Custom logging description
     *
     * @param string $eventName
     * @return string
     */
    public function getDescriptionForEvent(string $eventName): string
    {
        return "Data has been {$eventName}";
    }

    public static function defaultPermissions()
    {
        return [
            'view_users',
            'add_users',
            'edit_users',
            'delete_users',

            'view_roles',
            'add_roles',
            'edit_roles',
            'delete_roles',

            'view_permissions',
            'add_permissions',
            'edit_permissions',
            'delete_permissions',

            'view_logs',
        ];
    }

    public static function additionalPermission()
    {
        return [
            'add_pelapor',
            'view_pelapor',
            'edit_pelapor',
            'delete_pelapor',

            'add_pelanggaran',
            'view_pelanggaran',
            'edit_pelanggaran',
            'delete_pelanggaran',

            'add_operator_type',
            'view_operator_type',
            'edit_operator_type',
            'delete_operator_type',

            'add_jenis_laporan',
            'view_jenis_laporan',
            'edit_jenis_laporan',
            'delete_jenis_laporan',

            'add_jenis_apresiasi',
            'view_jenis_apresiasi',
            'edit_jenis_apresiasi',
            'delete_jenis_apresiasi',

            'add_kota',
            'view_kota',
            'edit_kota',
            'delete_kota',

            'add_province',
            'view_province',
            'edit_province',
            'delete_province',

            'add_laporan',
            'view_laporan',
            'edit_laporan',
            'delete_laporan',

            'add_operator',
            'view_operator',
            'edit_operator',
            'delete_operator',

            'add_external_link',
            'view_external_link',
            'edit_external_link',
            'delete_external_link',

            'add_chart',
            'view_chart',
            'edit_chart',
            'delete_chart'
        ];
    }
}
