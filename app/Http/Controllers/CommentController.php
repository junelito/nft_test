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

            foreach ($comments as $row) {
                $output .= '
                    <div class="panel panel-default">
                    <div class="panel-heading">By <b>'.$row->username.'</b> on <i>'.$row->created_at.'</i></div>
                    <div class="panel-body">'.$row->comment.'</div>
                    <div class="panel-footer" align="right"><button type="button" class="btn btn-default reply" id="'.$row->id.'">Reply</button></div>
                    </div>
                    ';
                $output .= $this->getReplyComments($row->id);
            }
        }
        
        echo $output;
    }

    private function getReplyComments($parentId = 0, $layer = 1)
    {
        $output = '';

        if ($parentId == 0) {
            $marginleft = 0;
        } else {
            $marginleft = $layer * 48;
            $layer++;
        }

        $comments = Comment::where('parent_id', $parentId)->orderBy('id', 'DESC')->get();

        if ($comments) {

            foreach($comments as $row) {
                
                $replyBtn = '<button type="button" class="btn btn-default reply" id="'.$row->id.'">Reply</button>';
                if ($layer >= 3) {
                    $replyBtn = '';
                }

                $output .= '
                <div class="panel panel-default" style="margin-left:'.$marginleft.'px">
                    <div class="panel-heading">By <b>'.$row->username.'</b> on <i>'.$row->created_at.'</i></div>
                    <div class="panel-body">'.$row->comment.'</div>
                    <div class="panel-footer" align="right">'.$replyBtn.'</div>
                </div>
                ';
                $output .= $this->getReplyComments($row->id, $layer);
            }
        }

        return $output;
    }
}
