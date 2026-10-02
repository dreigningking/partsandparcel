<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.dash')]
#[Title('Create Blog Post — Admin Console')]
class AdminBlogPostCreate extends Component
{
    use WithFileUploads;

    public string $title = '';
    public string $excerpt = '';
    public string $content = '';
    public ?int $category_id = null;
    public string $tagsInput = '';
    public string $status = 'draft';

    public $featuredImage = null;
    public $featuredVideo = null;

    protected function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'content' => ['required', 'string', 'min:10'],
            'category_id' => ['required', 'exists:categories,id'],
            'tagsInput' => ['nullable', 'string', 'max:500'],
            'status' => ['required', 'in:draft,published,archived'],
            'featuredImage' => ['nullable', 'image', 'max:5120'], // 5MB max
            'featuredVideo' => ['nullable', 'mimes:mp4,mov,webm,ogg', 'max:51200'], // 50MB max
        ];
    }

    public function save(): mixed
    {
        $this->validate();

        $tagsString = implode(', ', array_values(array_filter(array_map('trim', explode(',', $this->tagsInput)))));

        $slug = Str::slug($this->title);
        $base = $slug ?: 'post';
        $i = 0;
        while (Post::query()->where('slug', $slug)->exists()) {
            $slug = $base . '-' . (++$i);
        }

        $post = Post::query()->create([
            'user_id' => Auth::id(),
            'category_id' => $this->category_id,
            'title' => $this->title,
            'slug' => $slug,
            'excerpt' => $this->excerpt ?: null,
            'content' => $this->content,
            'status' => $this->status,
            'published_at' => $this->status === 'published' ? now() : null,
            'tags' => $tagsString ?: null,
        ]);

        if ($this->featuredImage) {
            $post->attachMedia($this->featuredImage, 'featured');
        }

        if ($this->featuredVideo) {
            $post->attachMedia($this->featuredVideo, 'featured_video');
        }

        session()->flash('status', 'Article published/saved successfully.');

        return redirect()->route('admin.blog.show', $post);
    }

    public function render()
    {
        return view('livewire.admin.admin-blog-post-form', [
            'heading' => 'Create New Article',
            'submitLabel' => 'Publish / Save Article',
            'categories' => Category::query()->orderBy('name', 'asc')->get(['id', 'name']),
            'isEdit' => false,
            'existingImage' => null,
            'existingVideo' => null,
        ]);
    }
}
