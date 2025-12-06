<?php

namespace Database\Seeders;

use App\Models\Tutor;
use App\Models\TutoringSession;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class TutorSeeder extends Seeder
{
    public function run()
    {
        $tutors = [
            [
                'name' => 'Sarah Johnson',
                'email' => 'sarah@example.com',
                'phone' => '555-0101',
                'bio' => 'Mathematics expert with 10 years of experience',
                'subjects' => ['Mathematics', 'Physics'],
                'hourly_rate' => 50.00,
            ],
            [
                'name' => 'Michael Chen',
                'email' => 'michael@example.com',
                'phone' => '555-0102',
                'bio' => 'Computer Science and Programming specialist',
                'subjects' => ['Programming', 'Computer Science'],
                'hourly_rate' => 60.00,
            ],
            [
                'name' => 'Emily Rodriguez',
                'email' => 'emily@example.com',
                'phone' => '555-0103',
                'bio' => 'Language arts and literature teacher',
                'subjects' => ['English', 'Literature', 'Writing'],
                'hourly_rate' => 45.00,
            ],
        ];

        foreach ($tutors as $tutorData) {
            $tutor = Tutor::create($tutorData);

            // Create sample sessions for each tutor
            for ($i = 0; $i < 5; $i++) {
                TutoringSession::create([
                    'tutor_id' => $tutor->id,
                    'session_date' => Carbon::now()->addDays($i + 1),
                    'start_time' => '14:00',
                    'end_time' => '15:00',
                    'subject' => $tutorData['subjects'][0],
                    'status' => 'available',
                ]);
            }
        }
    }
}