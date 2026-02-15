<?php

namespace Database\Seeders;

use App\Models\Setting;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $options = [
            'name' => 'Duralux Starter',
            'domain' => 'https://example.com',
            'description' => 'We give holistic solutions with strategy, design & technology. Design and programming web applications. Our Web Development service provides cutting-edge solutions to meet your business needs.',
            'keywords' => 'web development, web design, programming, software development, digital solutions',
            'home_video' => 'https://www.youtube.com/embed/your-video-id',
            'logo' => 'logo.png',
            'logo_black' => 'logo-black.png',
            'favicon' => 'favicon.png',
            'theme' => 'default',
            'primary_color' => '#3482FFF4',
            'timezone' => 'Africa/Cairo',
            'default_language' => 'en',
            'default_currency' => 'egp',
            'maintenance' => 0,
            'multistep_register' => 0,
            'emailVerified' => 0,
            'recaptcha' => 0,
            'recaptcha_site_key' => '6LcdgoYqAAAAALmmMQxSyV4KzgGRFrTtf7esBU68',
            'recaptcha_secret' => '6LcdgoYqAAAAAErmuceR5ZG7XSGpWgnBDN_E2Oaq',
            'max_sessions' => 1,
            'session_timeout' => 2,
            'max_attempts' => 3,
            'email1' => 'test@example.com',
            'email2' => 'support@example.com',
            'phone1' => '+20123456789',
            'phone2' => '+20123456789',
            'whatsapp' => '+20123456789',
            'facebook' => 'https://facebook.com/example',
            'twitter' => 'https://x.com/example',
            'instagram' => 'https://instagram.com/example',
            'linkedin' => 'https://linkedin.com/example',
            'youtube' => 'https://youtube.com/example',
            'address' => '66 avenue des Champs, 75008, Paris, France',
            'author' => 'Adel Fawzy',
            'app_store_link' => 'https://apps.apple.com/app/id1234567890',
            'play_store_link' => 'https://play.google.com/store/apps/details?id=com.example.app',
            'email_enabled' => 0,
            'sms_enabled' => 0,
            'email' => 'support@example.com',
            'MAIL_PASSWORD' => '********',
            'MAIL_HOST' => 'mail.example.com',
            'MAIL_PORT' => '465',
            'MAIL_ENCRYPTION' => 'ssl',
            'can_any_register' => 1,
            'googleLogin' => 0,
            'facebookLogin' => 0,
            'twitterLogin' => 0,
            'GOOGLE_CLIENT_ID' => '326522251635-h38btkri1gg5kn7i40chv56s84riqi60.apps.googleusercontent.com',
            'GOOGLE_CLIENT_SECRET' => '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',
            'FACEBOOK_CLIENT_ID' => '508478304153531',
            'FACEBOOK_CLIENT_SECRET' => 'bdaefd7146784b2c272a39ff016a1d04',
            'TWITTER_CLIENT_API_KEY' => 'vPJAO8m4gYyycVvrOtSILFQrr',
            'TWITTER_CLIENT_API_SECRET_KEY' => 'n5RoZez3iX16XzVVcl6gmWxJqiP5RgfJMPeUeYcVcwpvnO75fr',
            'headerCode' => null,
            'footerCode' => null,
            'allow_cookies' => 0,
            'google_analytics' => null,
        ];

        foreach ($options as $option => $value) {
            Setting::firstOrCreate(['option' => $option], ['value' => $value]);
        }
    }
}
