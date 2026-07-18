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
    }
}
