<?php

namespace App\Jobs;

use App\Imports\BarangKI\BarangKiImport;
use App\Models\User;
use App\Notifications\ImportCompletedNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;

class ImportBarangKiJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filePath;
    protected $importId;
    protected $userId;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The maximum number of seconds the job can run.
     *
     * @var int
     */
    public $timeout = 600; // 10 menit

    /**
     * Create a new job instance.
     */
    public function __construct(string $filePath, string $importId, int $userId)
    {
        $this->filePath = $filePath;
        $this->importId = $importId;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Set status awal
            cache()->put("import_barang_ki_status_{$this->importId}", 'processing', 3600);
            cache()->put("import_barang_ki_progress_{$this->importId}", 0, 3600);

            Log::info("Starting import Barang KI", [
                'import_id' => $this->importId,
                'file_path' => $this->filePath,
                'user_id' => $this->userId
            ]);

            // Cek apakah file exist
            if (!Storage::exists($this->filePath)) {
                throw new \Exception('File tidak ditemukan: ' . $this->filePath);
            }

            // Create import instance
            $import = new BarangKiImport($this->importId);

            // Mulai proses import
            Excel::import($import, $this->filePath);

            // Set status selesai
            cache()->put("import_barang_ki_status_{$this->importId}", 'completed', 3600);
            cache()->put("import_barang_ki_progress_{$this->importId}", 100, 3600);

            // Simpan hasil import
            $result = [
                'success_count' => $import->getSuccessCount(),
                'skipped_count' => $import->getSkippedCount(),
                'error_count' => count($import->getErrors()),
                'errors' => $import->getErrors()
            ];

            cache()->put("import_barang_ki_result_{$this->importId}", $result, 3600);

            Log::info("Import Barang KI completed", [
                'import_id' => $this->importId,
                'result' => $result
            ]);

            // Kirim notifikasi ke user
            $user = User::find($this->userId);
            if ($user) {
                $user->notify(new ImportCompletedNotification(
                    'Barang KI',
                    $result['success_count'],
                    $result['error_count'],
                    $this->importId
                ));
            }
        } catch (\Exception $e) {
            Log::error("Import Barang KI failed", [
                'import_id' => $this->importId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Set status error
            cache()->put("import_barang_ki_status_{$this->importId}", 'failed', 3600);
            cache()->put("import_barang_ki_progress_{$this->importId}", 0, 3600);

            $errorResult = [
                'success_count' => 0,
                'skipped_count' => 0,
                'error_count' => 1,
                'errors' => ['Import gagal: ' . $e->getMessage()]
            ];

            cache()->put("import_barang_ki_errors_{$this->importId}", $errorResult['errors'], 3600);
            cache()->put("import_barang_ki_result_{$this->importId}", $errorResult, 3600);

            // Kirim notifikasi error
            $user = User::find($this->userId);
            if ($user) {
                $user->notify(new ImportCompletedNotification(
                    'Barang KI',
                    0,
                    1,
                    $this->importId,
                    'Import gagal: ' . $e->getMessage()
                ));
            }

            throw $e;
        } finally {
            // Hapus file setelah proses selesai
            if (Storage::exists($this->filePath)) {
                Storage::delete($this->filePath);
                Log::info("Import file deleted: " . $this->filePath);
            }
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Import Barang KI job failed finally", [
            'import_id' => $this->importId,
            'error' => $exception->getMessage()
        ]);

        // Set status failed
        cache()->put("import_barang_ki_status_{$this->importId}", 'failed', 3600);
        cache()->put("import_barang_ki_progress_{$this->importId}", 0, 3600);

        // Cleanup file
        if (Storage::exists($this->filePath)) {
            Storage::delete($this->filePath);
        }
    }
}
