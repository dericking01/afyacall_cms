<?php

namespace App\Models;

use Exception;
use GuzzleHttp\Client;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'mobile',
        'password',
        'last_login_at',
        'last_login_ip',
        'status',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * generate OTP and send sms.
     *
     * @return response()
     */
    public function generateCode()
    {
        $code = rand(100000, 999999);

        UserCode::updateOrCreate([
            'user_id' => auth()->user()->id,
            'code' => $code
        ]);

        $receiverNumber = auth()->user()->mobile;
        $message = 'Your Afyacall Login OTP code is ' . $code;

        logger()->info($message);

        //        try {
        //            $client = new Client();
        //            $client->request('GET', 'http://192.168.1.10:6013/cgi-bin/sendsms', [
        //                'query' => [
        //                    'username' => 'afya',
        //                    'password' => 'Afya4017',
        //                    'from' => '15723',
        //                    'to' => '+' . $receiverNumber,
        //                    'text' => $message,
        //                ]
        //            ]);
        //        } catch (Exception $e) {
        //            logger()->error('OTP sending failure', ['exception' => $e]);
        //        }
    }
}
