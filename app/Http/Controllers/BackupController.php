<?php

namespace App\Http\Controllers;

use App\Models\BackupLog;
use App\Services\BackupService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BackupController extends Controller
{
    public function __construct(
        protected BackupService $backupService
    ) {}

    public function index(): View
    {
        $backups = BackupLog::latest()->paginate(10);
        $specs = $this->backupService->getDisasterRecoverySpecifications();

        $totalBackups = BackupLog::count();
        $verifiedCount = BackupLog::where('status', 'verified')->count();
        $totalStorageBytes = BackupLog::sum('file_size_bytes');

        return view('backup.index', compact(
            'backups',
            'specs',
            'totalBackups',
            'verifiedCount',
            'totalStorageBytes'
        ));
    }

    public function createBackup(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'backup_type' => ['required', 'in:full,differential,incremental'],
            'encrypt' => ['nullable', 'boolean'],
            'storage_location' => ['required', 'in:primary_local,secondary_offsite_cikarang,immutable_cloud'],
            'encryption_key' => ['nullable', 'string', 'min:8'],
        ]);

        $encrypt = $request->boolean('encrypt', true);
        $result = $this->backupService->createBackup(
            type: $validated['backup_type'],
            encrypt: $encrypt,
            storageLocation: $validated['storage_location'],
            encryptionKey: $validated['encryption_key'] ?? null
        );

        return redirect()->route('backup.index')
            ->with('success', "ARSIP CADANGAN SUKSES DIBUAT: {$result['message']}");
    }

    /**
     * Menjalankan pengujian restorasi berkala & validasi kesesuaian informasi
     */
    public function testRestore(Request $request, BackupLog $backup): RedirectResponse
    {
        $encryptionKey = $request->input('encryption_key');
        $result = $this->backupService->testAndVerifyBackup($backup->id, $encryptionKey);

        if ($result['success']) {
            return redirect()->route('backup.index')
                ->with('success', "UJI RESTORASI BERKALA BERHASIL: Arsip [{$backup->backup_name}] terbukti valid, integritas SHA-256 cocok, dan siap dipulihkan ke situs DRC.");
        }

        return redirect()->route('backup.index')
            ->with('error', "UJI RESTORASI GAGAL: {$result['report']}");
    }

    public function download(BackupLog $backup): BinaryFileResponse|RedirectResponse
    {
        $fullPath = storage_path('app/'.$backup->file_path);

        if (! File::exists($fullPath)) {
            return back()->with('error', 'Berkas fisik backup tidak ditemukan pada storage server.');
        }

        return response()->download($fullPath, $backup->backup_name, [
            'Content-Type' => 'application/octet-stream',
        ]);
    }
}
