<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

use App\Answer;
use App\Course;
use App\Question;
use App\Subject;
use App\SubjectQuestion;
use App\User;
use App\UserResult;

class GeneralSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $faker = \Faker\Factory::create('pl_PL'); 

        $course['name']='1EF-DI';
        Course::create($course);
        $course['name']='2EF-DI';
        Course::create($course);

        $user['name']=$faker->firstName;
        $user['surname']=$faker->lastName;
        $user['email']='ucz1@prz.pl';
        $user['password']=bcrypt('ucz1');
        $user['course']='1EF-DI';
        $user['role']='s';
        User::create($user);

        $user['name']=$faker->firstName;
        $user['surname']=$faker->lastName;
        $user['email']='nau1@prz.pl';
        $user['password']=bcrypt('nau1');
        $user['role']='n';
        User::create($user);

    	for ($i = 0; $i < 20; $i++) {
            
            $subject['idAuthor']=2;
            $subject['multipleChoice']=$faker->numberBetween(0,1);
            if ( $subject['multipleChoice']== 1)
            $subject['name']='wielorotny '.$faker->sentence;
            else
            $subject['name']='jednokrotny '.$faker->sentence;
            $subject['separatePage']=$faker->numberBetween(0,1);
            $subject['limitedTime']=$faker->numberBetween(0,1);
            $subject['time']=$faker->numberBetween(1,60);
            
            $subject['course']='1EF-DI';
            if($faker->numberBetween(0,1)==1) 
                $subject['subject']='java';
            else 
                $subject['subject']='web';
            if($i==1)
            $subject['course']='java';
            if($i==2)
            $subject['course']='web';

            $subject['description']=$faker->paragraph;
            $subject['randomize']=$faker->numberBetween(0,1);
            $subject3 = Subject::create($subject);
            $num =$faker->numberBetween(5,50);
            for ($j = 0; $j < $num; $j++) {
                $question['idSubject']=$subject3['id'];
                $question['text']=$faker->sentence;
                if($faker->numberBetween(0,4)==1)
                 $question['code']=str_replace(">", ">\n", $faker->randomHtml(2,3));
                else $question['code']=null;                
                if($faker->numberBetween(0,4)==1)
                $question['image']='https://i.ytimg.com/vi/LACbVhgtx9I/maxresdefault.jpg';
                else $question['image']=null;
                $question3 = Question::create($question);
                $l=0;
                for ($k = 0; $k < 4; $k++) {
                    $answer['idQuestion']=$question3['id'];
                    if($subject['multipleChoice'] && $l>=0){
                        if($faker->numberBetween(0,1)==1){
                            $answer['status']= 1;
                            $answer['text']='p '.$faker->sentence;
                            $l++;
                        }
                        else {
                            $answer['status']=0;
                            $answer['text']=$faker->sentence;
                        }
                    }
                    else {
                        $answer['status']=0;
                        $answer['text']=$faker->sentence;
                    }
                    if($k == 3 && $l==0){
                        $answer['status']=1;
                        $answer['text']='p '.$faker->sentence;
                    }
                    $answer3 = Answer::create($answer);
                }
            }
        }
    }
}
