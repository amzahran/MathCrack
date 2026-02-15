<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    public function run()
    {
        $languages = config('laravellocalization.supportedLocales');

        // قائمة اللغات التي تستخدم RTL
        $rtlLanguages = ['ar', 'fa', 'he', 'ur'];

        $popularLanguages = ['en', 'ar'];
        foreach ($languages as $code => $language) {
            // تحويل رمز اللغة إلى رمز العلم
            $flag = $language['regional']
                ? strtolower(substr($language['regional'], -2))
                : strtolower(substr($code, 0, 2));

            Language::create([
                'code' => $code,
                'name' => $language['name'],
                'native' => $language['native'],
                'regional' => $language['regional'],
                'flag' => $flag,
                'is_active' => in_array($code, $popularLanguages),
                'is_rtl' => in_array($code, $rtlLanguages), // تحديد اتجاه اللغة
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}