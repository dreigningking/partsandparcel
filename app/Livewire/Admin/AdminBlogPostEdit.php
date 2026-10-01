<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use App\Models\Media;
use App\Models\Post;
use App\Models\Listing;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class AdminBlogPostEdit extends Component
{
    use WithFileUploads;

    public Post $post;

    public string $title = '';

    public string $excerpt = '';

    public string $content = '';

    public ?int $category_id = null;

    public ?int $listing_id = null;

    public string $tagsInput = '';

    public string $status = 'draft';

    public string $content_source = 'manual';

    public $featuredImage;

    public function mount(Post $post): void
    {
        $this->post = $post;
        $this->title = $post->title;
        $this->excerpt = (string) ($post->excerpt ?? '');
        $this->content = $post->content;
        $this->category_id = $post->category_id;
        $this->listing_id = $post->listing_id;
        $this->tagsInput = is_string($post->tags) ? $post->tags : (is_array($post->tags) ? implode(', ', $post->tags) : '');
        $this->status = $post->status;
        $this->content_source = $post->content_source ?? 'manual';
    }

    protected function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'content' => ['required', 'string', 'min:20'],
            'category_id' => ['required', 'exists:categories,id'],
            'listing_id' => ['nullable', 'exists:properties,id'],
            'tagsInput' => ['nullable', 'string', 'max:500'],
            'status' => ['required', 'in:draft,published,archived'],
            'content_source' => ['required', 'in:manual,ai'],
            'featuredImage' => ['nullable', 'image', 'max:5120'],
        ];
    }

    public function save(): mixed
    {
        $this->validate();

        $tagsString = implode(', ', array_values(array_filter(array_map('trim', explode(',', $this->tagsInput)))));

        $slug = Str::slug($this->title);
        if ($slug !== $this->post->slug) {
            $base = $slug;
            $i = 0;
            while (Post::query()->where('slug', $slug)->where('id', '!=', $this->post->id)->exists()) {
                $slug = $base.'-'.(++$i);
            }
        } else {
            $slug = $this->post->slug;
        }

        $publishedAt = $this->post->published_at;
        if ($this->status === 'published') {
            $publishedAt = $publishedAt ?? now();
        } elseif ($this->status === 'draft') {
            $publishedAt = null;
        }

        $this->post->update([
            'title' => $this->title,
            'slug' => $slug,
            'excerpt' => $this->excerpt ?: null,
            'content' => $this->content,
            'category_id' => $this->category_id,
            'listing_id' => $this->listing_id,
            'status' => $this->status,
            'published_at' => $publishedAt,
            'tags'         => $tagsString ?: null,
            'content_source' => $this->content_source,
        ]);

        if ($this->featuredImage) {
            $this->post->media()->where('is_primary', true)->delete();
            $path = $this->featuredImage->store('posts', 'public');
            Media::query()->create([
                'user_id' => Auth::id(),
                'mediable_id' => $this->post->id,
                'mediable_type' => Post::class,
                'name' => $this->post->title ?: 'Featured image',
                'path' => $path,
                'type' => 'image',
                'mime_type' => $this->featuredImage->getMimeType(),
                'size' => (string) $this->featuredImage->getSize(),
                'extension' => $this->featuredImage->getClientOriginalExtension(),
                'is_primary' => true,
            ]);
        }

        session()->flash('status', __('Post updated.'));

        return redirect()->route('admin.blog.show', ['post' => $this->post->id]);
    }

    public function render()
    {
        return view('livewire.admin.admin-blog-post-form', [
            'categories' => Category::query()->orderBy('name', 'asc')->get(),
            'properties' => Listing::query()->orderBy('name', 'asc')->limit(200)->get(['id', 'name']),
            'heading' => __('Edit post'),
            'submitLabel' => __('Save'),
        ]);
    }
}
