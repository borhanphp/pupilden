<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //DB::table('permissions')->truncate(); // clean table first
        // User Management
        Permission::create(['name' => 'view users', 'head' => 'User Management']);
        Permission::create(['name' => 'create users', 'head' => 'User Management']);
        Permission::create(['name' => 'edit users', 'head' => 'User Management']);
        Permission::create(['name' => 'delete users', 'head' => 'User Management']);

        // Role Management
        Permission::create(['name' => 'view roles', 'head' => 'Role Management']);
        Permission::create(['name' => 'create roles', 'head' => 'Role Management']);
        Permission::create(['name' => 'edit roles', 'head' => 'Role Management']);
        Permission::create(['name' => 'delete roles', 'head' => 'Role Management']);

        // Permission Management
        Permission::create(['name' => 'view permissions', 'head' => 'Permission Management']);
        Permission::create(['name' => 'create permissions', 'head' => 'Permission Management']);
        Permission::create(['name' => 'edit permissions', 'head' => 'Permission Management']);
        Permission::create(['name' => 'delete permissions', 'head' => 'Permission Management']);

        // Answer Management
        Permission::create(['name' => 'view answers', 'head' => 'Answer Management']);
        Permission::create(['name' => 'create answers', 'head' => 'Answer Management']);
        Permission::create(['name' => 'edit answers', 'head' => 'Answer Management']);
        Permission::create(['name' => 'delete answers', 'head' => 'Answer Management']);

        // Certificate Management
        Permission::create(['name' => 'view certificates', 'head' => 'Certificate Management']);
        Permission::create(['name' => 'create certificates', 'head' => 'Certificate Management']);
        Permission::create(['name' => 'edit certificates', 'head' => 'Certificate Management']);
        Permission::create(['name' => 'delete certificates', 'head' => 'Certificate Management']);

        // Course Management
        Permission::create(['name' => 'view courses', 'head' => 'Course Management']);
        Permission::create(['name' => 'create courses', 'head' => 'Course Management']);
        Permission::create(['name' => 'edit courses', 'head' => 'Course Management']);
        Permission::create(['name' => 'delete courses', 'head' => 'Course Management']);

        // CourseCategory Management
        Permission::create(['name' => 'view course categories', 'head' => 'Course Category Management']);
        Permission::create(['name' => 'create course categories', 'head' => 'Course Category Management']);
        Permission::create(['name' => 'edit course categories', 'head' => 'Course Category Management']);
        Permission::create(['name' => 'delete course categories', 'head' => 'Course Category Management']);

        // CourseStudent Management
        Permission::create(['name' => 'view course students', 'head' => 'Course Student Management']);
        Permission::create(['name' => 'create course students', 'head' => 'Course Student Management']);
        Permission::create(['name' => 'edit course students', 'head' => 'Course Student Management']);
        Permission::create(['name' => 'delete course students', 'head' => 'Course Student Management']);

        // CourseSubCategory Management
        Permission::create(['name' => 'view course sub categories', 'head' => 'Course Sub Category Management']);
        Permission::create(['name' => 'create course sub categories', 'head' => 'Course Sub Category Management']);
        Permission::create(['name' => 'edit course sub categories', 'head' => 'Course Sub Category Management']);
        Permission::create(['name' => 'delete course sub categories', 'head' => 'Course Sub Category Management']);

        // Domain Management
        Permission::create(['name' => 'view domains', 'head' => 'Domain Management']);
        Permission::create(['name' => 'create domains', 'head' => 'Domain Management']);
        Permission::create(['name' => 'edit domains', 'head' => 'Domain Management']);
        Permission::create(['name' => 'delete domains', 'head' => 'Domain Management']);

        // Exam Management
        Permission::create(['name' => 'view exams', 'head' => 'Exam Management']);
        Permission::create(['name' => 'create exams', 'head' => 'Exam Management']);
        Permission::create(['name' => 'edit exams', 'head' => 'Exam Management']);
        Permission::create(['name' => 'delete exams', 'head' => 'Exam Management']);

        //ExamAttempts Management
        Permission::create(['name' => 'view exam attempts', 'head' => 'Exam Attempts Management']);
        Permission::create(['name' => 'create exam attempts', 'head' => 'Exam Attempts Management']);
        Permission::create(['name' => 'edit exam attempts', 'head' => 'Exam Attempts Management']);
        Permission::create(['name' => 'delete exam attempts', 'head' => 'Exam Attempts Management']);

        //Organization Management
        Permission::create(['name' => 'view organizations', 'head' => 'Organization Management']);
        Permission::create(['name' => 'create organizations', 'head' => 'Organization Management']);
        Permission::create(['name' => 'edit organizations', 'head' => 'Organization Management']);
        Permission::create(['name' => 'delete organizations', 'head' => 'Organization Management']);

        // Question Management
        Permission::create(['name' => 'view sales orders', 'head' => 'Sale Order Management']);
        Permission::create(['name' => 'create questions', 'head' => 'Question Management']);
        Permission::create(['name' => 'edit questions', 'head' => 'Question Management']);
        Permission::create(['name' => 'delete questions', 'head' => 'Question Management']);

        // Student Management
        Permission::create(['name' => 'view students', 'head' => 'students Management']);
        Permission::create(['name' => 'create students', 'head' => 'Student Management']);
        Permission::create(['name' => 'edit students', 'head' => 'Student Management']);
        Permission::create(['name' => 'delete students', 'head' => 'Student Management']);
        Permission::create(['name' => 'approve students', 'head' => 'Student Management']);

        // Video Management
        Permission::create(['name' => 'view videos', 'head' => 'Video Management']);
        Permission::create(['name' => 'create videos', 'head' => 'Video Management']);
        Permission::create(['name' => 'edit videos', 'head' => 'Video Management']);
        Permission::create(['name' => 'delete videos', 'head' => 'Video Management']);

        // Video watch History Management
        Permission::create(['name' => 'view video watch histories', 'head' => 'Video Watch History Management']);
        Permission::create(['name' => 'create video watch histories', 'head' => 'Video Watch History Management']);
        Permission::create(['name' => 'edit video watch histories', 'head' => 'Video Watch History Management']);
        Permission::create(['name' => 'delete video watch histories', 'head' => 'Video Watch History Management']);

    }
}
