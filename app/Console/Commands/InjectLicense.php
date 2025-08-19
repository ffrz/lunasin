<?php

/**
 * MIT License
 * 
 * Copyright (c) 2025 Fahmi Fauzi Rahman
 * GitHub: https://github.com/ffrz
 * Email: fahmifauzirahman@gmail.com
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InjectLicense extends Command
{
    protected $signature = 'license:inject';
    protected $description = 'Inject license header into PHP files in specific directories';

    // Direktori target
    protected array $targetDirs = [
        'app',
        'bootstrap',
        'config',
        'database',
        'routes',
        'tests',
    ];

    public function handle()
    {
        $this->info('Injecting license headers...');

        // Baca file LICENSE
        $licensePath = base_path('LICENSE');
        if (!File::exists($licensePath)) {
            $this->error('LICENSE file not found!');
            return 1;
        }

        $licenseText = File::get($licensePath);
        $licenseBlock = $this->wrapAsComment($licenseText);

        foreach ($this->targetDirs as $dir) {
            $path = base_path($dir);
            if (!File::isDirectory($path)) {
                $this->warn("Directory not found: $dir");
                continue;
            }

            $files = File::allFiles($path);

            foreach ($files as $file) {
                if ($file->getExtension() !== 'php') {
                    continue;
                }

                $this->processFile($file->getRealPath(), $licenseBlock);
            }
        }

        $this->info('License injection complete.');
        return 0;
    }

    private function wrapAsComment(string $text): string
    {
        // Bungkus LICENSE jadi komentar PHP
        $lines = explode("\n", trim($text));
        $commented = "/**\n";
        foreach ($lines as $line) {
            $commented .= " * " . rtrim($line) . "\n";
        }
        $commented .= " */\n\n";
        return $commented;
    }

    private function processFile(string $filePath, string $licenseBlock): void
    {
        $contents = File::get($filePath);

        // Pastikan file diawali dengan <?php
        if (strpos($contents, '<?php') !== 0) {
            return;
        }

        // Hapus komentar lama di awal file (jika ada)
        $pattern = '/^<\?php\s+(\/\*.*?\*\/\s+)?/s';
        $newContents = preg_replace($pattern, "<?php\n\n" . $licenseBlock, $contents, 1);

        if ($newContents !== null) {
            File::put($filePath, $newContents);
            $this->line("Updated: $filePath");
        }
    }
}
