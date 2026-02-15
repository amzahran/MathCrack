<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Theme;

class ThemeSeeder extends Seeder
{
    public function run(): void
    {
        // بيانات الموضوعات
        $themes = [
            [
                'name' => 'default',
                'image' => 'images/themes/default.png'
            ],
            [
                'name' => 'theme1',
                'image' => 'images/themes/theme1.png'
            ],
        ];

        // إنشاء أو تحديث السجلات
        foreach ($themes as $theme) {
            Theme::firstOrCreate(
                ['name' => $theme['name']],
                ['image' => $theme['image']]
            );
        }
    }
}
