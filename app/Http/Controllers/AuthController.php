<?php

namespace App\Http\Controllers;

use App\Http\Requests\PasswordChangeRequest;
use App\Http\Requests\PasswordResetRequest;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegistrationRequest;
use App\Mail\DefaultEmail;
use App\Models\PasswordResets;
use App\Models\Roles;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

/**
 * Class AuthController
 * @package App\Http\Controllers
 *
 * @OA\Tag(
 *     name="Auth",
 *     description="Авторизационные методы"
 * )
 */
class AuthController extends Controller
{
    /**
     * Регистрация пользователя
     *
     * @OA\Post(
     *     path="/api/auth/register",
     *     operationId="registration",
     *     tags={"Auth"},
     *     summary="Регистрация нового пользователя",
     *     description="Регистрация пользователя",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"email", "password", "name"},
     *                 @OA\Property(property="email", type="string", format="email"),
     *                 @OA\Property(property="password", type="string", format="password"),
     *                 @OA\Property(property="name", type="string"),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(response=200, description="Successful", @OA\JsonContent()),
     *     @OA\Response(response=400, description="Validate error", @OA\JsonContent())
     * )
     *
     * @param UserRegistrationRequest $request
     * @return mixed
     */
    public function registration(UserRegistrationRequest $request): mixed
    {
        $request->validated();
        $request->merge(['password' => Hash::make($request->password)]);

        $user = User::create($request->all());

        if(!$user) {
            return Response::error('Ошибка при регистрации');
        }

        $clientRole = Roles::where('slug', 'client')->first();
        $user->assignRole($clientRole);

        $result = $user->toArray();
        $result['token'] = $user->createToken('authToken')->plainTextToken;

        return Response::success('Регистрация прошла успешно', $result);
    }

    /**
     * Авторизация пользователя
     *
     * @OA\Post(
     *     path="/api/auth/login",
     *     operationId="login",
     *     tags={"Auth"},
     *     summary="Авторизация пользователя",
     *     description="Авторизация пользователя",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"email", "password"},
     *                 @OA\Property(property="email", type="string", format="email"),
     *                 @OA\Property(property="password", type="string", format="password"),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(response=200, description="Successful", @OA\JsonContent()),
     *     @OA\Response(response=400, description="Validate error", @OA\JsonContent())
     * )
     *
     * @param UserLoginRequest $request
     * @return mixed
     */
    public function login(UserLoginRequest $request): mixed
    {
        $request->validated();

        $password = $request->post('password');
        $email = $request->post('email');

        $user = User::where('email', $email)->first();

        if($user && Hash::check($password, $user->password)) {
            $token = $user->createToken('authToken')->plainTextToken;
            if($token) {
                return Response::success('Успешная авторизация', $token);
            }
        }

        return Response::error('Неправильный логин или пароль');
    }

    /**
     * Создает заявку на изменение пароля и отправляет письмо на почту
     *
     * @OA\Post(
     *     path="/api/auth/reset-password",
     *     operationId="reset-password",
     *     tags={"Auth"},
     *     summary="Сброс пароля",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *             required={"email"},
     *                 @OA\Property(property="email", type="string", format="email"),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(response=200, description="Successful", @OA\JsonContent()),
     *     @OA\Response(response=400, description="Validate error", @OA\JsonContent())
     * )
     * @param PasswordResetRequest $request
     * @return \Illuminate\Http\Response
     */
    public function resetPassword(PasswordResetRequest $request): \Illuminate\Http\Response
    {
        $request->validated();
        $email = $request->post('email');
        $user = User::where('email', $email)->first();

        if(!$user) {
            return Response::error('Пользователь не найден');
        }

        $token = Str::orderedUuid();

        PasswordResets::where('user_id', $user->id)->delete();

        $application = PasswordResets::create([
            'email' => $email,
            'user_id' => $user->id,
            'token' => $token,
            'expired_at' => Carbon::now()->addMinutes(30),
            'created_at' => Carbon::now(),
        ]);

        if(!$application) {
            return Response::error('Запрос на сброс пароля не был создан');
        }

        $params = [
            'token' => $token,
            'template' => 'password.password_reset'

        ];

        Mail::to($email)->send(new DefaultEmail($params));

        return Response::success('Письмо отправлено на почту');
    }

    /**
     * Проверяет существование заявки по совпадению токенов
     *
     * @OA\Get(
     *     path="/api/auth/check-password-token",
     *     operationId="check-password-token",
     *     tags={"Auth"},
     *     summary="Проверяет существование заявки по совпадению токенов",
     *     description="",
     *     @OA\Parameter(
     *         name="token",
     *         in="query",
     *         description="Token: {reset-token}",
     *         required=true,
     *     ),
     *     @OA\Response(response=200, description="Successful", @OA\JsonContent()),
     *     @OA\Response(response=500, description="Query server error", @OA\JsonContent())
     * )
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function checkPassToken(Request $request): \Illuminate\Http\Response
    {
        $token = $request->get('token');

        if($token) {
            $application = PasswordResets::where([
                ['token', $token],
                ['expired_at', '>', Carbon::now()]
            ])->first();

            if($application) {
                return Response::success('Токен совпадает');
            }
        }

        return Response::error('Токен не существует');
    }

    /**
     * Создание нового пароля
     *
     * @OA\Post(
     *     path="/api/auth/change-password",
     *     operationId="change-password",
     *     tags={"Auth"},
     *     summary="Смена пароля",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *             required={"token", "password", "password_confirmation"},
     *                 @OA\Property(property="token", type="string"),
     *                 @OA\Property(property="password", type="string", format="password"),
     *                 @OA\Property(property="password_confirmation", type="string", format="password"),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(response=200, description="Successful", @OA\JsonContent()),
     *     @OA\Response(response=400, description="Validate error", @OA\JsonContent())
     * )
     * @param PasswordChangeRequest $request
     * @return \Illuminate\Http\Response
     */
    public function changePassword(PasswordChangeRequest $request): \Illuminate\Http\Response
    {
        $request->validated();
        $token = $request->post('token');
        $password = $request->post('password');

        $application = PasswordResets::where([
            ['token', $token],
            ['expired_at', '>', Carbon::now()]
        ])->first();

        if(!$application) {
            return Response::error('Заявка не найдена, либо истек ее срок действия');
        }

        $user = User::find($application->user_id);

        if(!$user) {
            return Response::error('Пользователь не найден');
        }

        $user->password = Hash::make($password);
        $user->save();
        $application->delete();

        return Response::success('Пароль был изменен');
    }

    /**
     * Выход пользователя из сессии
     *
     * @OA\Post(
     *     path="/api/auth/logout",
     *     operationId="logout",
     *     tags={"Auth"},
     *     summary="Выход пользователя из сессии",
     *     description="Выход пользователя из сессии (уничтожение токена)",
     *
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=200, description="Successful", @OA\JsonContent()),
     *     @OA\Response(response=400, description="Session error", @OA\JsonContent()),
     * )
     *
     * @param Request $request
     * @return mixed
     */
    public function logout(Request $request): mixed
    {
        $user = $request->user();
        $token = $user->currentAccessToken();

        if(method_exists($token, 'delete')) {
            $token->delete();

            return Response::success('Успешный выход');
        }

        return Response::error('Сессия не активна');
    }
}
