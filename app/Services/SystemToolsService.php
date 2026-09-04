<?php

namespace App\Services;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Throwable;

/**
 * Lets a super admin run a handful of deployment tasks (migrations, composer
 * install, cache clearing) from the Admin → Settings → System Tools UI, for
 * hosts where a terminal / SSH is inconvenient or unavailable.
 *
 * Every method returns ['success' => bool, 'output' => string] instead of
 * throwing, so the controller can always render a clean result — this is an
 * operator tool for the site owner, so, unlike the public API, showing the
 * real error text is intentional and useful.
 */
class SystemToolsService
{
    private const MIGRATION_NAME_PATTERN = '/^[A-Za-z0-9_]+$/';

    // ── Migrations ────────────────────────────────────────────────────────────

    /**
     * Migration filenames (without .php) that exist on disk but have not yet
     * been recorded in the migrations table.
     *
     * @return list<string>
     */
    public function pendingMigrations(): array
    {
        $files = collect(File::files(database_path('migrations')))
            ->map(fn ($file) => pathinfo($file->getFilename(), PATHINFO_FILENAME))
            ->sort()
            ->values();

        $ran = DB::table('migrations')->pluck('migration')->all();

        return $files->reject(fn ($name) => in_array($name, $ran, true))->values()->all();
    }

    /**
     * @return array{success: bool, output: string}
     */
    public function runAllMigrations(): array
    {
        try {
            Artisan::call('migrate', ['--force' => true]);

            return ['success' => true, 'output' => trim(Artisan::output())];
        } catch (Throwable $e) {
            return ['success' => false, 'output' => $e->getMessage()];
        }
    }

    /**
     * Run exactly one migration file by name. $name must exactly match an
     * existing file in database/migrations (no path separators or extension)
     * so this can never be used to run an arbitrary path.
     *
     * @return array{success: bool, output: string}
     */
    public function runMigration(string $name): array
    {
        if (! preg_match(self::MIGRATION_NAME_PATTERN, $name)) {
            return ['success' => false, 'output' => 'Invalid migration name.'];
        }

        $path = database_path("migrations/{$name}.php");

        if (! File::exists($path)) {
            return ['success' => false, 'output' => 'Migration file not found.'];
        }

        try {
            Artisan::call('migrate', [
                '--path'     => "database/migrations/{$name}.php",
                '--force'    => true,
                '--realpath' => false,
            ]);

            return ['success' => true, 'output' => trim(Artisan::output())];
        } catch (Throwable $e) {
            return ['success' => false, 'output' => $e->getMessage()];
        }
    }

    // ── Composer ──────────────────────────────────────────────────────────────

    /**
     * @return array{success: bool, output: string}
     */
    public function runComposerInstall(): array
    {
        if (! function_exists('proc_open')) {
            return [
                'success' => false,
                'output'  => 'proc_open() is disabled on this server, so composer cannot be run from the browser. '
                           . 'Run "composer install" via SSH or your host\'s terminal instead.',
            ];
        }

        try {
            $result = Process::path(base_path())
                ->timeout(300)
                ->run(['composer', 'install', '--no-interaction', '--optimize-autoloader']);

            return [
                'success' => $result->successful(),
                'output'  => trim($result->output() . PHP_EOL . $result->errorOutput()),
            ];
        } catch (Throwable $e) {
            return [
                'success' => false,
                'output'  => 'Could not run composer: ' . $e->getMessage()
                           . ' — it may not be installed or reachable on this server\'s PATH. Use SSH/terminal instead.',
            ];
        }
    }

    // ── Cache ─────────────────────────────────────────────────────────────────

    /**
     * @return array{success: bool, output: string}
     */
    public function clearCache(string $type): array
    {
        $commands = match ($type) {
            'config' => ['config:clear'],
            'route'  => ['route:clear'],
            'view'   => ['view:clear'],
            'cache'  => ['cache:clear'],
            'all'    => ['optimize:clear'],
            default  => null,
        };

        if ($commands === null) {
            return ['success' => false, 'output' => 'Unknown cache type.'];
        }

        try {
            $output = '';
            foreach ($commands as $command) {
                Artisan::call($command);
                $output .= Artisan::output();
            }

            return ['success' => true, 'output' => trim($output)];
        } catch (Throwable $e) {
            return ['success' => false, 'output' => $e->getMessage()];
        }
    }
}
