<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        if (app()->environment('production')) {
            $this->command?->warn('Seeder is disabled in production. Create users through the application UI.');
            return;
        }

        $adminPassword = 'admin123456';
        $clientPassword = 'client123456';

        $this->command?->warn('=== DEFAULT CREDENTIALS (change after first login!) ===');
        $this->command?->warn("Admin: admin@forgedesk.dev / admin123456");
        $this->command?->warn("Client: client@forgedesk.dev / client123456");
        $this->command?->warn('=====================================================');

        $admin = User::create([
            'name' => 'מנהל המערכת',
            'email' => 'admin@forgedesk.dev',
            'password' => Hash::make($adminPassword),
            'role' => 'admin',
            'phone' => '050-1234567',
            'company' => 'ForgeDesk Studio',
            'is_active' => true,
        ]);

        $client = User::create([
            'name' => 'לקוח לדוגמה',
            'email' => 'client@forgedesk.dev',
            'password' => Hash::make($clientPassword),
            'role' => 'client',
            'admin_id' => $admin->id,
            'phone' => '052-7654321',
            'company' => 'חברת דוגמה בע"מ',
            'address' => 'רחוב הרצל 1, תל אביב',
            'is_active' => true,
        ]);

        $project = Project::create([
            'user_id' => $client->id,
            'title' => 'אתר תדמיתי לחברת טכנולוגיה',
            'description' => 'בניית אתר תדמיתי חדיש עם עיצוב מודרני, כולל דפי מידע, טופס יצירת קשר וניהול תוכן.',
            'status' => 'in_progress',
            'priority' => 'high',
            'budget' => 15000,
            'paid_amount' => 5000,
            'start_date' => now()->subDays(10),
            'due_date' => now()->addDays(20),
        ]);

        Task::create([
            'project_id' => $project->id,
            'assigned_to' => $admin->id,
            'title' => 'עיצוב ממשק משתמש',
            'description' => 'יצירת עיצוב מותאם אישית לדפי האתר principales',
            'status' => 'completed',
            'priority' => 'high',
            'estimated_hours' => 16,
            'actual_hours' => 14,
        ]);

        Task::create([
            'project_id' => $project->id,
            'assigned_to' => $admin->id,
            'title' => 'פיתוח frontend',
            'description' => 'מימוש העיצוב ב-HTML, CSS ו-JavaScript',
            'status' => 'in_progress',
            'priority' => 'high',
            'estimated_hours' => 24,
            'due_date' => now()->addDays(10),
        ]);

        Task::create([
            'project_id' => $project->id,
            'assigned_to' => $admin->id,
            'title' => 'הקמת מערכת ניהול תוכן',
            'description' => 'בניית פאנל ניהול לעריכה וניהול תוכן האתר',
            'status' => 'pending',
            'priority' => 'medium',
            'estimated_hours' => 20,
            'due_date' => now()->addDays(15),
        ]);

        Task::create([
            'project_id' => $project->id,
            'assigned_to' => $admin->id,
            'title' => 'בדיקות ואופטימיזציה',
            'description' => 'בדיקות תאימות, ביצועים ואבטחה',
            'status' => 'pending',
            'priority' => 'medium',
            'estimated_hours' => 8,
            'due_date' => now()->addDays(18),
        ]);

        $project2 = Project::create([
            'user_id' => $client->id,
            'title' => 'חנות מקוונת למוצרי יודאיקה',
            'description' => 'בניית חנות מקוונת מלאה עם סליקת תשלומים, ניהול מלאי ומערכת הזמנות.',
            'status' => 'pending',
            'priority' => 'medium',
            'budget' => 25000,
            'start_date' => now()->addDays(5),
            'due_date' => now()->addDays(60),
        ]);

        Task::create([
            'project_id' => $project2->id,
            'assigned_to' => $admin->id,
            'title' => 'אפיון ותכן טכני',
            'description' => 'אפיון מפורט של החנות ובניית תכן טכני',
            'status' => 'pending',
            'priority' => 'high',
            'estimated_hours' => 12,
            'due_date' => now()->addDays(10),
        ]);

        $clients = [
            ['name' => 'חברת נדל"ן גלובלית', 'company' => 'Global Real Estate Ltd', 'email' => 'estate@demo.dev', 'phone' => '050-1111111', 'type' => 'real_estate'],
            ['name' => 'בית קפה "הפינה"', 'company' => 'HaPina Coffee', 'email' => 'cafe@demo.dev', 'phone' => '050-2222222', 'type' => 'cafe'],
            ['name' => 'משרד עורכי דין כהן ושות\'', 'company' => 'Cohen Law Firm', 'email' => 'law@demo.dev', 'phone' => '050-3333333', 'type' => 'law'],
            ['name' => 'סטודיו לעיצוב גרפי דורון', 'company' => 'Doron Design Studio', 'email' => 'design@demo.dev', 'phone' => '050-4444444', 'type' => 'design'],
            ['name' => 'מרפאת שיניים ד"ר לוי', 'company' => 'Dr. Levi Dental Clinic', 'email' => 'dental@demo.dev', 'phone' => '050-5555555', 'type' => 'dental'],
        ];

        $projectsData = [
            'real_estate' => [
                ['title' => 'אתר נדל"ן עם חיפוש מתקדם', 'status' => 'in_progress', 'priority' => 'urgent', 'budget' => 35000, 'paid' => 15000],
                ['title' => 'מערכת CRM לניהול לקוחות', 'status' => 'pending', 'priority' => 'high', 'budget' => 28000, 'paid' => 0],
            ],
            'cafe' => [
                ['title' => 'אתר תפריט והזמנות אונליין', 'status' => 'in_progress', 'priority' => 'high', 'budget' => 12000, 'paid' => 6000],
                ['title' => 'אפליקציית נאמנות לקוחות', 'status' => 'review', 'priority' => 'medium', 'budget' => 18000, 'paid' => 18000],
            ],
            'law' => [
                ['title' => 'אתר משרד עו"ד מקצועי', 'status' => 'completed', 'priority' => 'medium', 'budget' => 8000, 'paid' => 8000],
                ['title' => 'מערכת ניהול תיקים משפטיים', 'status' => 'in_progress', 'priority' => 'high', 'budget' => 45000, 'paid' => 20000],
            ],
            'design' => [
                ['title' => 'תיק עבודות דיגיטלי', 'status' => 'in_progress', 'priority' => 'medium', 'budget' => 6000, 'paid' => 3000],
            ],
            'dental' => [
                ['title' => 'אתר מרפאת שיניים + קביעת תורים', 'status' => 'in_progress', 'priority' => 'high', 'budget' => 15000, 'paid' => 7500],
                ['title' => 'מערכת תזכורות SMS למטופלים', 'status' => 'pending', 'priority' => 'medium', 'budget' => 9000, 'paid' => 0],
            ],
        ];

        $taskTemplates = [
            ['title' => 'אפיון דרישות', 'estimated' => 8, 'status' => 'completed'],
            ['title' => 'עיצוב מסכים', 'estimated' => 16, 'status' => 'completed'],
            ['title' => 'פיתוח צד שרת', 'estimated' => 24, 'status' => 'in_progress'],
            ['title' => 'פיתוח צד לקוח', 'estimated' => 20, 'status' => 'pending'],
            ['title' => 'בדיקות QA', 'estimated' => 12, 'status' => 'pending'],
            ['title' => 'העלאה לשרת והטמעה', 'estimated' => 6, 'status' => 'pending'],
        ];

        foreach ($clients as $data) {
            $client = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make('client123456'),
                'role' => 'client',
                'admin_id' => $admin->id,
                'phone' => $data['phone'],
                'company' => $data['company'],
                'is_active' => true,
            ]);

            $projects = $projectsData[$data['type']] ?? [];
            foreach ($projects as $i => $p) {
                $project = Project::create([
                    'user_id' => $client->id,
                    'title' => $p['title'],
                    'description' => 'פרויקט ' . $p['title'] . ' - כולל ליווי מלא משלב האפיון ועד ההשקה.',
                    'status' => $p['status'],
                    'priority' => $p['priority'],
                    'budget' => $p['budget'],
                    'paid_amount' => $p['paid'],
                    'hourly_rate' => rand(150, 300),
                    'estimated_hours' => collect($taskTemplates)->sum('estimated'),
                    'start_date' => now()->subDays(rand(5, 30)),
                    'due_date' => now()->addDays(rand(14, 90)),
                ]);

                foreach ($taskTemplates as $j => $task) {
                    $taskStatus = $p['status'] === 'completed' ? 'completed' : $task['status'];
                    if ($p['status'] === 'completed') {
                        $taskStatus = 'completed';
                    } elseif ($p['status'] === 'pending' && $j === 0) {
                        $taskStatus = 'in_progress';
                    }

                    Task::create([
                        'project_id' => $project->id,
                        'assigned_to' => $admin->id,
                        'title' => $task['title'],
                        'description' => 'ביצוע ' . $task['title'] . ' עבור ' . $p['title'],
                        'status' => $taskStatus,
                        'priority' => $j < 2 ? 'high' : 'medium',
                        'estimated_hours' => $task['estimated'],
                        'actual_hours' => $taskStatus === 'completed' ? ($task['estimated'] - rand(0, 2)) : null,
                        'due_date' => $j === 0 ? now()->addDays(7) : now()->addDays(14 * ($j + 1)),
                    ]);
                }
            }
        }
    }
}
