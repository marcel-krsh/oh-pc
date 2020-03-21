<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Program;
use App\Models\ProgramGroup;
use App\Models\SystemSetting;
use Auth;

class GroupController extends Controller
{

     public function __construct(){
        $this->allitapc();
    }

    public function getGroupsJson()
    {
        $groups = Group::active()->get();
        return response()->json($groups);
    }

    public function udateGroupProgramRelations()
    {
        $system_settings = SystemSetting::where('id', '<', 47)->get();
        $new = 0;
        $updated = 0;
        $issues = array();
        $issues['count'] = 0;
        foreach ($system_settings as $key => $s) {
            $pr_keys = explode(',', $s->value);
            foreach ($pr_keys as $key => $pr_key) {
                $temp_gr = explode('_', $s->key);
                if ($temp_gr[1] == 'bundle') {
                    $temp_gr[1] = 'FAF NSP TCE RTCAP 811';
                }
                $group = Group::where('group_name', strtoupper($temp_gr[1]))->first();
                if (is_null($group)) {
                    $issues['count']++;
                    $issues['issue'][] = 'Group not found for  ' . $temp_gr[1];
                    break;
                }
                $program = Program::whereProgramKey($pr_key)->first();
                if (is_null($program)) {
                    $issues['count']++;
                    $issues['issue'][] = 'Program not found for program key = ' . $pr_key;
                    break;
                }
                $check_pg = ProgramGroup::whereProgramKey($pr_key)->whereGroupId($group->id)->first();
                if (!is_null($check_pg)) {
                    $program_group = $check_pg;
                    $updated++;
                } else {
                    $program_group = new ProgramGroup;
                    $new++;
                }
                $program_group->group_id = $group->id;
                $program_group->program_id = $program->id;
                $program_group->program_key = $pr_key;
                $program_group->save();
            }
        }
        return 'Added ' . $new . ', and updated ' . $updated;
    }

}
