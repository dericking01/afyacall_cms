<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserCode;
use Illuminate\Support\Facades\Session;

class TwoFactorAuthController extends Controller
{
    
  /**
     * index method for 2fa
     *
     * @return response()
     */
    public function index()
    {
        return view('auth.2fa');
    }

    /**
     * validate sms
     *
     * @return response()
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required',
        ]);

        $exists = UserCode::where('user_id', auth()->user()->id)
            ->where('code', $validated['code'])
            ->where('updated_at', '>=', now()->subMinutes(5))
            ->exists();

        if ($exists) {
            Session::put('tfa', auth()->user()->id);
	    return redirect()->route('admin.subscriptions.index');
        }

        return redirect()
            ->back()
            ->with('error', 'You entered wrong OTP code.');
    }
    /**
     * resend otp code
     *
     * @return response()
     */
    public function resend()
    {
        auth()->user()->generateCode();

        return back()
            ->with('success', 'We have resent OTP on your mobile number.');
    }
}
