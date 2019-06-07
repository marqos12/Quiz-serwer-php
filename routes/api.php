<?php

use Illuminate\Http\Request;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::post('subject','API\QuizController@createSubject');
//Route::post('register', 'Auth\RegisterController@register');

 
//sprawdź odpowiedni bez zapisywania wyniku i autoryzacji
Route::post('question/checkWOR','QuestionController@checkAnswers');
//zwraca detale quizu demo
Route::get('demo/details/{name}','OtherController@getDemoDetails');

//lista pytań z odpowiedziami dla quizu bez statusów
Route::get('question/demo/WAFQ/{id}','QuestionController@getQWAFQD');
//pojedyncze pytanie z odpowiezdiami
Route::get('question/demo/WA/{id}','QuestionController@getQWAD');

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('login', 'API\UserController@login');
Route::post('register', 'API\UserController@register');

Route::group(['middleware' => 'auth:api'], function(){
//userController
        //update user
    Route::put('updateUser','API\UserController@update');
        //delete user
    Route::delete('user/{id}','API\UserController@delete');


    //quizController
        //tworzenie quizy
    Route::post('subject','QuizController@createSubject');
        //zwraca liczbę pytań dla quizu
    Route::get('subject/noQt/{id}','QuizController@getNoQuizQt');
        //zwraca obiekt quizu o id
    Route::get('subject/details/{id}','QuizController@getQuizDetails');
        //zwraca listę quizów dla kierunku
    Route::get('subject/list/course/{id}','QuizController@getQuizForCourse');
        //zwarca listę quizów dla autora
    Route::get('subject/list/author/{id}','QuizController@getQuizForAuthor');
        //zwraca wynik dla użydkownika
    Route::get('subject/result/{idUser}','QuizController@getResultForUser');
        //uaktualnienie tematu
    Route::put('subject','QuizController@updateSubject');
        //delete subject
    Route::delete('subject/{id}','QuizController@deleteSubject');

    //questionController
        //sprwdź odpowiedzi z zapisaniem wyniku
    Route::post('question/checkWR','QuestionController@checkAnswersSaveResult');
        //utwórz pytanie z odpowiedziami
    Route::post('question','QuestionController@createQuestion');
        //zwraca listę pytań z odpowiedziami z statusami dla quizu
    Route::get('question/answerWS/{id}','QuestionController@getAnswerWSFQuiz');
        //lista pytań z odpowiedziami dla quizu bez statusów
    Route::get('question/WAFQ/{id}','QuestionController@getQWAFQ');
        //pojedyncze pytanie z odpowiezdiami
    Route::get('question/WA/{id}','QuestionController@getQWA');
        //zwraca losowe pytania dla quizu
    Route::get('question/random/{id}','QuestionController@getRadndomQFQ');
        //update pytania z odpowiedziami
    Route::put('question','QuestionController@updateQWA');
    //delete question
    Route::delete('question/{id}','QuestionController@deleteQuestion');

    //otherController
        //zwraca listę kierunków
    Route::get('courses','OtherController@getCoursesList');
        //zwraca listę uzytkownikow po filtracji
<<<<<<< HEAD
    Route::get('usersList/{email}/{name}/{surname}/{course}','OtherController@getUsersList');
        //zwaraca szczegóły użytkownika
    Route::get('user/{id}','OtherController@userDetails');
=======
    Route::get('usersList','OtherController@getUsersList');
>>>>>>> parent of ae647ac... utworzenie projektu
	//edycja uzytkownika przez nauczyciela
    Route::put('user','OtherController@editUserByN');
	//edycja uzytkownika przez siebie
    Route::put('userOwn','OtherController@editUser');

});