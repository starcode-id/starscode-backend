<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                "name" => "kelas digital marketing ",
                "certificate" => true,
                "type" => "free",
                "price" => 0,
                "status" => "published",
                "level" => "intermediate",
                "mentor_id" => 2
            ],
            [
                "name" => "cybersecurity ",
                "certificate" => true,
                "type" => "premium",
                "price" => 500000,
                "status" => "published",
                "level" => "intermediate",
                "mentor_id" => 2
            ]
        ];

        DB::table('courses')->insert($data);
    }
}
