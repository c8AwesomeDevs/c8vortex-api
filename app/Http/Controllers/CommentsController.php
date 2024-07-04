<?php

namespace App\Http\Controllers;

use App\Mail\FeedbackAndSupportConcern;
use Illuminate\Http\Request;
use App\Services\CommentsService;
use Illuminate\Support\Facades\Mail;

class CommentsController extends Controller
{
    public function saveComments(Request $request, CommentsService $commentsService, $id)
    {
        $data = [
            'element_id' => $id,
            'user_name' => $request->username,
            'timestamp' => $request->timestamp,
            'comment' => $request->comment,
        ];
        $new_comments = $commentsService->saveComment($data);

        return response()->json($new_comments);
    }

    public function updateComments(Request $request, CommentsService $commentsService, $id)
    {
        $data = [
            'timestamp' => $request->timestamp,
            'comment' => $request->comment,
        ];
        $updatedComment = $commentsService->updateComments($id, $data);

        return response()->json($updatedComment);
    }

    public function getComments(Request $request, CommentsService $commentsService)
    {

        $comments_details = $commentsService->getComments($request->element_id);
        return response()->json([
            'comments' => $comments_details
        ]);
    }
    public function deleteComments(CommentsService $commentsService, $id)
    {
        $deletedComment = $commentsService->deleteComments($id);

        if ($deletedComment) {
            return response()->json(['message' => 'Comment deleted successfully']);
        } else {
            return response()->json(['message' => 'Comment not found'], 404);
        }
    }

    // feedback and support
    // this logic does not belong here; i just put it here because idk where to put it
    public function accomodateFeedbackAndSupport(Request $request)
    {
        // validate
        $validated = $request->validate([
            'timestamp_sent' => 'required|string',
            'user_fullname' => 'required|string',
            'user_email' => 'required|email',
            'user_phone' => 'string',
            'company_name' => 'required|string',
            'company_country' => 'required|string',
            'company_domain' => 'required|string',
            'user_concern' => 'required|string',
        ]);

        // fill in for user_phone for when user did not provide his phone number
        $validated['user_phone'] = isset($validated['user_phone']) ? $validated['user_phone'] : "N/A";

        $approvers = ['c8vortexsupport@calibr8systems.com'];
        foreach ($approvers as $approver) {
            $validated['approver'] = $approver;
            Mail::to($approver)->queue(new FeedbackAndSupportConcern($validated));
        }

        return $validated;
    }
}
