<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Comment;

class CommentController extends Controller
{
    //
    public function addComment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'comment' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()->all()]);
        }

        if ($request->ajax()) {
            $comment = New Comment;
            $comment->parent_id = $request->comment_id;
            $comment->comment = $request->comment;
            $comment->username = $request->username;
            $comment->save();
        }

        echo "[]";
    }

    public function getComments(Request $request)
    {
        $output = '';
        if ($request->ajax()) {

            $comments = Comment::where('parent_id', 0)->orderBy('id', 'DESC')->get();

            $i = 0;
            $array = [];
            foreach ($comments as $row) {
                $array[$i]['id'] = $row->id;
                $array[$i]['username'] = $row->username;
                $array[$i]['comment'] = $row->comment;
                $array[$i]['created_at'] = $row->created_at;
                $array[$i]['margin_left'] = 0;
                $array[$i]['layer'] = 1;
                $array[$i]['children'][] = $this->getReplyComments($row->id);

                $i++;
            }

            return view('comment.comment', compact('array'));
        }
    }

    private function getReplyComments($parentId = 0, $layer = 1)
    {
        $array = [];

        if ($parentId == 0) {
            $marginleft = 0;
        } else {
            $marginleft = $layer * 48;
            $layer++;
        }

        $comments = Comment::where('parent_id', $parentId)->orderBy('id', 'DESC')->get();

        if ($comments) {

            $i = 0;
            
            foreach($comments as $row) {
                $array[$i]['id'] = $row->id;
                $array[$i]['username'] = $row->username;
                $array[$i]['comment'] = $row->comment;
                $array[$i]['created_at'] = $row->created_at;
                $array[$i]['margin_left'] = $marginleft;
                $array[$i]['layer'] = $layer;
                $array[$i]['children'][] = $this->getReplyComments($row->id, $layer);

                $i++;
            }
        }

        return $array;
    }
}
