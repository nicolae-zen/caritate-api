<?php

namespace App\Http\Controllers\Auth;

use App\Models\OtpCode;
use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    /**
     * @group Autentificare
     *
     * Trimite un cod OTP către utilizator, folosind numărul de telefon.
     *
     * @bodyParam phone_number string required Numărul de telefon al utilizatorului. Example: 37360000000
     */
    public function sendOtp(Request $request)
    {
        // 1. Validare request
        $request->validate([
            'phone_number' => 'required|string|min:8|max:20',
        ]);

        
        $phoneNumber = $request->phone_number;

        // 2. Generare cod OTP (random 6 cifre)
        $otpCode = random_int(100000, 999999);

        // 3. Stergere coduri OTP existente, pentru acest telefon
        OtpCode::where('phone_number', $phoneNumber)->delete();

        // 4. Salvarea noului cod OTP in baza de date (hashuit)
        OtpCode::create([
            'phone_number' => $phoneNumber,
            'code_hash' => Hash::make($otpCode),
            'expires_at' => Carbon::now()->addMinutes(5), // Va fi valabil doar 5 minute
            'attempts' => 0,
        ]);

        // 5. Returneaza raspunsul JSON cu codul OTP (pentru test. In realitate se va transmite prin SMS, pe numarul introdus)
        return response()->json([
            'message' => 'OTP generat cu succes',
            'phone_number' => $phoneNumber,
            'otp_code' => $otpCode, // Pe prod, se transmite doar prin SMS
        ], 200);

    }

    /**
     * @group Autentificare
     *
     * Verifica codul OTP, si creaza/logheaza utilizatorul
     *
     * @bodyParam phone_number string required Numărul de telefon indicat anterior.
     * @bodyParam otp_code int required Codul OTP, primit prin SMS. Example: 123456
     */
    public function verifyOtp(Request $request)
    {
        // 1. Validare date primite
        $request->validate([
            'phone_number' => 'required|string|min:8|max:20',
            'otp_code' => 'required|digits:6',
        ]);

        // $phoneNumber = $request->phone_number;
        $phoneNumber = $request->phone_number;
        $otpCodeInput = $request->otp_code;

        // 2. Cauta OTP-ul in baza de date
        $otpRecord = OtpCode::where('phone_number', $phoneNumber)->where('expires_at', '>', now())->first();

        if(!$otpRecord)
        {
            return response()->json([
                'message' => 'Codul OTP expirat sau inexistent.',
            ], 400);
        }

        // 3. Verificam daca codul introdus se potriveste
        if(!Hash::check($otpCodeInput, $otpRecord->code_hash))
        {
            // Cod gresit -> incrementam incercarile
            $otpRecord->increment('attempts');

            // Daca sunt mai mult de 5 incercari, stergem codul
            if($otpRecord->attempts >= 5)
            {
                $otpRecord->delete();
            }

            return response()->json([
                'message' => 'Cod OTP gresit.',
            ], 401);
        }

        // 4. Cod corect -> stergem OTP-ul
        $otpRecord->delete();

        // 5. Cautam sau cream utilizatorul
        $user = User::firstOrCreate(
            ['phone_number' => $phoneNumber],
            [
                'name' => null,
                'email' => null,
                'is_admin' => false,
                'total_donated' => 0,
            ]
        );
        
        // 6. Actualizam ultima autentificare
        $user->update([
            'last_login' => now(),
        ]);

        // 7. Generam token JWT de autentificare
        $token = auth('api')->login($user);

        return response()->json([
            'message' => 'Autentificare reusita',
            'token' => $token,
            'user' => $user,
        ], 200);
    }

    /**
     * @group Autentificare
     * 
     * Returneaza un nou acces token (JWT) folosind refresh.
     * 
     * @authenticated
     */
    public function refresh()
    {
        try {
            /** @var JWTGuard $guard */
            $guard = auth('api');
            $newToken = $guard->refresh();

            return response()->json([
                'message' => 'Token reimprospatat cu succes',
                'token' => $newToken,
            ], 200);
        } catch(JWTException $e) {
            return response()->json([
                'message' => 'Eroare la reimprospatarea token-ului',
                'error' => $e->getMessage(),
            ], 401);
        }
    }

    /**
     * @group Autentificare
     * 
     * Logout utilizator
     * 
     * 
     * @authenticated
     */
    public function logout(Request $request)
    {
        try {
            auth('api')->logout(); // Invalidam token-ul JWT

            return response()->json([
                'message' => 'Te-ai deconectat cu succes.'
            ], 200);
        } catch (JWTException $e) {
            return response()->json([
                'message' => 'Nu s-a putut face deconectarea.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
