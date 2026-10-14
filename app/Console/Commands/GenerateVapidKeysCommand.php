<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Minishlink\WebPush\VAPID;

class GenerateVapidKeysCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'webpush:vapid {--show : Chỉ hiển thị khóa VAPID mà không tự động ghi vào .env}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tạo cặp khóa VAPID (Public & Private Key) cho Web Push Notifications';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // Tự động cấu hình đường dẫn OPENSSL_CONF trên Windows Laragon nếu chưa có
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' && ! getenv('OPENSSL_CONF')) {
            $possiblePaths = [
                'D:\\laragon\\laragon\\bin\\php\\php-8.3.26-Win32-vs16-x64\\extras\\ssl\\openssl.cnf',
                'D:\\laragon\\bin\\php\\extras\\ssl\\openssl.cnf',
                'C:\\laragon\\bin\\php\\extras\\ssl\\openssl.cnf',
                dirname(PHP_BINARY).'\\extras\\ssl\\openssl.cnf',
            ];

            foreach ($possiblePaths as $path) {
                if (file_exists($path)) {
                    putenv("OPENSSL_CONF={$path}");
                    $_ENV['OPENSSL_CONF'] = $path;
                    break;
                }
            }
        }

        try {
            $keys = VAPID::createVapidKeys();
        } catch (\Throwable $e) {
            $this->error('Không thể tạo khóa VAPID: '.$e->getMessage());

            return Command::FAILURE;
        }

        $publicKey = $keys['publicKey'];
        $privateKey = $keys['privateKey'];

        $this->info('Đã tạo thành công cặp khóa VAPID chuẩn:');
        $this->line("VAPID_PUBLIC_KEY={$publicKey}");
        $this->line("VAPID_PRIVATE_KEY={$privateKey}");

        if ($this->option('show')) {
            return Command::SUCCESS;
        }

        // Cập nhật vào file .env nếu có
        $envPath = base_path('.env');
        if (file_exists($envPath)) {
            $content = file_get_contents($envPath);

            if (str_contains($content, 'VAPID_PUBLIC_KEY=')) {
                $content = preg_replace('/VAPID_PUBLIC_KEY=.*/', "VAPID_PUBLIC_KEY=\"{$publicKey}\"", $content);
                $content = preg_replace('/VAPID_PRIVATE_KEY=.*/', "VAPID_PRIVATE_KEY=\"{$privateKey}\"", $content);
            } else {
                $content .= "\n# VAPID Web Push Notifications\nVAPID_SUBJECT=\"mailto:admin@lifeplanner.local\"\nVAPID_PUBLIC_KEY=\"{$publicKey}\"\nVAPID_PRIVATE_KEY=\"{$privateKey}\"\n";
            }

            file_put_contents($envPath, $content);
            $this->info('Đã lưu khóa VAPID mới vào file .env thành công!');
        }

        return Command::SUCCESS;
    }
}
