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
Use DB;

class QuestionController extends Controller
{
    //sprawdź bez zapisywania wyniku dla testu demo!
    public function checkAnswers(Request $request){
        $total = 0;
        $true = 0;
        $answer2 = Answer::find($request[0]['answers'][0]['id']);
        $question2 = Question::find($answer2->idQuestion);
        $subject = Subject::find($question2->idSubject);
        if($subject->course !='web' && $subject->course !='java')return response()->json('Brak autoryzacji (nie jest to quiz demo)', 401);

        foreach($request->all() as  $question){
            $total++;
            $value = true;
            foreach ($question['answers'] as $answer){
                $status = Answer::where('id',$answer['id'])->value('status');
                if ($answer['value'] != $status) $value = false;
            }
            if($value) $true++;
        }
        $result = array(
            'total' => $total,
            'true' => $true
        );
        return response()->json($result, 200);
    }

    //sprawdź z zapisywaniem wyniku
    public function checkAnswersSaveResult(Request $request){
        DB::beginTransaction();
        $total = 0;
        $true = 0;
        foreach($request->all() as  $question){
            $total++;

            $value = true;
            foreach ($question['answers'] as $answer){
                $status = Answer::where('id',$answer['id'])->value('status');
                if ($answer['value'] != $status) $value = false;
            }
            if($value) $true++;
        }
        $result = array(
            'total' => $total,
            'true' => $true
        );
        $userId=$request->user()->id;
        $questionId = Answer::where('id',$request->all()[1]['answers'][1]['id'])->value('idQuestion');
        $subjectId = Question::where('id',$questionId)->value('idSubject');
        DB::table('user_results')->where('idUser',$userId)->where('idSubject',$subjectId)->delete();
   
        $userResult['idUser'] = $userId;
        $userResult['idSubject'] = $subjectId;
        $userResult['result'] = $true/$total;
        UserResult::create($userResult);
        DB::commit();
        return response()->json($result, 201);
    }

    //nowe pytanie
    public function createQuestion(Request $request){
        if($request->user()->role=='n'){
            $subject = Subject::find($request->all()['idSubject']);
            if($request->user()->id!=$subject->idAuthor)
                return response()->json('Brak autoryzacji (zły autor)', 401);
            $true=0;
            $answers = $request->all()['answers'];
            foreach($answers as $answer){if($answer['status']==1)$true++;}
            if($true == 0)
            return response()->json('Brak poprawnego pytania', 400);

            DB::beginTransaction();
            $request['answers']=null;
            $question = Question::create($request->all());
            $questionId=$question->id;
            $answersArr = array();
            foreach($answers as $answer){
                $answer['idQuestion'] = $questionId;
                $answer = Answer::create($answer);
                array_push($answersArr,$answer);
            }
            $question->answers = $answersArr;
            DB::commit();
            return response()->json($question, 201);
     }
     else 
     return response()->json('Brak autoryzacji (musisz być nauczycielem)', 401);
    }

    //lista pytań z odpowiedziami dla quizu
    public function getAnswerWSFQuiz(Request $request,$id){
        if($request->user()->role!='n')
          return response()->json('Brak autoryzacji (musisz być nauczycielem)', 401);
        $subject=Subject::find($id);
        if($subject->idAuthor!=$request->user()->id)
            return response()->json('Brak autoryzacji (zły autor)', 401);
        $questionList = Question::where('idSubject',$id)->get();
        $quizQuestionList = array();
        foreach($questionList as $question){
            $answers = Answer::where('idQuestion',$question['id'])->get();
            $question['answers'] = $answers;
            array_push($quizQuestionList,$question);
        }

        return response()->json($quizQuestionList, 200);
    }

    //lista pytań z odpowiedziami dla quizu
    public function getQWAFQ($id){
        $questionList = Question::where('idSubject',$id)->get();
        $quizQuestionList = array();
        foreach($questionList as $question){
            $answers = Answer::where('idQuestion',$question['id'])->get();
            foreach ($answers as $answer) {
                $answer['status']=null;
            }
            $question['answers'] = $answers;
            array_push($quizQuestionList,$question);
        }

        return response()->json($quizQuestionList, 200);
    }

    //lista pytań z odpowiedziami dla quizu Demo
    public function getQWAFQD($id){
        $subject = Subject::find($id);
        if ($subject['course']=='java' || $subject['course']=='web'){
        $questionList = Question::where('idSubject',$id)->get();
        $quizQuestionList = array();
        foreach($questionList as $question){
            $answers = Answer::where('idQuestion',$question['id'])->get();
            foreach ($answers as $answer) {
                $answer['status']=null;
            }
            $question['answers'] = $answers;
            array_push($quizQuestionList,$question);
        }

        return response()->json($quizQuestionList, 200);
        }
        return response()->json('Brak autoryzacji (zły przedmiot demo)', 401);
    }

    //pojedyncze pytanie z odpowiezdiami
    public function getQWA($id,Request $request){
        $question = Question::where('id',$id)->get()->first();
        if($request->user()->role!='n'){
            $subject = Subject::find($question->idSubject);
            if($subject->course != $request->user()->course) return response()->json('Brak autoryzacji (zły kierunek)', 401);
        }
        $answers = Answer::where('idQuestion',$question['id'])->get();
        $question['answers'] = $answers;
      
        return response()->json($question, 200);
    }

    //pojedyncze pytanie z odpowiezdiami Demo
    public function getQWAD($id){
        $question = Question::where('id',$id)->get()->first();
        $subject = Subject::find($question['idSubject']);
        if ($subject['course']=='java' || $subject['course']=='web') {
            $answers = Answer::where('idQuestion', $question['id'])->get();
            $question['answers'] = $answers;
      
            return response()->json($question, 200);
        }        
        return response()->json('Brak autoryzacji (zły przedmiot demo)', 401);
    }

    //zwraca losowe pytania dla quizu
    public function getRadndomQFQ($id){
        $questionList = Question::where('idSubject',$id)->get();
        $quizQuestionList = array();
        foreach($questionList as $question){
            $answers = Answer::where('idQuestion',$question['id'])->get();
            
            $answerList=array();
            foreach ($answers as $answer) {
                $answer['status']=null;
                array_push( $answerList,$answer);
            }
                shuffle($answerList);
            $question['answers'] = $answerList;
            array_push($quizQuestionList,$question);
        }

        shuffle($quizQuestionList);
        return response()->json($quizQuestionList, 200);
    }

    public function updateQWA(Request $request){
        if($request->user()->role!='n') return response()->json('Brak autoryzacji (zła rola)', 401);
        $answers = $request['answers'];
       // $request['answers']=null;
       $true=0;
       foreach($answers as $answer){if($answer['status']==1)$true++;}
       if($true == 0)
       return response()->json('Brak poprawnego pytania', 400);

        $question = Question::findOrFail($request['id']);
        $subject = Subject::find($question->idSubject);   
        if($subject->idAuthor != $request->user()->id) return response()->json('Brak autoryzacji (zły autor)', 401);
       
        foreach($answers as $answer){
            $answer1 = Answer::findOrFail($answer['id']);
            if($question->id != $answer1->idQuestion) return response()->json('Brak autoryzacji (niezgodność id)', 401);
            $answer1 = $answer1->update($answer);
        }
         $question = $question->update($request->all());
        //$question['answers']=(array)$answers;
        return response()->json($question, 200);
    }

    public function deleteQuestion($id,Request $request) { 
        if($request->user()->role!='n') return response()->json('Brak autoryzacji (zła rola)', 401);
        $question = Question::find($id);
        $subject = Subject::find($question->idSubject);
        if($subject->idAuthor!=$request->user()->id) return response()->json('Brak autoryzacji (zły autor)', 401);
            $answers = Answer::where('idQuestion',$id)->get();
            foreach($answers as $answer){
                $answer = Answer::find($answer->id);
                $answer->delete();
            }
            $question = Question::find($question->id);
            $question->delete();
        
        return response()->json($question,200); 
    }
}
