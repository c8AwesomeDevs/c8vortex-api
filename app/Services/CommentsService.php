<?php

namespace App\Services;

use App\Models\Element;
use App\Models\Comments;
use App\Contracts\CommentsInterface;

class CommentsService implements CommentsInterface

{
    
    public function getComments($element_id) {
        $comments_details = Comments::where('element_id', $element_id)->get();
 
        return $comments_details;
    }
  
    public function saveComment($data) {
        $new_comments = Comments::create($data);

        return $new_comments;
    }

    public function updateComments($id, $data){
        $updatecomment = Comments::where('id', $id)->update($data);

        return $updatecomment;
    }
    
    public function deleteComments($id){
        $deletedComment = Comments::where('id', $id)->delete();

        return $deletedComment;
    }
}