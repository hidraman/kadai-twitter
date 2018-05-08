<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Task;
use App\Http\Controllers\Controller;



class TasksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
         // ここをallではなく、今ログインしているユーザのものだけ取得するように修正する
         // micropostsのMicropostsControllerのindexアクションを参照
        $data = [];
        if (\Auth::check()) {
            $user = \Auth::user();
            $tasks = $user->tasks()->orderBy('created_at', 'desc')->paginate(10);

            $data = [
                'user' => $user,
                'tasks' => $tasks,
            ];
            return view('tasks.index', $data);
        }else {
            return view('welcome');
        }

    }
    public function show($id)
    {
        $task = Task::find($id);
        // ここ↑で取得した$taskが持っているuser_idが、今ログインしているユーザのIDと一致するかを確認して、
        // 一致していれば処理を進める(showの画面を出す)、一致していなければトップページにリダイレクトするなどの処理を加える
        // 比較についてはMicropostsControllerのdestroyアクション内に似たようなコードがあるのでそちらを参照
    if (\Auth::user()->id === $task->user_id){
        return view('tasks.show', [
            'task' => $task,
        ]);
    }else{
            return view('welcome');
        }
    }
    public function create()
    {
        $task = new Task;

        return view('tasks.create', [
            'task' => $task,
        ]);
    }
    public function store(Request $request)
    {
         $this->validate($request, [
            'status' => 'required|max:10',   // 追加
            'content' => 'required|max:191',
        ]);
        
        $request->user()->tasks()->create([
            'content' => $request->content,
            'status' => $request->status,
        ]);

        return redirect('/');
    
    }
    public function edit($id)
    {
        $task = Task::find($id);

        return view('tasks.edit', [
            'task' => $task,
        ]);
    }
    public function update(Request $request, $id)
    {
         $this->validate($request, [
            'status' => 'required|max:10',   // 追加
            'content' => 'required|max:191',
        ]);

        $task = Task::find($id);
        $task->status = $request->status;    // 追加
        $task->content = $request->content;
        $task->save();

        return redirect('/');
    }
    public function destroy($id)
    {
        $task = Task::find($id);
        $task->delete();

        return redirect('/');
    }
}
