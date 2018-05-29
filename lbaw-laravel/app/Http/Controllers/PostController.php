<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Redirect;
use Hash;
use Auth;

class PostController extends Controller {

    public function reports($id) {
        if (Auth::user()->type == 'ADMIN') {
            $ret = \App\PostReport::select('postid', 'reporterid', 'date', 'reason', 'username', 'type', 'email', 'state', 'img_path', 'points')->where('postid', $id)->join('users', 'id', 'reporterid')->get();
            $post = \App\Post::select('posterid', 'content', 'date', 'isvisible', 'points')->where('id', $id)->get();
            return view('pages.reports.reports')->with(['reports' => $ret])->with(['post' => $post])->with(['id' => $id]);
        } else {
            return redirect('404');
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id) {
        $ret = (array) DB::select('SELECT * FROM Tag WHERE id=:id', ['id' => $id]);
        if ($ret != null) {
            $str = '';
            foreach ($ret as $val) {
                $str = $str . 'Tag:' . $val->name . "<br>";
            }
            return $str;
        }
        return 'Val:' . session('test');
    }

    public function test(Request $req) {
        return $req->input('data');
    }

    //todo: begin add try

    public function postQuestionPage() {
        if (Auth::check()) {
            return view('pages.PostQuestionPage.index');
        } else
            return view('pages.index');
    }

    public function postQuestion(Request $request) {
        if (Auth::check() == false)
            return redirect("/");

        $postID = DB::transaction(function() use($request) {
                    $newPost = DB::table('post')->insertGetId([
                        'posterid' => Auth::user()->id,
                        'content' => $request->input('content')
                    ]);

                    DB::table('question')->insert([
                        'postid' => $newPost,
                        'title' => $request->input('title'),
                    ]);

                    $tagsString = trim($request->input('tags'));
                    $tags = preg_split('/\s+/', $tagsString);
                    for ($i = 0; $i < count($tags); $i++) {
                        $currTag = $tags[$i];
                        $dbTag = DB::table('tag')->select('id')->where('name', $currTag)->first();
                        if ($dbTag == null) {
                            //insert new tag in database
                            $dbTagId = DB::table('tag')->insertGetId(['name' => $currTag]);
                        } else {
                            $dbTagId = $dbTag->id;
                        }

                        DB::table('tagquestion')->insert([
                            'question_id' => $newPost,
                            'tag_id' => $dbTagId,
                        ]);
                    }

                    return $newPost;
                });

        if ($postID != null)
            return redirect('questions/' . $postID);
    }

    public function showQuestionPage($id) {
        $questionElements = DB::table('post')->select('users.points as userPoints', 'users.*', 'post.id as post_id', 'post.posterid', 'post.content', 'post.date', 'post.isvisible', 'post.points', 'question.*')
                ->join('users', DB::raw('post.posterid'), '=', DB::raw('users.id'))
                ->join('question', DB::raw('post.id'), '=', DB::raw('question.postid'))
                ->where(DB::raw('post.isvisible'), '=', TRUE)
                ->where(DB::raw('post.id'), '=', $id)
                ->get();
        if (!$questionElements || count($questionElements) == 0)
            return redirect('/404');

        $answersElements = DB::table('post')->select('users.*', 'post.id as post_id', 'post.posterid', 'post.content', 'post.date as post_date', 'post.isvisible', 'post.points', 'answer.*')
                ->join('users', DB::raw('post.posterid'), '=', DB::raw('users.id'))
                ->join('answer', DB::raw('post.id'), '=', DB::raw('answer.postid'))
                ->where(DB::raw('post.isvisible'), '=', TRUE)
                ->where(DB::raw('answer.questionid'), '=', $id)
                ->orderBy('post_date', 'asc')
                ->get();
        if (!$answersElements)
            return redirect('/404');

        $questionUserCounter = array(
            'posts' => DB::table('post')->where('posterid', '=', $questionElements[0]->id)->count('posterid'),
            'points' => DB::table('users')->where('id', '=', $questionElements[0]->id)->sum('points')
        );
        if (!$questionUserCounter)
            return redirect('/404');

        if (Auth::check()) {
            $postVotes = DB::table('postvote')->select('*')->where('posterid', '=', Auth::user()->id)->get();
            if (!$postVotes)
                return redirect('/404');
        } else
            $postVotes = null;

        $answerUserCounter = array();
        foreach ($answersElements as $answerElements) {
            $row = array(
                'posts' => DB::table('post')->where('posterid', '=', $answerElements->id)->count('posterid'),
                'points' => DB::table('users')->where('id', '=', $answerElements->id)->sum('points')
            );
            if (!$row)
                return redirect('/404');
            array_push($answerUserCounter, $row);
        }

        $questionUserPointsCounter = DB::table('post')->where('posterid', '=', $questionElements[0]->id)->count('posterid');

        if (!$questionUserPointsCounter)
            return redirect('/404');
        return view('pages.ViewQuestion.index', ['questionElements' => $questionElements[0],
            'answersElements' => $answersElements, 'questionUserCounter' => $questionUserCounter,
            'answerUserCounter' => $answerUserCounter, 'postVotes' => $postVotes
        ]);
    }

    public function postAnswer($post_id, Request $request) {
        $user = Auth::user()->id;
        $postID = DB::transaction(function() use($request, $post_id) {
                    $newPost = DB::table('post')->insertGetId([
                        'posterid' => Auth::user()->id,
                        'content' => $request->input('content')
                    ]);

                    DB::table('answer')->insert([
                        'postid' => $newPost,
                        'questionid' => $post_id,
                        'iscorrect' => false
                    ]);

                    return $newPost;
                });

        if ($post_id != null)
            return redirect('questions/' . $post_id);
    }

    public function closeQuestion($id) {
        if (Auth::check()) {
            $logged_user_id = Auth::user()->id;
            $user = \App\User::where('id', $logged_user_id)->first();
            if ($user == null)
                return redirect("/");

            $posterId = DB::select('SELECT posterID FROM Post WHERE id=:id', ['id' => $id])[0]->posterid;

            $validAccess = false;
            if ($user->type == "ADMIN")
                $validAccess = true;
            else if ($posterId == $logged_user_id)
                $validAccess = true;

            if ($validAccess == false)
                return redirect("users/" . $posterId);

            DB::table("question")->where('postid', $id)->update(array('isclosed' => true));
            return redirect("users/" . $posterId);
        }

        return redirect("/");
    }

    public function postVote(Request $request, $postId) {
        if (Auth::check()) {
            $voteValue = $request->voteValue;
            $loggedUserId = Auth::user()->id;
            $postValueFromPreviousVote = DB::table("postvote")->select("value")->where('postid', $postId)
                            ->where('posterid', $loggedUserId)->first();

            if (count($postValueFromPreviousVote) != 0) {

                if ($postValueFromPreviousVote->value == $voteValue) {
                    DB::table("postvote")->where('postid', $postId)->where('posterid', $loggedUserId)->delete();
                    $postAtualPoints = DB::table("post")->select("points")->where('id', $postId)->first();
                    return $postAtualPoints->points;
                }

                DB::table("postvote")->where('postid', $postId)->where('posterid', $loggedUserId)->update(['value' => $voteValue]);
                $postAtualPoints = DB::table("post")->select("points")->where('id', $postId)->first();
                return $postAtualPoints->points;
            } else {
                DB::table("postvote")->insert(['postid' => intval($postId), 'posterid' => intval($loggedUserId),
                    'value' => intval($voteValue)]);
            }

            $postAtualPoints = DB::table("post")->select("points")->where('id', $postId)->first();
            return $postAtualPoints->points;
        }

        return "error";
    }

    public function delete($postId) {
        if (!Auth::check())
            return "error";

        $question = DB::table("question")->select("postid")->where('postid', $postId)->first();
        if ($question != null)
            PostController::deleteQuestion($postId);

        $answer = DB::table("answer")->select("postid")->where('postid', $postId)->first();
        if ($answer != null)
            PostController::deleteAnswer($postId);
    }

    public function deleteQuestion($postId) {
        DB::transaction(function() use($postId) {
            DB::table("post")->where('id', $postId)->update(array('isvisible' => false));
            DB::table("postvote")->where('postid', $postId)->delete();

            $answersToQuestion = DB::table("answer")->select("postid")->where('questionid', $postId)->get();
            foreach ($answersToQuestion as $answer)
                PostController::deleteAnswer($answer->postid);
        });
    }

    public function deleteAnswer($postId) {
        DB::transaction(function() use($postId) {
            DB::table("post")->where('id', $postId)->update(array('isvisible' => false));
            DB::table("postvote")->where('postid', $postId)->delete();
        });
    }

    public function reportPost(Request $request, $postID) {
        $newReporterId = Auth::user()->id;
        $reason = $request->reportReason;
        return DB::transaction(function() use($postID, $newReporterId, $reason) {
                    $reportAlreadyExistant = DB::table("postreport")->select('date')->where('postid', '=', $postID)->where('reporterid', '=', $newReporterId)->first();
                    if ($reportAlreadyExistant)
                        return "already reported";

                    DB::table("postreport")->insert([
                        'postid' => $postID,
                        'reporterid' => $newReporterId,
                        'reason' => $reason,
                        'date' => now()
                    ]);
                    return "success";
                });
    }

    public static function getXMostRecentQuestionsAsHTML(Request $request) {
        $numberOfQuestions = $request->input('numOfQuestionsToRetrieve');
        $firstQuestionOffset = $request->input('offset');
        $questions = PostController::getXMostRecentQuestions($numberOfQuestions, $firstQuestionOffset);
        if (Auth::check())
            return view('pages.indexloggedin_questionsdiv', ['questions' => $questions['questions']], ['questions_tags' => $questions['questions_tags']]);
        else
            return view('pages.index_questionsdiv', ['questions' => $questions['questions']], ['questions_tags' => $questions['questions_tags']]);
    }

    public static function getXMostRecentQuestions($numberOfQuestions, $firstQuestionOffset = 0) {
        $questions = DB::table('question')
                ->join('post', 'question.postid', '=', 'post.id')
                ->join('users', 'post.posterid', '=', 'users.id')
                ->select('question.postid as question_id', 'title', 'content', 'post.posterid as poster_id', 'post.points as question_points', 'users.points as poster_points', 'username')
                ->where('isvisible', '=', 'true')
                ->orderBy('date', 'desc')
                ->skip($firstQuestionOffset)
                ->take($numberOfQuestions)
                ->paginate(5);

        return PostController::checkQuestionsReturn($questions);
    }

    public static function getXHotQuestions($numberOfQuestions) {
        $questions = DB::table('question')
                        ->join('post', 'question.postid', '=', 'post.id')
                        ->join('users', 'post.posterid', '=', 'users.id')
                        ->select('question.postid as question_id', 'title', 'content', 'post.posterid as poster_id', 'post.points as question_points', 'users.points as poster_points', 'username')
                        ->where('isvisible', '=', 'true')
                        ->orderBy('post.points', 'desc')
                        ->orderBy('date', 'desc')
                        ->take($numberOfQuestions)->paginate(5);

        return PostController::checkQuestionsReturn($questions);
    }

    public static function checkQuestionsReturn($questions) {
        if (!$questions)
            return "error";
        $questions_tags = array();
        foreach ($questions as $question) {
            $tags_arr = array();
            $tags = DB::table('question')
                    ->join('tagquestion', 'question.postid', '=', 'tagquestion.question_id')
                    ->join('tag', 'tagquestion.tag_id', '=', 'tag.id')
                    ->select('tag.name as tag_name')
                    ->where('question.postid', '=', $question->question_id)
                    ->get();
            if (!$tags)
                return "error";

            $questions_tags[$question->question_id] = $tags;
        }

        if (Auth::check()) {
            $postVotes = DB::table('postvote')->select('*')->where('posterid', '=', Auth::user()->id)->get();
            if (!$postVotes)
                return redirect('/404');
        } else
            $postVotes = null;

        $ret = array();
        $ret['questions'] = $questions;
        $ret['questions_tags'] = $questions_tags;
        $ret['postVotes'] = $postVotes;
        return $ret;
    }

    public function searchForQuestion(Request $request) {
        $tags = $request->input('tags');
        $keywords = $request->input('keywords');
        $tagsArray = json_decode($tags);
        $keywordsArray = json_decode($keywords);
        /*
          $tagsArray = array();
          $tagsArray[0] = 'Java';
          $tagsArray[1] = 'C++';
          $tagsArray[2] = 'JS';

         */


                $tags_matches = DB::table('question')
                ->join('tagquestion', 'question.postid', '=', 'tagquestion.question_id')
                ->join('tag', 'tagquestion.tag_id', '=', 'tag.id')
                ->whereIn('tag.name', $tagsArray)
                ->select(DB::raw('count(question.postid) as tag_count'), 'question.postid as question_id1')
                ->groupBy('question.postid')
                ->orderBy('tag_count', 'DESC');
                
                $keyword_matches = DB::table('question')
                ->join('post', 'question.postid', '=', 'post.id')
                ->whereIn('question.title', $keywordsArray)
                ->orWhereIn('post.content', $keywordsArray)
                ->select(DB::raw('count(question.postid) as keyword_count'), 'question.postid as question_id2')
                ->groupBy('question.postid')
                ->orderBy('keyword_count', 'DESC');
                
                $final_results = $tags_matches
                ->join($keyword_matches->toSql(), function ($join) {
                $join->on('question_id1', '=', 'question_id2');
                })->toSql();
                echo $tags_matches->toSql();
                echo "\n";
                echo $keyword_matches->toSql();
                echo "\n";
                
                //$final_results = $tags_matches->leftJoin($keyword_matches)->get();
                echo ($final_results);
                

        return;
        echo "\n";
        echo $DBTagResults;
        return;
        $currentDBResults = null;
        foreach ($keywordsArray as $keyword) {
            $retFromDB = DB::table('question')
                    ->join('post', 'question.postid', '=', 'post.id')
                    ->where('question.title', 'like', '%' . $keyword . '%')
                    ->orwhere('post.content', 'like', '%' . $keyword . '%')
                    ->select(DB::raw('count(question.postid) as keyword_count, question.postid as question_id'))
                    ->groupBy('question.postid');
            if ($currentDBResults == null)
                $currentDBResults = $retFromDB;
            else
                $currentDBResults = $currentDBResults->unionAll($retFromDB);
        }
        /*
          $DBKeywrodsResults =
          $currentDBResults
          ->select('*', DB::raw('SUM (keyword_count) as keyword_matches'))
          ->groupBy('question_id')
          ->get();
         */
        $finalMatches = $DBTagResults
                ->join($DBKeywrodsResults)
                ->sum('keyword_count as keyword_matches')
                ->groupBy('postid')
                ->get();

        //echo json_encode($dbResultsArray);
        /* $finalResult;
          foreach($dbResultsArray as $db)
          {

          } */
        if (Auth::check())
            return view('pages.indexloggedin_questionsdiv', ['questions' => $questions], ['questions_tags' => $questions_tags]);
        else
            return view('pages.index_questionsdiv', ['questions' => $questions], ['questions_tags' => $questions_tags]);
    }

    //}
    /* $questions = DB::table('question')
      ->join('tagquestion', 'question.postid', '=', 'tagquestion.question_id')
      ->join('tag', 'tagquestion.tag_id', '=', 'tag.id')
      ->whereIn('tag.name', $tags); */

    //todo: end

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        //
    }

}
