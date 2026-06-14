<?php

namespace App\Jobs\Exports\BarangKI;

use App\Exports\BarangKI\BarangKIExport;
use App\Models\Auth\UserModel;
use App\Models\Auth\Notification\NotificationModel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;

class ExportBarangKIJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1; // Cegah retry yang bisa bikin duplikat
    public $timeout = 300;

    protected $filters;
    protected $user;
    protected $format;
    protected $notificationId;

    public function __construct($filters, UserModel $user, $format = 'excel', $notificationId = null)
    {
        $this->filters = $filters;
        $this->user = $user;
        $this->format = $format;
        $this->notificationId = $notificationId;
    }

    /**
     * Jalankan job
     */
    public function handle()
    {
        try {
            Log::info('ExportBarangKIJob dimulai', [
                'user_id' => $this->user->id,
                'notification_id' => $this->notificationId,
                'format' => $this->format
            ]);

            $this->deleteOldFiles();
            $this->deleteOldNotifications();

            $timestamp = now()->format('Y-m-d_H-i-s');
            $extension = $this->getFileExtension();
            $filename = "barang_ki_{$this->user->id}_{$timestamp}.{$extension}";
            $disk = 'public';
            $filePath = "exports/{$filename}";

            // Generate export
            switch ($this->format) {
                case 'csv':
                    Excel::store(new BarangKIExport($this->filters), $filePath, $disk, \Maatwebsite\Excel\Excel::CSV);
                    break;
                case 'pdf':
                    Excel::store(new BarangKIExport($this->filters), $filePath, $disk, \Maatwebsite\Excel\Excel::DOMPDF);
                    break;
                default:
                    Excel::store(new BarangKIExport($this->filters), $filePath, $disk);
                    break;
            }

            $publicUrl = Storage::url($filePath);

            // Update notifikasi: SUKSES
            $this->updateNotificationSuccess($publicUrl, $filename);

            Log::info('ExportBarangKIJob berhasil', ['file' => $publicUrl]);
        } catch (\Exception $e) {
            Log::error('ExportBarangKIJob gagal', [
                'user_id' => $this->user->id,
                'notification_id' => $this->notificationId,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            // Update notifikasi: GAGAL
            $this->updateNotificationFailure($e);

            // Tetap report ke Sentry / log
            report($e);
        }
    }

    /**
     * Update notifikasi saat sukses
     */
    private function updateNotificationSuccess($url, $filename)
    {
        if (!$this->notificationId) return;

        $notification = NotificationModel::find($this->notificationId);
        if (!$notification) {
            Log::warning('Notifikasi tidak ditemukan saat sukses', ['id' => $this->notificationId]);
            return;
        }

        $notification->update([
            'data' => [
                'message' => 'Export BarangKI berhasil diselesaikan.',
                'title' => 'Export Completed',
                'filename' => $filename,
                'file_path' => $url,
                'format' => $this->format,
                'download_url' => $url,
                'success' => true,
            ],
            'path' => $url,
            'read_at' => null,
        ]);
    }

    /**
     * Update notifikasi saat gagal
     */
    private function updateNotificationFailure(\Exception $e)
    {
        if (!$this->notificationId) return;

        $notification = NotificationModel::find($this->notificationId);
        if (!$notification) {
            Log::warning('Notifikasi tidak ditemukan saat gagal', ['id' => $this->notificationId]);
            return;
        }

        $notification->update([
            'data' => [
                'message' => 'Export gagal: ' . Str::limit($e->getMessage(), 200),
                'title' => 'Export Failed',
                'error' => true,
                'exception' => class_basename($e),
                'line' => $e->getLine(),
                'file' => basename($e->getFile()),
            ],
            'read_at' => null,
        ]);
    }

    /**
     * Hapus file lama (> 1 hari)
     */
    private function deleteOldFiles()
    {
        try {
            $storage = Storage::disk('public');
            $directory = 'exports';
            if (!$storage->exists($directory)) return;

            $files = $storage->files($directory);
            $cutoffTime = now()->subDay();

            foreach ($files as $file) {
                $lastModified = Carbon::createFromTimestamp($storage->lastModified($file));
                if ($lastModified->lt($cutoffTime)) {
                    $storage->delete($file);
                    Log::info('File lama dihapus', ['file' => $file]);
                }
            }
        } catch (\Exception $e) {
            Log::warning('Gagal hapus file lama', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Hapus notifikasi lama (> 7 hari)
     */
    private function deleteOldNotifications()
    {
        try {
            $deleted = NotificationModel::whereJsonContains('data->filename', 'barang_ki_%')
                ->where('created_at', '<', now()->subDays(7))
                ->delete();

            if ($deleted) {
                Log::info('Notifikasi export lama dihapus', ['jumlah' => $deleted]);
            }
        } catch (\Exception $e) {
            Log::warning('Gagal hapus notifikasi lama', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Ekstensi file
     */
    private function getFileExtension(): string
    {
        return match ($this->format) {
            'csv' => 'csv',
            'pdf' => 'pdf',
            default => 'xlsx',
        };
    }
}
