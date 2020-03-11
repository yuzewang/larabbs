<?php

namespace App\Observers;

use App\Models\Reply;
use App\Notifications\TopicReplied;

// creating, created, updating, updated, saving,
// saved,  deleting, deleted, restoring, restored

class ReplyObserver
{
    public function created(Reply $reply)
    {
        $reply->topic->reply_count = $reply->topic->replies->count();
        $reply->topic->save();
        // 通知话题作者有新的评论
        $reply->topic->user->notify(new TopicReplied($reply));
    }

    public function saving(Reply $reply)
    {
        // xss 过滤
        $reply->content = clean($reply->content, 'default');

        // 判断内容为空的处理方式，拒绝保存入库
        if ($reply->content === '') {
            return false;
        }
    }


}
