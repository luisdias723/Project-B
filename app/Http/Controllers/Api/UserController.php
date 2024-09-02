<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\PermissionResource;
use App\Http\Resources\UserResource;
use App\Extra\JsonResponse;
use App\Models\Permission;
use App\Models\Payment;
use App\Models\Role;
use App\Models\User;
use App\Models\Plan;
use App\Models\PlanSession;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPassword;
use App\Mail\NewUserPassword;
use App\Mail\UserRegistered;
use App\Mail\RegistrationFinished;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class UserController extends BaseController
{
    const ITEM_PER_PAGE = 25;

     /**
     * Display a listing of the user resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response|ResourceCollection
       
     */
          
       


    public function index(Request $request)
    {
        $searchParams = $request->all();
        $userQuery = User::query();
        $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);
        $role = Arr::get($searchParams, 'role', '');
        $keyword = Arr::get($searchParams, 'keyword', '');
        $rating_filter = Arr::get($searchParams, 'rating', '');
        
        $currentUser = Auth::user();
        $roles = $currentUser->getRoleNames()->toArray();

        if(($role == "" || $role == "admin" || $role == "gestor") && !$currentUser->isAdmin()){
            return response()->json(['error' => 'Não possui permissões ver este conteúdo'], 403);
        }
        
        // search by role
        if (!empty($role)) {
            $userQuery->whereHas('roles', function($q) use ($role) { $q->where('name', $role); });
        }


        // search by keyword
        if (!empty($keyword)) {
            $userQuery->where(function($query) use ($keyword){
                    $query->where('name', 'LIKE', '%' . $keyword . '%');
                    $query->orWhere('email', 'LIKE', '%' . $keyword . '%');
            });
        }


        return UserResource::collection($userQuery->paginate($limit));
    }

    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $params = $request->all();

        $validator = Validator::make(
            $request->all(),
            array_merge(
                $this->getValidationRules(),
                [
                    'password' => (($params['set_password'] == 'no') ? 'sometimes' : 'required|min:6'),
                    'confirmPassword' => 'same:password',
                    'vac_days' => (($params['set_vacation'] == 'NULL') ? 'sometimes' : 'required|min:0|max:50'),
                ]
            )
        );

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 403);
        } else {
            $user = User::create([
                'name' => $params['name'],
                'email' => $params['email'],
                'password' => Hash::make($params['password'] ?? Str::random(15)),
                'vac_days'=> $params['vac_days'],
            ]);

            $params['roles'] = isset($params['roles']) ? $params['roles'] : 4;
            $role = Role::findByName($params['role']);
            $user->syncRoles($role);

            if($params['set_password'] == 'no'){
                // generate a reset password token
                $token = Str::random(60);

                $insertedReset = DB::table('password_resets')->insert([
                    'email' => $user['email'],
                    'token' => $token
                ]);
                
                $url = env('APP_URL') . '#/password?id=' . $token;
                $user['url'] = $url;
                
                if ($insertedReset) {
                    return $this->sendNewUserPasswordMail($user);
                }

            } else {
                $this->sendRegistrationFinishedEmail($params['email'], env('APP_URL'));
            }

            return new UserResource($user);
        }
    }

    public function register(Request $request)
    {
        // TODO: Put roles to new users
        $validator = Validator::make(
            $request->all(),
                $this->getValidationRules(true),
                [
                    'password' => ['required', 'min:6'],
                    'confirmPassword' => 'same:password',
                ]
        );

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 403);
        } else {

            $params = $request->all();
            
            $existsEmail = isset($params['email']) ? User::where('email', 'like', $params['email'])->first() : null;

            if(isset($existsEmail) && !empty($existsEmail)) {
                return response()->json(['errors' => 'Já existe um utilizador com o email introduzido'], 403);
            }

            Log::info('utilizador registado');

            // return new UserResource($user);
            return true;
        }
    }
    
    /**
     * Display the specified resource.
     *
     * @param  User $user
     * @return UserResource|\Illuminate\Http\JsonResponse
     */
    public function show(User $user)
    {
        $userQuery = User::where('users.id', $user->id);
        
        $userQuery->select('users.*', 'vacation_days.num_days as num_days')
                ->leftJoin('vacation_days', 'users.id', 'vacation_days.user_id');

        return new UserResource($userQuery->first());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param User    $user
     * @return UserResource|\Illuminate\Http\JsonResponse
     */
    public function update(Request $request, User $user)
    {
        if ($user === null) {
            return response()->json(['error' => 'Utilizador não encontrado'], 404);
        }

        $cur_user = Auth::user();

        if ($user->isAdmin() && !$cur_user->isAdmin()) {
            return response()->json(['error' => 'Admin não pode ser modificado'], 403);
        }

        $currentUser = Auth::user();
        if (!$currentUser->isAdmin()
            && $currentUser->id !== $user->id
            && !$currentUser->hasPermission(\App\Extra\Acl::PERMISSION_USER_MANAGE)
        ) {
            return response()->json(['error' => 'Permissão negada'], 403);
        }

        $validator = Validator::make(
            $request->all(),
            array_merge(
                $this->getValidationRules(false),
                [
                    'newPassword' => ['sometimes', 'nullable', 'min:6'],
                    'confirmNewPassword' => ['sometimes', 'same:newPassword'],
                ]
            )
        );

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 403);
        } else {
            $user->name = $request->get('name');
            $user->email = $request->get('email');
            $user->phone = $request->get('phone');
            $user->mobile_code = $request->get('mobile_code');
            $user->vac_days = $request->get('vac_days');

            $user->birthday = $request->get('birthday');
            $user->address = $request->get('address');
            $user->zipcode = $request->get('zipcode');
            $user->city = $request->get('city');
            $user->country = $request->get('country');
               

            if($request->get('newPassword')) {
                $user->password = Hash::make($request->get('newPassword'));
            }

            $user->update();
            return new UserResource($user);
        }
    }


    public function updateActive(Request $request){

        $validator = Validator::make(
            $request->all(),
            array_merge(
                [
                    'active' => ['required'],
                    'user_id' => ['required'],
                ]
            )
        );

        $params = $request->all();

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 403);
        } else {
            $is_admin = User::where('id', $params['user_id'])->whereHas('roles', function($q) { $q->where('name', 'admin'); })->exists();

            if ($is_admin) {
                return response()->json(['error' => 'Admin não pode ser modificado'], 403);
            }
    
          
            User::where('id', $params['user_id'])->update(['active' => $params['active']]);

            return true;
        }
          
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param User    $user
     * @return UserResource|\Illuminate\Http\JsonResponse
     */
    public function updatePermissions(Request $request, User $user)
    {
        if ($user === null) {
            return response()->json(['error' => 'User not found'], 404);
        }

        if ($user->isAdmin()) {
            return response()->json(['error' => 'Admin can not be modified'], 403);
        }

        $permissionIds = $request->get('permissions', []);
        $rolePermissionIds = array_map(
            function($permission) {
                return $permission['id'];
            },

            $user->getPermissionsViaRoles()->toArray()
        );

        $newPermissionIds = array_diff($permissionIds, $rolePermissionIds);
        $permissions = Permission::allowed()->whereIn('id', $newPermissionIds)->get();
        $user->syncPermissions($permissions);
        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param User    $user
     * @return UserResource|\Illuminate\Http\JsonResponse
     */
    public function updateGroupId(Request $request, User $user)
    {
        if ($user === null) {
            return response()->json(['error' => 'User not found'], 404);
        }

        if ($user->isAdmin()) {
            return response()->json(['error' => 'Admin can not be modified'], 403);
        }

        $group_id = $request->get('group_id', null);
        User::where('id', $user->id)->update(array('group_id' => $group_id));

        // $user->group_id = $group_id;

        return new UserResource($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  User $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        if ($user->isAdmin()) {
            response()->json(['error' => 'Ehhh! Não pode eliminar utilizadores admin'], 403);
        }

        $currentUser = Auth::user();

        $roles = $currentUser->getRoleNames()->toArray();

        if (!in_array(['admin'], $roles)){
            response()->json(['error' => 'Não foi possível concluír o pedido.'], 403);
        }

        try {
            $user->delete();
        } catch (\Exception $ex) {
            response()->json(['error' => $ex->getMessage()], 403);
        }

        return response()->json(null, 204);
    }

    /**
     * Get permissions from role
     *
     * @param User $user
     * @return array|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function permissions(User $user)
    {
        try {
            return new JsonResponse([
                'user' => PermissionResource::collection($user->getDirectPermissions()),
                'role' => PermissionResource::collection($user->getPermissionsViaRoles()),
            ]);
        } catch (\Exception $ex) {
            response()->json(['error' => $ex->getMessage()], 403);
        }
    }

    /**
     * @param bool $isNew
     * @return array
     */
    private function getValidationRules($isNew = true)
    {
        return [
            'name' => 'required',
            'email' => $isNew ? 'required|email|unique:users' : 'required|email',
            'roles' => [
                'required',
                'array'
            ],
        ];
    }


    public function updateAvatar(Request $request, User $user)
    {
        $validator = Validator::make(
            $request->all(),
            array(
                'avatar'   => ['image', 'dimensions:max_width=700,max_height=700'],
            )
        );

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 403);
        } else {
        
            $file = $request->file('avatar');
            $name = '/avatar/' . uniqid() . '.' . $file->extension();
            $file->storePubliclyAs('public', $name);

            $user->avatar = $name;
            $user->save();
            
            return new userResource($user);
        }
    }

    public function getAllClients()
    {
        $clients = User::query()->select('id', 'name')->where('type', 'cliente');
        
        foreach($clients as $client){
            $client['name'] = html_entity_decode($client['name']);
        }

        return array('data' => $clients->get());
    }

    // Get link to Reset password
    public function getReset(Request $request){

        $validator = Validator::make(
            $request->all(),
            array(
                'email'   => ['required'],
            )
        );

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 403);
        } else {
            $params = $request->all();
            $user = User::where('email', $params['email'])->first();
            if (isset($user) && $user['id']) {
                return $this->generateURL($user);
            } else {
                return response()->json(['errors' => 'Não existe um utilizador com esse email'], 403);
            }
        }
    }

    // Generate token to send to user on email
    private function generateURL($user) {
        $token = Str::random(60);

        $insertedReset = DB::table('password_resets')->insert([
            'email' => $user['email'],
            'token' => $token
        ]);
        $url = env('APP_URL') . '#/password?id=' . $token;
        $user['url'] = $url;
        
        if ($insertedReset) {
            return $this->sendResetPasswordMail($user);
        }
    }

    // sends an email to reset Password
    private function sendResetPasswordMail($user) {

        $details = [
            'email' => $user['email'],
            'username' => $user['name'],
            'url' => $user['url']
        ];

        Mail::to($user['email'])
        ->send(new ResetPassword($details));
        
        return 'Email enviado';
    }

    private function sendNewUserPasswordMail($user) {

        $details = [
            'email' => $user['email'],
            'username' => $user['name'],
            'url' => $user['url']
        ];

        Mail::to($user['email'])->send(new NewUserPassword($details));
        
        return 'Email enviado';
    }

    private function sendRegistrationFinishedEmail($email, $url) {
    
        $details = [
            'title' => 'Registo efetuado',
            'subtitle' => 'Pode agora aceder à nossa plataforma',
            'email' => $email,
            'url' => $url
        ];

        // Mail::to($email)->send(new RegistrationFinished($details));
        
        Log::info('Email de registo enviado para ' . $email);

        // return 'Email enviado';
        return true;
    }

    // sends an email invitation to all participants
    private function sendRegistrationStartedEmail($email, $url) {
    

        $details = [
            'title' => 'Obrigado',
            'subtitle' => 'por se registar na nossa plataforma',
            'email' => $email,
            'url' => $url
        ];

        Mail::to($email)->send(new UserRegistered($details));
        
        Log::info('Email de registo enviado para ' . $email);

        // return 'Email enviado';
        return true;
    }

    public function resetPassword(Request $request) {
        $validator = Validator::make(
            $request->all(),
            array(
                'email'   => ['required'],
                'password'   => ['required','min:6'],
                'confirm_password'   => ['required','same:password','min:6'],
                'token'   => ['required'],
            )
        );

        if ($validator->fails()) {
            return response()->json(['errors' => 'Não foi possível validar os campos introduzidos'], 403);
        } else {
            $params = $request->all();

            $tokenGenerated = DB::table('password_resets')->where('token', 'like', $params['token'])->where('email', 'like', $params['email'])->first();
            // dd(isset($tokenGenerated) && !empty($tokenGenerated));
            if (isset($tokenGenerated) && !empty($tokenGenerated)) {
                $user = User::where('email', '=', $params['email'])->update([
                    'password' => Hash::make($params['password']),
                ]);
                $tokenGenerated = DB::table('password_resets')->where('token', '=', $params['token'])->where('email', '=', $params['email'])->delete();
                return $user;
            } else {
                return 0;
            }
        }
    }

    /**
     * Set a new user avatar.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function changeAvatar(Request $request){

        $currentUser = Auth::user();
        $roles = $currentUser->getRoleNames()->toArray();

        if($currentUser->id !== intval($request->user) && (!in_array('admin', $roles) && !in_array('gestor', $roles))){
            return response()->json(['error' => 'Não possui permissões alterar esta informação'], 403);
        }

        $params = $request->all();

        $request->validate([
            'file' => 'required|mimes:jpg,jpeg,png|max:20480',
            'user' => 'required'
        ]);

        if ($request->file()) {

            $file_name = time() . '_' . $params['title'] . '.' . $request->file('file')->extension();
            $file_path = $request->file('file')->storeAs('avatars', $file_name, 'public');
            $newPath = '/storage/' . $file_path;

            $user = User::where('id', '=', $request->user)->update([
                'profile_photo_path' => $newPath
            ]);

            return $newPath;
        }
        return null;
    }

    public function getUsers(Request $request){

        $searchParams = $request->all();
       
        $UserQuery = User::query()->select('id','name');

        return UserResource::collection($UserQuery->get());
        
    }

}