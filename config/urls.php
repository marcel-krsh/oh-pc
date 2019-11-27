<?php
$domain = "http://homestead2.test/";
$user_id = 7859;
$audit_id = 6871;
$unit_id = 152144;
$project_id = 45871;
$project_key = 250225;
$building_id = 11351;
$report_id = 283;
return [
  'get_urls' => [
    $domain,
    // $domain . "unified_login",
    // $domain . "codes",
    // $domain . "home",
    // // $domain . "verification",
    // $domain . "ip",
    // // $domain . "request-access",
    // // $domain . "check",
    // $domain . "ping",
    // // $domain . "mobile/auto_login",
    // // $domain . "tablet_login",
    // // $domain . "mobile/audits",
    // // $domain . "mobile/request_auto_login",
    // // $domain . "mobile/reports",
    // // $domain . "notifications/view-message/" . $user_id . '/1',
    // // $domain . "notifications/report/" . $user_id . '/1',
    // // $domain . "mobile/reports",
    // $domain . "updated_cached_audit/" . $audit_id,
    // $domain . "modals/household/" . $unit_id,
    // $domain . "change_log",
    // // $domain . "compliance_rerun/" . $audit_id,
    // $domain . 'audit/' . $audit_id . '/rerun',
    // // $domain . 'project/' . $project_id . '/runselection',
    // $domain . 'audit/' . $audit_id . '/details',
    // $domain . 'document/list/' . $project_id,
    // $domain . 'modals/createuser_for_contact',
    // $domain . 'audit/' . $audit_id . '/details',
    // $domain . 'document/list/' . $project_id,
    // //Chat routes not included
    // $domain . 'tables/users',
    // $domain . 'tables/usersdata',
    // $domain . 'dashboard/audits',
    // $domain . 'dashboard/audits/' . $audit_id . '/buildings',
    // $domain . 'dashboard/audits/' . $audit_id . '/buildings/reorder',
    // $domain . 'dashboard/audits/' . $audit_id . '/building/' . $building_id . '/units/reorder',
    // $domain . 'dashboard/audits/' . $audit_id . '/amenities/reorder',
    // $domain . 'dashboard/audits/' . $audit_id . '/building/' . $building_id . '/details',
    // $domain . 'dashboard/audits/' . $audit_id . '/building/' . $building_id . '/inspection',
    // # $domain . 'dashboard/audits/' . $audit_id . '/building/' . $building_id . '/details/{detail_id}/inspection',

    // $domain . 'autocomplete/all',
    // $domain . 'autocomplete/auditproject',
    // $domain . 'autocomplete/auditname',
    // // $domain . 'session/filters/{type}/{value?}',
    // $domain . 'modals/audits/' . $audit_id . '/updateStep',
    // $domain . 'projects/' . $project_key . '',
    // $domain . 'projects/' . $project_key . '/title',
    // $domain . 'projects/' . $project_id . '/details',
    // $domain . 'projects/' . $project_key . '/audit-details/' . $audit_id,
    // $domain . 'projects/view/' . $project_key . '/' . $audit_id,
    // $domain . 'projects/view/' . $project_key . '/' . $audit_id . '/title',
    // $domain . 'projects/' . $project_id . '/details/compliance/' . $audit_id,
    // $domain . 'projects/' . $project_id . '/details/assignment/' . $audit_id,
    // // $domain . 'projects/{project}/details/assignment/date/{dateid}'
    // // $domain . 'projects/{project}/communications/title' // no method found! BOT USED?
    $domain . 'modals/new-report',
    $domain . 'project/' . $project_id . '/reports',
    $domain . 'report/' . $report_id . '/generate',
    $domain . 'report/' . $report_id . '/reset',
    $domain . 'report/' . $report_id . '/comments/{heading}',
    $domain . 'projects/' . $project_id . '/reports',
    $domain . 'projects/' . $project_id . '/reports/title',
    $domain . 'project/' . $project_id . '/contacts',
    $domain . 'modals/' . $project_id . '/add-user-to-project',
    // $domain . 'modals/remove-user-from-project/{project}/{user}',
    // $domain . 'modals/add-organization-to-user/{user}/{project}',
    // $domain . 'modals/edit-organization-of-user/{org}/{project}',
    // $domain . 'modals/edit-name-of-user/{user}/{project}',

  ],
  'post_urls' => [
    $domain . "login" => [
      'post_data' => ['email' => 'divya.manchiredy19@gmail.com', 'password' => 'admin123'],
    ],
  ],
];
