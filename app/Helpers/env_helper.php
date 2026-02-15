<?php

if (!function_exists('update_env')) {
    /**
     * تحديث متغيرات ملف .env
     * @param array $data مصفوفة تحتوي على المتغيرات وقيمها
     * @return bool
     */
    function update_env(array $data): bool
    {
        try {
            $envFile = base_path('.env');
            $content = file_get_contents($envFile);

            foreach ($data as $key => $value) {
                $value = is_string($value) ? '"' . $value . '"' : $value;

                if (strpos($content, "{$key}=") !== false) {
                    $content = preg_replace(
                        "/{$key}=[^\n]*/",
                        "{$key}={$value}",
                        $content
                    );
                } else {
                    $content .= "\n{$key}={$value}";
                }
            }

            file_put_contents($envFile, $content);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}

