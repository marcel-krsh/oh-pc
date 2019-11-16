<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Program extends Model
{
    public $timestamps = true;
    //protected $dateFormat = 'Y-m-d\TH:i:s.u';

    protected $guarded = ['id'];

    /*
    // 1 - FAF || NSP || TCE || RTCAP || 811 units
    $program_bundle_ids = explode(',', SystemSetting::get('program_bundle'));

    // 2 - 811 units
    $program_811_ids = explode(',', SystemSetting::get('program_811'));

    // 3 - Medicaid units
    $program_medicaid_ids = explode(',', SystemSetting::get('program_medicaid'));

    // 4 - HOME
    $program_home_ids = explode(',', SystemSetting::get('program_home'));

    // 5 - OHTF
    $program_ohtf_ids = explode(',', SystemSetting::get('program_ohtf'));

    // 6 - NHTF
    $program_nhtf_ids = explode(',', SystemSetting::get('program_nhtf'));

    // 7 - HTC
    $program_htc_ids = explode(',', SystemSetting::get('program_htc'));
     */

    public function groups()
    {
        $groups = [];

        // 1 - FAF || NSP || TCE || RTCAP || 811 units
        $program_bundle_ids = explode(',', SystemSetting::get('program_bundle'));
        if (in_array($this->program_key, $program_bundle_ids)) {
            $groups[] = 1;
        }

        // 2 - 811 units
        $program_811_ids = explode(',', SystemSetting::get('program_811'));
        if (in_array($this->program_key, $program_811_ids)) {
            $groups[] = 2;
        }

        // 3 - Medicaid units
        $program_medicaid_ids = explode(',', SystemSetting::get('program_medicaid'));
        if (in_array($this->program_key, $program_medicaid_ids)) {
            $groups[] = 3;
        }

        // 4 - HOME
        $program_home_ids = explode(',', SystemSetting::get('program_home'));
        if (in_array($this->program_key, $program_home_ids)) {
            $groups[] = 4;
        }

        // 5 - OHTF
        $program_ohtf_ids = explode(',', SystemSetting::get('program_ohtf'));
        if (in_array($this->program_key, $program_ohtf_ids)) {
            $groups[] = 5;
        }

        // 6 - NHTF
        $program_nhtf_ids = explode(',', SystemSetting::get('program_nhtf'));
        if (in_array($this->program_key, $program_nhtf_ids)) {
            $groups[] = 6;
        }

        // 7 - HTC
        $program_htc_ids = explode(',', SystemSetting::get('program_htc'));
        if (in_array($this->program_key, $program_htc_ids)) {
            $groups[] = 7;
        }

        return $groups;
    }

    public function isInGroup($group) : bool
    {
        // make is easy to know if a program belongs to a group
        switch ($group) {
            case 1:
                // 1 - FAF || NSP || TCE || RTCAP || 811 units
                $program_bundle_ids = explode(',', SystemSetting::get('program_bundle'));
                if (in_array($this->id, $program_bundle_ids)) {
                    return true;
                }
                break;
            case 2:
                // 2 - 811 units
                $program_811_ids = explode(',', SystemSetting::get('program_811'));
                if (in_array($this->id, $program_811_ids)) {
                    return true;
                }
                break;
            case 3:
                // 3 - Medicaid units
                $program_medicaid_ids = explode(',', SystemSetting::get('program_medicaid'));
                if (in_array($this->id, $program_medicaid_ids)) {
                    return true;
                }
                break;
            case 4:
                // 4 - HOME
                $program_home_ids = explode(',', SystemSetting::get('program_home'));
                if (in_array($this->id, $program_home_ids)) {
                    return true;
                }
                break;
            case 5:
                // 5 - OHTF
                $program_ohtf_ids = explode(',', SystemSetting::get('program_ohtf'));
                if (in_array($this->id, $program_ohtf_ids)) {
                    return true;
                }
                break;
            case 6:
                // 6 - NHTF
                $program_nhtf_ids = explode(',', SystemSetting::get('program_nhtf'));
                if (in_array($this->id, $program_nhtf_ids)) {
                    return true;
                }
                break;
            case 7:
                // 7 - HTC
                $program_htc_ids = explode(',', SystemSetting::get('program_htc'));
                if (in_array($this->id, $program_htc_ids)) {
                    return true;
                }
                break;
        }

        return false;
    }

    public function relatedGroups()
    {
        return $this->belongsToMany(\App\Models\Group::class, 'program_groups', 'program_id', 'group_id');
    }
}
