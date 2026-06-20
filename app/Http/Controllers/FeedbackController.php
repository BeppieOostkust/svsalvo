<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Models\FeedbackComment;
use App\Models\FeedbackVote;
use App\Models\User;
use App\Support\PublicStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class FeedbackController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Feedback::with(['user', 'moderator'])
            ->withCount('comments')
            ->public()
            ->latest();

        // Filters
        if ($request->has('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'votes':
                    $query->orderByRaw('(upvotes - downvotes) DESC');
                    break;
                case 'oldest':
                    $query->oldest();
                    break;
                case 'newest':
                default:
                    $query->latest();
                    break;
            }
        }

        $feedback = $query->paginate(10);
        $feedback->getCollection()->each(function (Feedback $item) {
            $this->exposeFeedbackUserImages($item);
        });

        return Inertia::render('Feedback/Index', [
            'feedback' => $feedback,
            'filters' => $request->only(['type', 'status', 'sort']),
            'stats' => [
                'total' => Feedback::public()->count(),
                'pending' => Feedback::where('status', 'pending')->count(),
                'approved' => Feedback::where('status', 'approved')->count(),
                'implemented' => Feedback::where('status', 'implemented')->count(),
            ]
        ]);
    }

    public function show(Feedback $feedback): Response
    {
        // Check if user can view this feedback
        if (!in_array($feedback->status, ['approved', 'implemented', 'under_review']) && 
            $feedback->user_id !== Auth::id() && 
            !Auth::user()->is_admin) {
            abort(404);
        }

        $feedback->load([
            'user',
            'moderator',
            'comments' => function ($query) {
                $query->public()->with('user')->latest();
            },
            'votes'
        ]);

        $userVote = null;
        if (Auth::check()) {
            $userVote = FeedbackVote::where('feedback_id', $feedback->id)
                ->where('user_id', Auth::id())
                ->first();
        }

        return Inertia::render('Feedback/Show', [
            'feedback' => $this->exposeFeedbackUserImages($feedback),
            'userVote' => $userVote,
            'auth' => [
                'user' => Auth::user() ? PublicStorage::expose(Auth::user(), 'profile_image') : null,
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Feedback/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'type' => 'required|in:idea,feedback,suggestion,bug_report,feature_request',
            'is_anonymous' => 'boolean',
        ]);

        $feedback = Feedback::create([
            ...$validated,
            'user_id' => Auth::id(),
            'status' => 'pending',
        ]);

        return redirect()->route('feedback.show', $feedback)
            ->with('message', 'Je feedback is succesvol ingediend en wordt binnenkort beoordeeld.');
    }

    public function vote(Request $request, Feedback $feedback)
    {
        $validated = $request->validate([
            'vote_type' => 'required|in:upvote,downvote',
        ]);

        $existingVote = FeedbackVote::where('feedback_id', $feedback->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existingVote) {
            if ($existingVote->vote_type === $validated['vote_type']) {
                // Remove vote if same type
                $existingVote->delete();
                $this->updateVoteCounts($feedback);
                return back()->with('message', 'Stem ingetrokken.');
            } else {
                // Update vote type
                $existingVote->update($validated);
                $this->updateVoteCounts($feedback);
                return back()->with('message', 'Stem bijgewerkt.');
            }
        }

        // Create new vote
        FeedbackVote::create([
            'feedback_id' => $feedback->id,
            'user_id' => Auth::id(),
            'vote_type' => $validated['vote_type'],
        ]);

        $this->updateVoteCounts($feedback);

        return back()->with('message', 'Stem toegevoegd.');
    }

    public function addComment(Request $request, Feedback $feedback)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        FeedbackComment::create([
            'content' => $validated['content'],
            'feedback_id' => $feedback->id,
            'user_id' => Auth::id(),
            'is_moderator_comment' => Auth::user()->is_admin,
        ]);

        return back()->with('message', 'Reactie toegevoegd.');
    }

    public function deleteComment(FeedbackComment $comment)
    {
        // Check if user can delete this comment
        if ($comment->user_id !== Auth::id() && !Auth::user()->is_admin) {
            abort(403, 'Je hebt geen toestemming om deze reactie te verwijderen.');
        }

        $comment->delete();

        return back()->with('message', 'Reactie verwijderd.');
    }

    private function updateVoteCounts(Feedback $feedback)
    {
        $upvotes = FeedbackVote::where('feedback_id', $feedback->id)
            ->where('vote_type', 'upvote')
            ->count();

        $downvotes = FeedbackVote::where('feedback_id', $feedback->id)
            ->where('vote_type', 'downvote')
            ->count();

        $feedback->update([
            'upvotes' => $upvotes,
            'downvotes' => $downvotes,
        ]);
    }

    private function exposeFeedbackUserImages(Feedback $feedback): Feedback
    {
        foreach (['user', 'moderator'] as $relation) {
            if ($feedback->relationLoaded($relation) && $feedback->{$relation} instanceof User) {
                PublicStorage::expose($feedback->{$relation}, 'profile_image');
            }
        }

        if ($feedback->relationLoaded('comments')) {
            $feedback->comments->each(function (FeedbackComment $comment) {
                if ($comment->relationLoaded('user') && $comment->user instanceof User) {
                    PublicStorage::expose($comment->user, 'profile_image');
                }
            });
        }

        return $feedback;
    }
}
