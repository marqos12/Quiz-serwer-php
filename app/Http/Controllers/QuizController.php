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

use Validator;

class QuizController extends Controller
{
    public function createSubject(Request $request){
        if($request->user()->role!='n') return response()->json('Brak autoryzacji (zła rola)', 401);

        if (!isset($request->all()['name'])) return response()->json('Brak nazwy', 400);
        if (!isset($request->all()['subject'])) return response()->json('Brak przedmiotu', 400);
        if (!isset($request->all()['course'])) return response()->json('Brak kierunku', 400);
        if (isset($request->all()['limitedTime']) && $request->all()['limitedTime'] && $request->all()['time']<1) return response()->json('Niepoprawny limit czasu', 400);
        if (!isset($request->all()['canBack']))$request['canBack']=false;
        if (isset($request->all()['canBack']) && $request->all()['canBack'] && !$request->all()['separatePage']) $request['canBack'] = false;
        
        $request['idAuthor'] = $request->user()->id;
        $subject = Subject::create($request->all());
        return response()->json($subject, 201);
    }

    public function getNoQuizQt($id){      
        $number = Question::all()->where('idSubject',$id)->count();
        return response()->json($number, 200);
    }

    public function getQuizDetails($id){
        $quiz = Subject::findOrFail($id);
        $quiz->noQuestions = Question::where('idSubject',$id)->get()->count();
        return response()->json($quiz, 200);
    }

    public function getQuizForCourse($id, Request $request){     
        if($request->user()->course!=$id) return response()->json('Brak autoryzacji (zły kierunek)', 401); 
        $quizList = Subject::all()->where('course',$id);
        $array=array();
        foreach($quizList as $list) array_push($array, $list);
        return response()->json($array, 200);
    }

    public function getQuizForAuthor($id, Request $request){   
        if($request->user()->role!='n') return response()->json('Brak autoryzacji (zła rola)', 401);
        if($request->user()->id != $id) return response()->json('Brak autoryzacji (nie pytasz o siebie)', 401);
        
        $quizList = Subject::all()->where('idAuthor',$id);
        $array=array();
        foreach($quizList as $list) array_push($array, $list);
        return response()->json($array, 200);
    }

    public function getResultForUser($idUser,Request $request){  
        if($request->user()->id != $idUser ) return response()->json('Brak autoryzacji (nie pytasz o siebie)', 401);    
        $quizList = UserResult::where('idUser',$idUser)->get();
        
        return response()->json($quizList, 200);
    }

    public function updateSubject(Request $request){
        if($request->user()->role!='n') return response()->json('Brak autoryzacji (zła rola)', 401);
        if (!isset($request->all()['id'])) return response()->json('Brak id', 400);
        if (!isset($request->all()['name'])) return response()->json('Brak nazwy', 400);
        if (!isset($request->all()['subject'])) return response()->json('Brak przedmiotu', 400);
        if (!isset($request->all()['course'])) return response()->json('Brak kierunku', 400);
        if (isset($request->all()['limitedTime']) && $request->all()['limitedTime'] && $request->all()['time']<1) return response()->json('Niepoprawny limit czasu', 400);
        if (isset($request->all()['canBack']) && $request->all()['canBack'] && !$request->all()['separatePage']) $request['canBack'] = false;
        if (!isset($request->all()['idAuthor'])) $request['idAuthor'] = $request->user()->id;
        $quiz = Subject::findOrFail($request['id']);
        if($quiz->idAuthor!=$request->user()->id) return response()->json('Brak autoryzacji (zły autor)', 401);
        $quiz = $quiz->update($request->all());
        return response()->json($quiz, 200);
    }
    
    public function deleteSubject($id,Request $request) { 
        if($request->user()->role!='n') return response()->json('Brak autoryzacji (zła rola)', 401);
        $subject = Subject::find($id);        
        if($subject->idAuthor != $request->user()->id) return response()->json('Brak autoryzacji (zły autor)', 401);
        $questions = Question::where('idSubject',$id)->get();
        foreach($questions as $question){
            $answers = Answer::where('idQuestion',$question->id)->get();
            foreach($answers as $answer){
                $answer = Answer::find($answer->id);
                $answer->delete();
            }
            $question = Question::find($question->id);
            $question->delete();
        }
        $subject->delete();
        return response()->json($subject,200); 
    }

}