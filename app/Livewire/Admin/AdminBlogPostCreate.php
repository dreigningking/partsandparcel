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

class AdminBlogPostCreate extends Component
{
    use WithFileUploads;

    public string $title = '';

    public string $excerpt = '';

    public string $content = '';

    public ?int $category_id = null;

    public ?int $listing_id = null;

    public string $tagsInput = '';

    public string $status = 'draft';

    public string $content_source = 'manual';

    public $featuredImage;

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
        $base = $slug;
        $i = 0;
        while (Post::query()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.(++$i);
        }

        $post = Post::query()->create([
            'user_id' => Auth::id(),
            'category_id' => $this->category_id,
            'listing_id' => $this->listing_id,
            'title' => $this->title,
            'slug' => $slug,
            'excerpt' => $this->excerpt ?: null,
            'content' => $this->content,
            'content_source' => $this->content_source,
            'status' => $this->status,
            'published_at' => $this->status === 'published' ? now() : null,
            'tags'           => $tagsString ?: null,
        ]);

        if ($this->featuredImage) {
            $path = $this->featuredImage->store('posts', 'public');
            Media::query()->create([
                'user_id' => Auth::id(),
                'mediable_id' => $post->id,
                'mediable_type' => Post::class,
                'name' => $post->title ?: 'Featured image',
                'path' => $path,
                'type' => 'image',
                'mime_type' => $this->featuredImage->getMimeType(),
                'size' => (string) $this->featuredImage->getSize(),
                'extension' => $this->featuredImage->getClientOriginalExtension(),
                'is_primary' => true,
            ]);
        }

        session()->flash('status', __('Post created.'));

        return redirect()->route('admin.blog.show', ['post' => $post->id]);
    }

    public function render()
    {
        return view('livewire.admin.admin-blog-post-form', [
            'categories' => Category::query()->orderBy('name', 'asc')->get(),
            'properties' => Listing::query()->orderBy('name', 'asc')->limit(200)->get(['id', 'name']),
            'heading' => __('Create post'),
            'submitLabel' => __('Create'),
        ]);
    }
}
