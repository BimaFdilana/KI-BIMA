<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Auth\UserModel;
use App\Models\Toko\JabatanModel;
use App\Models\Toko\TokoModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use libphonenumber\PhoneNumberUtil;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\NumberParseException;
use Carbon\Carbon;

class ProfileController extends Controller
{
    public function index()
    {
        // Get current authenticated user
        $user = Auth::user();
        $roleName = $user->getRoleNames()->first();
        $role = ucfirst($roleName);
        $permissions = $user->getAllPermissions();

        // Format phone number with country code
        $formattedPhoneNumber = $this->formatPhoneNumber($user->phone_number);

        $formattedPermissions = $permissions->map(function ($permission) {
            $permission->formatted_name = $this->formatPermissionNameAdvanced($permission->name);
            return $permission;
        });


        // Format date of birth - assuming you have a date_of_birth column
        $formattedBirthDate = null;
        if (!empty($user->date_of_birth)) {
            $birthDate = Carbon::parse($user->date_of_birth);
            $formattedBirthDate = $birthDate->translatedFormat('j F Y'); // Will output "1 Juni 2024"
        }

        // Format created_at timestamp
        $formattedCreatedAt = null;
        if ($user->created_at) {
            $createdAt = Carbon::parse($user->created_at);
            $formattedCreatedAt = $createdAt->translatedFormat('j F Y'); // Will output "01:05, 12 Maret 2024"
        }

        // Get user's toko information if they have any
        $toko = $user->tokos()->first(); // Get the first (and only) toko
        $tokoInfo = null;
        if ($toko) {
            // Get jabatan name based on jabatan_id from pivot table
            $jabatan = JabatanModel::find($toko->pivot->jabatan_id);
            $jabatanName = $jabatan ? $jabatan->name : 'Unknown Position';
            $employeeCount = $toko->users()->count();
            $formattedCreatedAt = $createdAt = Carbon::parse($user->created_at)->translatedFormat('j F Y');
            $tokoInfo = [
                'id' => $toko->id,
                'name' => $toko->name,
                'position' => $jabatanName,
                'status' => $toko->status,
                'address' => $toko->address,
                'description' => $toko->description,
                'owner' => $toko->owner,
                'employee_count' => $employeeCount,
                'formattedCreatedAt' => $formattedCreatedAt,
            ];
        }
        return view('auth.user-profile', compact(
            'user',
            'role',
            'formattedPermissions',
            'tokoInfo',
            'formattedPhoneNumber',
            'formattedBirthDate',
            'formattedCreatedAt'
        ));
    }

    private function formatPermissionName($name)
    {
        return collect(preg_split('/[._]/', $name))
            ->filter() // Remove empty strings
            ->map(function ($word) {
                return ucfirst(strtolower($word));
            })
            ->implode(' ');
    }

    // Alternative method with more sophisticated formatting
    private function formatPermissionNameAdvanced($name)
    {
        $words = collect(preg_split('/[._]/', $name))
            ->filter()
            ->map(function ($word) {
                // Handle common abbreviations
                $abbreviations = [
                    'api' => 'API',
                    'ui' => 'UI',
                    'url' => 'URL',
                    'html' => 'HTML',
                    'css' => 'CSS',
                    'js' => 'JavaScript',
                    'id' => 'ID',
                ];

                $lowerWord = strtolower($word);

                if (isset($abbreviations[$lowerWord])) {
                    return $abbreviations[$lowerWord];
                }

                return ucfirst($lowerWord);
            });

        return $words->implode(' ');
    }
    /**
     * Format phone number with proper country code in parentheses
     * 
     * @param string $phoneNumber
     * @return string
     */
    private function formatPhoneNumber($phoneNumber)
    {
        try {
            $phoneUtil = PhoneNumberUtil::getInstance();

            // Bersihkan semua karakter kecuali angka dan +
            $phoneNumber = preg_replace('/[^\d+]/', '', $phoneNumber);

            // Jika mulai dari 0, hapus 0
            if (substr($phoneNumber, 0, 1) === '0') {
                $phoneNumber = substr($phoneNumber, 1);
            }

            // Jika tidak diawali '+', tambah default +62
            if (substr($phoneNumber, 0, 1) !== '+') {
                $phoneNumber = '+62' . $phoneNumber;
            }

            $defaultRegion = 'ID';
            $parsedNumber = $phoneUtil->parse($phoneNumber, $defaultRegion);

            $countryCode = $parsedNumber->getCountryCode();
            $nationalNumber = $parsedNumber->getNationalNumber(); // <-- ini ambil raw number tanpa leading 0

            // Format manual biar fleksibel
            $formatted = '(+' . $countryCode . ') ' . $this->splitPhoneNumber($nationalNumber);

            return $formatted;
        } catch (NumberParseException $e) {
            return $phoneNumber;
        } catch (\Exception $e) {
            return $phoneNumber;
        }
    }

    // Fungsi bantu untuk kasih strip-stripnya
    private function splitPhoneNumber($number)
    {
        // Contoh sederhana: 3-4-4 digit split (812-3456-7891)
        return preg_replace("/(\d{3})(\d{4})(\d{4})/", "$1-$2-$3", $number);
    }
}
