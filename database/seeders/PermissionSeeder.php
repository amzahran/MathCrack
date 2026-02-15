<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'show levels', 'add levels', 'edit levels', 'delete levels',
            'show courses', 'add courses', 'edit courses', 'delete courses',
            'show lectures', 'add lectures', 'edit lectures', 'delete lectures',
            'show lives', 'add lives', 'edit lives', 'delete lives',
            'show tests', 'add tests', 'edit tests', 'delete tests',
            'show invoices', 'add invoices', 'edit invoices', 'delete invoices',
            'show students', 'add students', 'edit students', 'delete students',
            // 'show tickets', 'add tickets', 'edit tickets', 'delete tickets',
            // 'show blog_category', 'add blog_category', 'edit blog_category', 'delete blog_category',
            // 'show blog', 'add blog', 'edit blog', 'delete blog', 'access all blog',
            // 'show newsletters_subscribers', 'add newsletters_subscribers', 'edit newsletters_subscribers', 'delete newsletters_subscribers',
            // 'show pages', 'edit pages',
            // 'show questions', 'add questions', 'edit questions', 'delete questions',
            // 'show team_members', 'add team_members', 'edit team_members', 'delete team_members',
            'show users', 'add users', 'edit users', 'delete users',
            'show roles', 'add roles', 'edit roles', 'delete roles',
            'show contact_us', 'edit contact_us', 'delete contact_us',
            // 'show tasks', 'add tasks', 'edit tasks', 'delete tasks', 'access all tasks',
            // 'show chats', 'add chats', 'edit chats', 'delete chats',
            // 'show visitors_statistics', 'show google_analytics',
            'show settings', 'edit settings',
            // 'access maintenance',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission], ['guard_name' => 'web']);
        }

        $rootRole = Role::firstOrCreate(['name' => 'root']);
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $permissions = Permission::pluck('name')->toArray();
        $rootRole->syncPermissions($permissions);
        $adminRole->syncPermissions($permissions);
    }
}
