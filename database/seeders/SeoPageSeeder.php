<?php

namespace Database\Seeders;

use App\Models\SeoPage;
use Illuminate\Database\Seeder;

class SeoPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pages = [
            [
                'slug' => '/',
                'title' => 'Home',
            ],
            [
                'slug' => 'about',
                'title' => 'About Us',
            ],
            [
                'slug' => 'services',
                'title' => 'Services',
            ],
            [
                'slug' => 'contact',
                'title' => 'Contact Us',
            ],
            [
                'slug' => 'blog',
                'title' => 'Blog',
            ],
            [
                'slug' => 'faq',
                'title' => 'FAQ',
            ],
        ];

        foreach ($pages as $page) {
            SeoPage::firstOrCreate(['slug' => $page['slug']], $page);
        }
    }
}