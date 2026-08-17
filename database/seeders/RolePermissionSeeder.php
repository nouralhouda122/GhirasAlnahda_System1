<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Department;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $superAdminRole       = Role::firstOrCreate(['name' => 'Super Admin']);
        $managerRole          = Role::firstOrCreate(['name' => 'Manager']);
        $volunteerManagerRole = Role::firstOrCreate(['name' => 'Volunteer Manager']);
        $teamLeaderRole       = Role::firstOrCreate(['name' => 'Team Leader']);
        $employeeRole         = Role::firstOrCreate(['name' => 'Employee']);
        $volunteerRole        = Role::firstOrCreate(['name' => 'Volunteer']);

        // 2. قائمة الصلاحيات الخام
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

        $adminDept = Department::firstOrCreate(['name' => 'General Administration']);

        $deptRoleId = DB::table('department_roles')
            ->where('department_id', $adminDept->id)
            ->where('role_id', $superAdminRole->id)
            ->value('id');

        if (! $deptRoleId) {
            $deptRoleId = DB::table('department_roles')->insertGetId([
                'department_id' => $adminDept->id,
                'role_id' => $superAdminRole->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $allPermissionIds = Permission::pluck('id')->toArray();
        $existingPermissionIds = DB::table('department_role_permissions')
            ->where('department_role_id', $deptRoleId)
            ->pluck('permission_id')
            ->all();

        foreach (array_diff($allPermissionIds, $existingPermissionIds) as $permissionId) {
            DB::table('department_role_permissions')->insert([
                'department_role_id' => $deptRoleId,
                'permission_id' => $permissionId,
            ]);
        }
        User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('12345678'),
                'email_verified_at' => now(),
                'department_role_id' => $deptRoleId,
            ]
        );
    }
}
