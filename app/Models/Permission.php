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

            'add_operator-type',
            'view_operator-type',
            'edit_operator-type',
            'delete_operator-type',

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

            'add_external-link',
            'view_external-link',
            'edit_external-link',
            'delete_external-link',

            'add_chart',
            'view_chart',
            'edit_chart',
            'delete_chart',

            'add_banner',
            'view_banner',
            'edit_banner',
            'delete_banner',

            'add_bentuk-pelanggaran',
            'view_bentuk-pelanggaran',
            'edit_bentuk-pelanggaran',
            'delete_bentuk-pelanggaran',

            'add_kawasan',
            'view_kawasan',
            'edit_kawasan',
            'delete_kawasan',

            'add_static-page',
            'view_static-page',
            'edit_static-page',
            'delete_static-page',

            'add_instagram',
            'view_instagram',
            'edit_instagram',
            'delete_instagram',

            'add_website',
            'view_website',
            'edit_website',
            'delete_website',
        ];
    }
}
