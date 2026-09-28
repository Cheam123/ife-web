<?php

namespace Database\Seeders;

use App\Models\GeneralSetting;
use Illuminate\Database\Seeder;

class GeneralSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $settings = collect([
            [
                'key'         => 'telegram_api',
                'value'       => '5847205589:AAFAs2F7uBykILBpj0HaoyngaH0P6A1zdRI',
                'description' => 'Telegram API',
            ],
            [
                'key'         => 'telegram_api_webhook_url',
                'value'       => 'https://jbtest.shopplustech.com//webhook/telegram/callback&drop_pending_updates=true',
                'description' => 'Telegram WebHook Url',
            ],
            [
                'key'         => 'task_submit_cnt',
                'value'       => '0',
                'description' => 'Daily running number for task references (T-Ymd-0001), reset every night',
            ],
            [
                'key'         => 'simulate_telegram_message',
                'value'       => '0',
                'description' => '',
            ],
            [
                'key'         => 'rating_closing_month',
                'value'       => '2',
                'description' => 'rating closing for edit on the end of the month',
            ],
            [
                'key'         => 'app_version',
                'value'       => '1.0.0',
                'description' => 'rating closing for edit on the end of the month',
            ],
            [
                'key'         => 'app_name',
                'value'       => 'Ronda',
                'description' => 'System name',
            ],
            [
                'key'         => 'life_version',
                'value'       => '1.0',
                'description' => 'Version of Life App',
            ],
            [
                'key'         => 'life_app_download_link',
                'value'       => 'https://youtube.com',
                'description' => 'Download link for Life App',
            ],
            [
                'key'         => 'min_version',
                'value'       => '1.9.2',
                'description' => 'Oldest Life App version still allowed to run; below this the app must update',
            ]
        ]);

        $settings->each(function ($setting) {
            GeneralSetting::firstOrCreate([
                'key' => $setting['key']
            ], [
                'value' => $setting['value'],
                'description' => $setting['description']
            ]);
        });
    }
}
