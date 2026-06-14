<?php

namespace App\View\Components\Guest;

use App\Models\Toko\TokoModel;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class DownloadFooter extends Component
{
    public $tokoCount; // Menyimpan jumlah toko

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        // Ambil jumlah toko dari database
        $this->tokoCount = TokoModel::count();  // Menghitung jumlah toko
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('guest.components.download-footer', [
            'tokoCount' => $this->tokoCount, // Kirim jumlah toko ke view
        ]);
    }
}
