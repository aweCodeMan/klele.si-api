<?php

namespace App\Projectors;

use App\Models\Score;
use App\Models\Vote;
use App\StorableEvents\CommentCreated;
use App\StorableEvents\LinkPostCreated;
use App\StorableEvents\MarkdownPostCreated;
use App\StorableEvents\VoteSubmitted;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class VoteProjector extends Projector
{
    public function onStartingEventReplay()
    {
        Score::truncate();
        Vote::truncate();
    }

    public function onCommentCreated(CommentCreated $event)
    {
        Score::create(['uuid' => $event->aggregateRootUuid()]);
    }

    public function onLinkPostCreated(LinkPostCreated $event)
    {
        Score::create(['uuid' => $event->aggregateRootUuid()]);
    }

    public function onMarkdownPostCreated(MarkdownPostCreated $event)
    {
        Score::create(['uuid' => $event->aggregateRootUuid()]);
    }

    public function onVoteSubmitted(VoteSubmitted $event)
    {
        $vote = Vote::where('uuid', $event->aggregateRootUuid())
            ->where('user_uuid', $event->data->user_uuid)
            ->first();

        $offset = 0;

        if ($vote) {
            $offset = $this->getOffsetFromVote($vote);
        }

        switch ($event->data->vote) {
            case Vote::UPVOTE:
            case Vote::DOWNVOTE:

                Vote::updateOrCreate(['uuid' => $event->aggregateRootUuid(), 'user_uuid' => $event->data->user_uuid], [
                    'type' => $event->data->type,
                    'vote' => $event->data->vote,
                ]);

                $this->updateScore($event->aggregateRootUuid(), $event->data->vote + $offset);
                break;
            case Vote::NEUTRAL:
                $this->updateScore($event->aggregateRootUuid(), $offset);
                Vote::where('uuid', $event->aggregateRootUuid())->where('user_uuid', $event->data->user_uuid)->delete();
                break;
        }
    }

    private function getOffsetFromVote(Vote $vote)
    {
        return $vote->vote * -1;
    }

    private function updateScore(string $aggregateRootUuid, int $vote)
    {
        $score = Score::where('uuid', $aggregateRootUuid)->first();

        if ($score) {
            $score->votes += $vote;
            $score->save();
        } else {
            Score::create(['uuid' => $aggregateRootUuid, 'votes' => $vote]);
        }
    }
}
