<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ActivityLogService;
use App\Services\SystemToolsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Deployment convenience tools (migrations, composer, cache clearing) for
 * environments without terminal/SSH access. Restricted to super_admin only —
 * these run real server-side commands, unlike the rest of Admin → Settings.
 */
class SystemToolsController extends Controller
{
    public function __construct(private readonly SystemToolsService $tools) {}

    public function pendingMigrations(Request $request): JsonResponse
    {
        $this->authorizeSuperAdmin($request);

        return response()->json([
            'status' => true,
            'data'   => ['pending' => $this->tools->pendingMigrations()],
        ]);
    }

    public function migrate(Request $request): JsonResponse
    {
        $this->authorizeSuperAdmin($request);

        $result = $this->tools->runAllMigrations();

        ActivityLogService::systemToolRun('Ran all pending migrations.');

        return $this->toolResponse($result, 'All pending migrations ran successfully.', 'Migration failed.');
    }

    public function migrateOne(Request $request): JsonResponse
    {
        $this->authorizeSuperAdmin($request);

        $validated = $request->validate([
            'migration' => ['required', 'string', 'max:255'],
        ]);

        $result = $this->tools->runMigration($validated['migration']);

        ActivityLogService::systemToolRun("Ran migration \"{$validated['migration']}\".");

        return $this->toolResponse($result, 'Migration ran successfully.', 'Migration failed.');
    }

    public function composerInstall(Request $request): JsonResponse
    {
        $this->authorizeSuperAdmin($request);

        $result = $this->tools->runComposerInstall();

        ActivityLogService::systemToolRun('Ran composer install.');

        return $this->toolResponse($result, 'Composer install completed.', 'Composer install failed.');
    }

    public function recentLogs(Request $request): JsonResponse
    {
        $this->authorizeSuperAdmin($request);

        $filter = (string) $request->query('filter', '');
        $result = $this->tools->recentLogLines($filter);

        return $this->toolResponse($result, 'Loaded recent log entries.', 'Could not read the log file.');
    }

    public function cacheClear(Request $request): JsonResponse
    {
        $this->authorizeSuperAdmin($request);

        $validated = $request->validate([
            'type' => ['required', 'in:config,route,view,cache,opcache,all'],
        ]);

        $result = $this->tools->clearCache($validated['type']);

        ActivityLogService::systemToolRun("Cleared \"{$validated['type']}\" cache.");

        return $this->toolResponse($result, 'Cache cleared successfully.', 'Failed to clear cache.');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function authorizeSuperAdmin(Request $request): void
    {
        abort_unless($request->user()?->isSuperAdmin(), 403, 'Only the super admin can use system tools.');
    }

    /**
     * @param  array{success: bool, output: string}  $result
     */
    private function toolResponse(array $result, string $successMessage, string $failureMessage): JsonResponse
    {
        return response()->json([
            'status'  => $result['success'],
            'message' => $result['success'] ? $successMessage : $failureMessage,
            'data'    => ['output' => $result['output']],
        ], $result['success'] ? 200 : 500);
    }
}
