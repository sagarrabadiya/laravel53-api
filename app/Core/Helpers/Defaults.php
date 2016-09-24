<?php
/**
 * Author: sagar <sam.coolone70@gmail.com>
 *
 */

namespace App\Core\Helpers;

abstract class Defaults
{

    const PROJECTCOLOR = "#4285F4";

    /**
     * returns default permissions names
     * @return \Illuminate\Support\Collection
     */
    public static function permissionNames()
    {
        return collect([
            'board_read',
            'board_write',
            'milestone_read',
            'milestone_write',
            'ticket_read',
            'ticket_write',
            'note_read',
            'note_write',
            'team_read',
            'team_write'
        ]);
    }

    /**
     * returns defaults for company settings
     * @return \Illuminate\Support\Collection
     */
    public static function companySettings()
    {
        return collect([]);
    }

    /**
     * returns defaults for project settings
     * @return \Illuminate\Support\Collection
     */
    public static function projectSettings()
    {
        return collect([
            'logo' => 'https://placehold.it/200x200'
        ]);
    }

    /**
     * returns defaults for project member settings
     * @return \Illuminate\Support\Collection
     */
    public static function projectMemberSettings()
    {
        return collect([]);
    }

    /**
     * function to generate admin permissions
     * @return array
     */
    public static function getAdminPermissions()
    {
        $permissions = [];
        static::permissionNames()->each(function ($permission) use ($permissions) {
            $permissions[$permission] = 1;
        });
        $permissions['settings'] = json_encode(Defaults::memberDefaultSettings());
        return $permissions;
    }

    /**
     * function to get default settings of user in team
     * @return array
     */
    public static function memberDefaultSettings()
    {
        return [
            'color' => self::PROJECTCOLOR,
            'emails' => [
                'board_update' => true,
                'board_create' => true,
                'ticket_create' => true,
                'ticket_update' => true,
                'ticket_assigned' => true,
                'ticket_status' => true,
                'milestone_create' => true,
                'milestone_update' => true,
                'note_create' => true,
                'note_update' => true,
                'note_page_create' => true,
                'note_page_update' => true
            ]
        ];
    }

    /**
     * function to merge settings recurssively
     * @param $target
     * @param $input
     * @return array
     */
    public static function mergeSettings($target, $input)
    {
        $target = array_dot($target);
        $input = array_dot($input);
        $mergedDotData = array_merge($target, $input);
        $output = [];
        unset($target, $input);
        foreach ($mergedDotData as $key => $value) {
            array_set($output, $key, $value);
        }
        return $output;
    }
}
