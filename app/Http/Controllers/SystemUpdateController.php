<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SystemUpdateController extends Controller
{
    public function update(Request $request)
    {
        $commands = [
            'cd ' . base_path() . ' && git pull origin main',
            'cd ' . base_path() . ' && composer install --no-dev --optimize-autoloader',
            'cd ' . base_path() . ' && php artisan migrate --force',
            'cd ' . base_path() . ' && php artisan optimize:clear',
        ];

        $output = [];

        foreach ($commands as $command) {
            $output[] = shell_exec($command . ' 2>&1');
        }

        return response()->json([
            'success' => true,
            'output' => $output,
        ]);
    }
}
