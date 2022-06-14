<?php

namespace App\Services;

use App\Enums\ReactionType;
use App\Models\Post;

class PostService
{
    public function getBaseQuery(int $postId = null): array
    {
        $posts = Post::query();

        if (isset($postId)) {
            $posts = $posts->where('id', $postId);
        }
        $posts = $posts->with(['user', 'tags', 'attachments', 'parent', 'child'])
            ->get()
            ->toArray();

        $reactionsCount = Post::query();
        foreach (ReactionType::cases() as $type) {
            $reactionsCount = $reactionsCount->withCount([
                "reactions as {$type->value}" => function ($query) use ($type) {
                    $query->where('type', $type->value);
                },
            ]);
        }

        $postReaction = $reactionsCount->get();

        foreach ($posts as $key => $post) {
            foreach ($postReaction as $reaction) {
                if ($post['id'] === $reaction['id']) { // @phpstan-ignore-line
                    $posts[$key]['reactions'] = array_filter( // @phpstan-ignore-line
                        $reaction->toArray(), function ($value, $key) {
                        return in_array($key, ReactionType::values());
                    }, ARRAY_FILTER_USE_BOTH);
                }
            }
        }
        return $posts;
    }

    public function create(array $data): Post
    {
        $post = Post::create($data);

        $post->tags()->attach($data['tag_ids']);
        $post->save();

        if (isset($data['attachments'])) {
            $this->uploadAttachments($post, $data['attachments']);
        }

        return $post;
    }

    public function update(array $data, Post $post): Post
    {
        if (isset($data['attachments'])) {
            $this->uploadAttachments($post, $data['attachments']);
            unset($data['attachments']);
        }

        if (isset($data['tag_ids'])) {
            $post->tags()->attach($data['tag_ids']);
            $post->save();
        }

        $post->update($data);

        return $post;
    }

    /**
     * @param Post $post
     * @param \Illuminate\Http\UploadedFile[] $attachments
     */
    public function uploadAttachments($post, $attachments): void
    {
        $files = [];

        foreach ($attachments as $file) {
            $original = $file->getClientOriginalName();
            $filename = pathinfo($original, PATHINFO_FILENAME);
            $filename = $post->id . str($filename)->slug() . '-' . time(); // @phpstan-ignore-line
            $extension = pathinfo($original, PATHINFO_EXTENSION);
            $picturePath = $file->storeAs('attachments', "$filename.$extension");

            $files[]['file'] = \Illuminate\Support\Facades\Storage::url((string)$picturePath);
        }

        $post->attachments()->createMany($files);
    }
}
