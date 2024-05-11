<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MentorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                "name" => "joko",
                "profile" => "http://127.0.0.1:8000/images/RHmCQfnFjG.jpeg",
                "profession" => "web developer",
                "email" => "joko@gmail.com"
            ],
            [
                "name" => "farid attar",
                "profile" => "http://127.0.0.1:8000/images/RHmCQfnFjG.jpeg",
                "profession" => "software developer",
                "email" => "farid123@gmail.com"
            ]
        ];

        DB::table('mentors')->insert($data);
    }
}
