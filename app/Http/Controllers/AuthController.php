<?php


namespace App\Http\Controllers;


use App\Http\Helpers\Helpers;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * ✅ Enregistrement d'un nouvel utilisateur
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request)
    {
        logger($request->all());
        try {
            $data = $request->validate([
                'fullname' => 'required|string|max:255',
                'email' => 'nullable|email|unique:users,email',
                'phone' => 'required|string|unique:users,phone',
                'password' => 'required|string|min:6',
            ]);

            $user = User::create([
                'name' => $data['fullname'],
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'],
                'password' => Hash::make($data['password']),
                'role' => 'user'
            ]);

            $token = $user->createToken('pos-app')->plainTextToken;

            return Helpers::success([
                'user_id'  => $user->id,
                'access_token' => $token,
                'user_name'=>$user->name,
                'user_role'=>$user->role,
                'expires_in'=>3600

            ]);
        }catch (\Exception $e) {
            logger($e);
            return Helpers::error($e->getMessage());
        }

    }


    /**
     * ✅ Connexion
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function login(Request $request)
    {
        try {
            $request->validate([
                'phone'    => 'required',
                'password' => 'required'
            ]);

            $user = User::where('phone', $request->phone)->first();

            logger($user);
            if (! $user || ! Hash::check($request->password, $user->password)) {
                throw ValidationException::withMessages([
                    'phone' => ['Les identifiants sont incorrects.'],
                ]);
            }

            // Supprime les anciens tokens (optionnel, pour éviter multi-sessions)
            $user->tokens()->delete();

            $token = $user->createToken('pos-app')->plainTextToken;

            return Helpers::success([
                'user_id'  => $user->id,
                'access_token' => $token,
                'user_name'=>$user->name,
                'user_role'=>$user->role,
                'expires_in'=>3600

            ]);
        }catch (\Exception $exception){
            logger($exception);
            return Helpers::error($exception->getMessage());
        }

    }

    /**
     * ✅ Déconnexion
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete(); // Supprime tous les tokens
        return Helpers::success( 'Déconnecté avec succès');
    }

    /**
     * ✅ Profil de l'utilisateur connecté
     * @param Request $request
     * @return JsonResponse
     */
    public function profile(Request $request)
    {
        return Helpers::success($request->user());
    }
    public function updateProfile(Request $request)
    {

        $request->validate([
            'fullName' => 'required|string',
            'email' => 'required|string',
            'phone' => 'required|string',
        ]);
        $customer = Auth::user();

        if (!$customer) {
            return Helpers::error('$customer est requis', 400);
        }
        $customer->update([
            'name' => $request->fullName,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);


        return Helpers::success([
            'name' => $customer->name,
            'phone' => $customer->phone,
            'email' => $customer->email,
            'balance' => $customer->sold,
            'date_birth' => date('Y-m-d')
        ]);
    }

    public function changePassword(Request $request)
    {

        $request->validate([
            'new_password' => 'required|string',
            'password' => 'required|string',
        ]);
        $customer = Auth::user();

        if (!$customer) {
            return Helpers::error('$customer est requis', 400);
        }
        if (!Auth::attempt(['phone' => $customer->phone, 'password' => $request->password])) {
            return Helpers::error('Invalid credentials');

        }
        $customer->update([
            'password' => Hash::make($request->new_password)

        ]);

        return Helpers::success([
            'name' => $customer->name,
            'phone' => $customer->phone,
            'email' => $customer->email,
            'balance' => $customer->sold,
            'date_birth' => date('Y-m-d')
        ]);
    }
}

