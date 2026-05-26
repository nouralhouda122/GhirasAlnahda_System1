<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // =====================
        // ROLES + DEPARTMENTS
        // =====================

        // Campaign Department (1)
        $campaignManager = Role::firstOrCreate([
            'name' => 'Campaign Manager',
            'department_id' => 1
        ]);

        $campaignEmployee = Role::firstOrCreate([
            'name' => 'Campaign Employee',
            'department_id' => 1
        ]);

        $teamLeader = Role::firstOrCreate([
            'name' => 'Team Leader',
            'department_id' => 1
        ]);

        $volunteerManager = Role::firstOrCreate([
            'name' => 'Volunteer Manager',
            'department_id' => 1
        ]);

        // Monitoring / Evaluation Department (2)
        $evaluationManager = Role::firstOrCreate([
            'name' => 'Evaluation Manager',
            'department_id' => 2
        ]);

        $evaluationOfficer = Role::firstOrCreate([
            'name' => 'Evaluation Officer',
            'department_id' => 2
        ]);

        // Volunteer Department (3)
        $volunteer = Role::firstOrCreate([
            'name' => 'Volunteer',
            'department_id' => 3
        ]);

        // Super Admin (no department)
        $superAdmin = Role::firstOrCreate([
            'name' => 'Super Admin',
            'department_id' => null
        ]);

        // =====================
        // PERMISSIONS
        // =====================

        $permissions = [
            'view.user','add.user','edit.user','delete.user','ban.user',
            'show.Employee','view.department','view.department.details',
            'create.department','edit.department','assign.department.manager',
            'view.dashboard.kpi','view.statistics','view.statistics.details',
            'view.growth.metrics','add.course','create Campaign Employee',
            'create Volunteer Manager','create Evaluation Officer',
            'view.campaign','view.campaign.details','create.campaign',
            'edit.campaign','archive.campaign','view.donation',
            'confirm.donation','view.volunteer','view.volunteer.details',
            'view.top.volunteers','promote.volunteer','view.points',
            'view.points.record','add.points','remove.points',
            'view.attendance','record.attendance','record.checkout',
            'view.volunteer.attendance','manage.team.attendance',
            'scan.volunteer.qr','view.join.request','approve.join.request',
            'reject.join.request','join.campaign','view.evaluation.request',
            'create.survey','edit.survey','assign.evaluation.task',
            'submit.evaluation','view.evaluation.result',
            'send.evaluation.report','Showdetail.Employee','view.task',
            'update.task.status','Update.Employee','view.course',
            'enroll.course','view.complaint','create.complaint',
            'edit.complaint','resolve.complaint','view.post','create.post',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web'
            ]);
        }

        // =====================
        // ASSIGN PERMISSIONS
        // =====================

        $superAdmin->givePermissionTo(Permission::all());

        $campaignManager->givePermissionTo([
            'create Campaign Employee','create Volunteer Manager',
            'add.course','view.campaign','add.user','show.Employee',
            'Update.Employee','Showdetail.Employee','view.campaign.details',
            'create.campaign','edit.campaign','archive.campaign',
            'view.volunteer','view.volunteer.details','view.top.volunteers',
            'promote.volunteer','view.attendance','view.donation',
            'confirm.donation','view.points.record','add.points',
            'remove.points','view.join.request','approve.join.request',
            'reject.join.request','view.complaint','resolve.complaint',
        ]);

        $campaignEmployee->givePermissionTo([
            'view.campaign','show.Employee','view.campaign.details',
            'view.attendance','view.donation','view.volunteer',
            'view.volunteer.details','view.top.volunteers',
            'view.complaint','create.complaint','view.join.request',
        ]);

        $teamLeader->givePermissionTo([
            'record.attendance','record.checkout','scan.volunteer.qr',
            'view.volunteer.attendance','manage.team.attendance',
            'view.campaign','view.attendance','view.post',
            'create.post','view.complaint','create.complaint',
            'join.campaign',
        ]);

        $volunteerManager->givePermissionTo([
            'view.volunteer','show.Employee','view.volunteer.details',
            'view.top.volunteers','promote.volunteer',
            'view.points.record','add.points','remove.points',
            'view.join.request','approve.join.request',
            'reject.join.request','view.campaign','view.attendance',
        ]);

        $evaluationManager->givePermissionTo([
            'create Evaluation Officer','view.evaluation.request',
            'show.Employee','Update.Employee','Showdetail.Employee',
            'create.survey','edit.survey','add.user',
            'assign.evaluation.task','view.evaluation.result',
            'send.evaluation.report','view.task','update.task.status',
            'view.complaint','resolve.complaint',
        ]);

        $evaluationOfficer->givePermissionTo([
            'view.task','show.Employee','update.task.status',
            'submit.evaluation','view.evaluation.request',
            'view.complaint','create.complaint',
        ]);

        $volunteer->givePermissionTo([
            'join.campaign','view.course','enroll.course',
            'view.post','create.post','view.complaint',
            'create.complaint','view.attendance','view.points',
        ]);

        // =====================
        // USERS
        // =====================

        $adminUser = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('12345678'),
                'email_verified_at' => now(),
            ]
        );

        $adminUser->assignRole('Super Admin');

        $managerCampaignUser = User::firstOrCreate(
            ['email' => 'campaign@gmail.com'],
            [
                'name' => 'Campaign Manager',
                'password' => Hash::make('12345678')
            ]
        );

        $managerCampaignUser->assignRole('Campaign Manager');
    }
}
