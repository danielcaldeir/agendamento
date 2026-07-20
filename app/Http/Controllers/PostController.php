<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdatePatientRequest;
use App\Models\Patient;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostController extends Controller
{
    protected $userModel;
    protected $postModel;

    public function __construct()
    {
        $this->userModel = new User();
        $this->postModel = new Post();
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // READ
        // $post = new Post();
        $posts = $this->postModel->all();

        return $posts;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(UpdatePatientRequest $request)
    {
        $data = $request->only('user_id', 'blood_type', 'social_number');
        $this->userModel->find($data['user_id']);

    }

    /**
     * Show the form for creating a new resource.
     */
    public function signup(CreateUserRequest $request): JsonResponse
    {
        $data = $request->only(['name', 'email', 'password', 'type']);
        $user = $this->userModel->where('email', $data['email'])->first();
        if ($user){
            Patient::create([
                'user_id' => $user->id
            ]);
        } else {
            $user = $this->userModel->create($data);

            Patient::create([
                'user_id' => $user->id
            ]);
        }
        // $user = $this->userModel->create($data);
        // Patient::create([
        //     'user_id' => $user->id
        // ]);
        $user->refresh();
        $user->patient;

        $response = [
            'error' => '',
            'token' => $user
        ];
        return response()->json($response);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $new_post = [
            'titulo' => 'Meu Terceiro Post',
            'corpo' => 'Algum assunto por hoje',
            'autor' => 'Daniel'
        ];
        $post = new Post($new_post);
        // $post->save();

        // $post = new Post();
        // $post->titulo = 'Meu Segundo Post';
        // $post->corpo = 'Qualquer Assunto';
        // $post->autor = 'Daniel';
        // $post->save();
        return ($post);
    }

    /**
     * Display the specified resource.
     */
    // public function show(string $id)
    public function show(Request $request)
    {
        // READ
        $id = 2;
        $post = new Post();
        $posts = $post->find($id);
        return($posts);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    // public function update(Request $request, string $id)
    public function update(Request $request)
    {
        //
        $id = 3;
        // $new_post = [
        //     'autor' => 'Desconhecido'
        // ];
        $new_post = [
            'autor' => 'Daniel Caldeira'
        ];
        $post = new Post();
        $post_update = $post->find($id);
        // $post_update = $post->where('id', $id)->update($new_post);
        $post_update->autor = "Desconhecido";
        $post_update->corpo = "Algum Assunto nesta semana";
        $post_update->save();
        return $post_update;

    }

    /**
     * Remove the specified resource from storage.
     */
    // public function destroy(string $id)
    public function destroy(Request $request)
    {
        //
        $id = 1;
        $post = Post::find($id);
        if ($post) {
            return $post->delete();
        } else {
            return "Nao foi possivel encontrar o ID";
        }

        // return $post->delete();
    }
}
