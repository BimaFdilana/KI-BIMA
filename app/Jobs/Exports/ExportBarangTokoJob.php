<?php

namespace App\Jobs\Exports;

use App\Exports\BarangTokoExport;
use App\Models\Auth\UserModel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Auth\Notification\NotificationModel; // Pastikan model notifikasi kamu bernama NotificationModel

class ExportBarangTokoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filters;
    protected $user;
    protected $format;
    protected $notificationId; // Tambahkan ini

    public function __construct($filters, UserModel $user, $format = 'excel', $notificationId = null)
    {
        $this->filters = $filters;
        $this->user = $user;
        $this->format = $format;
        $this->notificationId = $notificationId; // Simpan ID notifikasi
    }

    public function handle()
    {
        try {
            $timestamp = now()->format('Y-m-d_H-i-s');
            $extension = $this->getFileExtension();
            $filename = "barang_toko_{$this->user->id}_{$timestamp}.{$extension}";
            $disk = 'public';
            $filePath = "exports/{$filename}"; // Folder exports di disk public

            // Generate export
            switch ($this->format) {
                case 'csv':
                    Excel::store(new BarangTokoExport($this->filters), $filePath, $disk, \Maatwebsite\Excel\Excel::CSV);
                    break;
                case 'pdf':
                    Excel::store(new BarangTokoExport($this->filters), $filePath, $disk, \Maatwebsite\Excel\Excel::DOMPDF);
                    break;
                default: // excel
                    Excel::store(new BarangTokoExport($this->filters), $filePath, $disk);
                    break;
            }

            $publicUrl = Storage::url($filePath);

            // Update notifikasi yang sudah ada
            if ($this->notificationId) {
                $notification = NotificationModel::find($this->notificationId);
                if ($notification) {
                    $notification->update([
                        'data' => [
                            'message' => 'Export Barang Toko berhasil diselesaikan.',
                            'title' => 'Export Completed',
                            'filename' => $filename,
                            'file_path' => $publicUrl,
                            'format' => $this->format,
                            'download_url' => $publicUrl,
                        ],
                        'path' => $publicUrl,
                        'read_at' => null,
                    ]);
                }
            }
        } catch (\Exception $e) {
            // Update notifikasi dengan pesan error
            if ($this->notificationId) {
                $notification = NotificationModel::find($this->notificationId);
                if ($notification) {
                    $notification->update([
                        'data' => [
                            'message' => 'Export gagal: ' . Str::limit($e->getMessage(), 200),
                            'title' => 'Export Failed',
                            'error' => true,
                        ],
                        'read_at' => null,
                    ]);
                }
            }
            throw $e; // tetap log error
        }
    }

    private function getFileExtension()
    {
        return match ($this->format) {
            'csv' => 'csv',
            'pdf' => 'pdf',
            default => 'xlsx',
        };
    }
}
