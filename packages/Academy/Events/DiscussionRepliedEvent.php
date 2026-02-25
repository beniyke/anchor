<?php

declare(strict_types=1);

namespace Academy\Events;

use Academy\Models\AcademyDiscussion;
use Academy\Models\AcademyDiscussionReply;

class DiscussionRepliedEvent
{
    public function __construct(public AcademyDiscussion $discussion, public AcademyDiscussionReply $reply)
    {
    }
}
