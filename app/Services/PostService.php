<?php

namespace App\Services;

use App\Enums\ReactionType;
use App\Models\Post;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class PostService
{
    public function getPostsWithMediaCount(Request $request): LengthAwarePaginator
    {
        $search = $request->input('search');
        $sorting = $request->input('sorting');
        $posts = Post::query()
            ->with('user', 'tags', 'parent', 'child')
            ->withCount([
                'comments as comments',
                'attachments as attachments',
                'reactions as reactions',
            ]);

        if (isset($search)) {
            $posts->where('title', 'ILIKE', "%{$search}%")
                ->orWhere('content', 'ILIKE', "%{$search}%");
        }
        if (isset($sorting)) {
            $sorting = explode(',', $sorting);

            if (count($sorting) === 1) {
                $sorting[] = 'desc';
            }
            $posts->orderBy($sorting[0], $sorting[1]);
        }

        return $posts->paginate(15);
    }

    private function getCounts(): array
    {
        $counts = [];
        foreach (ReactionType::cases() as $type) {
            $counts["reactions as {$type->value}"] = fn ($query) => $query->where('type', $type->value);
        }

        return $counts;
    }

    /**
     * @return Post|Post[] Posts
     */
    public function getBaseQuery(int $postId = null)
    {
        $counts = [];
        foreach (ReactionType::cases() as $type) {
            $counts["reactions as {$type->value}"] = fn ($query) => $query->where('type', $type->value);
        }

        if ((bool) $postId) {
            return $this->withReactions(Post::find($postId));
        }

        /**
         * @var Post[] $posts
         */
        $posts = [];
        foreach (Post::all() as $post) {
            $posts[] = $this->withReactions($post);
        }

        return $posts;
    }

    public function withReactions(Post $post): Post
    {
        $copy = Post::find($post->id);
        $copy->loadCount($this->getCounts());

        $post->reactions = array_filter(
            $copy->toArray(),
            fn ($value, $key) => in_array($key, ReactionType::values()),
            ARRAY_FILTER_USE_BOTH
        );

        return $post->load(['user', 'tags', 'attachments', 'parent', 'child', 'comments']);
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
     * @param  Post  $post
     * @param  \Illuminate\Http\UploadedFile[]  $attachments
     */
    public function uploadAttachments($post, $attachments): void
    {
        $files = [];

        foreach ($attachments as $file) {
            $original = $file->getClientOriginalName();
            $filename = pathinfo($original, PATHINFO_FILENAME);
            $filename = $post->id.str($filename)->slug().'-'.time();
            $extension = pathinfo($original, PATHINFO_EXTENSION);
            $picturePath = $file->storeAs('attachments', "$filename.$extension");

            $files[]['file'] = \Illuminate\Support\Facades\Storage::url((string) $picturePath);
        }

        $post->attachments()->createMany($files);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<\App\Models\Post> $posts
     */
    public function getListing()
    {
        $posts = Post::has('child')
            ->doesntHave('parent')
            ->with(['user', 'tags'])
            ->get();

        foreach ($posts as $post) {
            $depth = 0;
            $child = $post;
            while ($child->child != null) {
                $child = Post::findOrFail($child->child->id);
                $depth++;
            }
            $post->child_depth = $depth;
        }

        return $posts;
    }
}
