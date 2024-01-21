<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function __construct(protected User $repository)
    {

    }

    public function login(string $email, string $senha)
    {
        $credentials = [$email, $senha];

        if (Auth::attempt($credentials)) {
            $user = Auth::user(); // Obtém o usuário autenticado
            return new UserResource($user);
        }

        return back()->withErrors(['email' => 'Invalid login credentials']);
    }

    public function index(){
        $users = $this->repository->all();
        return UserResource::collection($users);
    }

    public function store(StoreUpdateUserRequest $request){
        $data = $request -> validated();

        $data['password'] = bcrypt($request->password);

        $user = $this->repository->create($data);

        return new UserResource($user);
    }

    public function show(string $id){
        $user = $this->repository->find($id);
        // $user = $this->repository->where('id', '=', $id) -> first();
        if(!$user){
            return response() -> json(['message' => 'user not found'], 404);
        }

        // $user = $this->repository->findOrFail($id);

        return new UserResource($user);
    }

    public function update(StoreUpdateUserRequest $request, string $id){
        $user = $this->repository->find($id);

        if(!$user){
            return response() -> json(['message' => 'user not found'], 404);
        }

        $data = $request -> validated();

        if($request -> password)
            $data['password'] = bcrypt($request -> password);

        $user -> update($data);

        return new UserResource($user);
    }

    public function destroy(string $id){
        $user = $this->repository->find($id);
        // $user = $this->repository->where('id', '=', $id) -> first();
        if(!$user){
            return response() -> json(['message' => 'user not found'], 404);
        }

        $user -> delete();

        // $user = $this->repository->findOrFail($id);

        return response() -> json([], 204);
    }
}
