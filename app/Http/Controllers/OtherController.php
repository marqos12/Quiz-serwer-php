<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Answer;
use App\Course;
use App\Question;
use App\Subject;
use App\SubjectQuestion;
use App\User;
use App\UserResult;
use DB;
use Validator;

class OtherController extends Controller
{
    //zwraca listę kierunków
    public function getCoursesList(){
        $courses = DB::table('courses')->select('name')->get();
		$array = array();
		foreach($courses as $course){
			array_push($array, $course->name);
		}
		
        return response()->json($array, 200);
    }

    //zwraca detale quizu demo
    public function getDemoDetails($name){
        if($name != 'web' && $name != 'java') return response()->json('Brak autoryzacji (zły test demo)', 401);
        $demo = SUBJECT::where('course',$name)->get()->first();
        
        $demo['noQuestions']= Question::where('idSubject',$demo['id'])->get()->count();
        return response()->json($demo, 200);
    }
	
	//filtracja listy uzytkownikow
    public function getUsersList(Request $request){		
		if($request->user()->role!='n') return response()->json('Brak autoryzacji (zła rola)', 401);
		$email=$request->input('email');
		$course=$request->input('course');
		$name=$request->input('name');
		$surname=$request->input('surname');
		if($course==null)
		$users = User::where([['email','LIKE','%'.$email.'%'],['name','LIKE','%'.$name.'%'],['surname','LIKE','%'.$surname.'%'],['course','LIKE','%'.$course.'%']])
		->orWhere([['email','LIKE','%'.$email.'%'],['name','LIKE','%'.$name.'%'],['surname','LIKE','%'.$surname.'%'],['course',$course]])->get();
		else 
		$users = User::where([['email','LIKE','%'.$email.'%'],['course','LIKE','%'.$course.'%'],['name','LIKE','%'.$name.'%'],['surname','LIKE','%'.$surname.'%']])->get();
		$array=array();
		//foreach($users as $user) push_array($array,$user);
		return response()->json($users, 200);
	}
	
	//edycja uzytkownika przez nauczyciela
	public function editUserByN(Request $request){
        if($request->user()->role!='n') return response()->json('Brak autoryzacji (zła rola)', 401);
		$user = User::find($request['id']);
		$request['id']=$user->id;
		$request['password']=$user->password;
		$request['email']=$user->email;
		$user->update($request->all());
		return response()->json($request->all(), 200);
		
	}
	
	//edycja uzytkownika przez siebie
	public function editUser(Request $request){
        if($request->user()->id!=$request['id']) return response()->json('Brak autoryzacji (nie edytujesz siebie)', 401);
		$user = User::find($request['id']);
		$request['id']=$user->id;
		$request['role']=$user->role;

		if(!isset($request['password'])||$request['password']=='')$request['password']=$user->password;
		else if($request['password']!=$request['c_password'])return response()->json('Hasła się nie zgadzają', 400);
		else $request['password']=bcrypt($request['c_password']);

		$request['email']=$user->email;
		$user->update($request->all());
        $user = User::find($user->id);
        $success['token'] =  $user->createToken('web')-> accessToken; 
        $success['user'] =  $user;
        return response()->json($success, 200); 
	}
	
	//szczegóły konta
	public function userDetails($id, Request $request){
        if($request->user()->role!='n') return response()->json('Brak autoryzacji (zła rola)', 401);
		$user = User::find($id);
		$user->password=null;
        return response()->json($user, 200); 
	}

	//potwierdzenie konta
	public function confirmPassword(){
    
	}
	
	//odzyskanie hasła
	public function newPassword(){
    
	}
}
