<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.dash')]
#[Title('Edit Blog Post — Admin Console')]
class AdminBlogPostEdit extends Component
{
    use WithFileUploads;

    public Post $post;

    public string $title = '';
    public string $excerpt = '';
    public string $content = '';
    public ?int $category_id = null;
    public string $tagsInput = '';
    public string $status = 'draft';

    public $featuredImage = null;
    public $featuredVideo = null;

    public function mount(Post $post): void
    {
        $this->post = $post;
        $this->title = $post->title;
        $this->excerpt = (string) ($post->excerpt ?? '');
        $this->content = $post->content;
        $this->category_id = $post->category_id;
        $this->tagsInput = is_string($post->tags) ? $post->tags : (is_array($post->tags) ? implode(', ', $post->tags) : '');
        $this->status = $post->status;
    }

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

    public function removeImage(): void
    {
        $this->post->clearMediaCollection('featured');
        $this->post->clearMediaCollection('default');
        $this->featuredImage = null;
        session()->flash('status', 'Featured image removed.');
    }

    public function removeVideo(): void
    {
        $this->post->clearMediaCollection('featured_video');
        $this->featuredVideo = null;
        session()->flash('status', 'Featured video removed.');
    }

    public function save(): mixed
    {
        $this->validate();

        $tagsString = implode(', ', array_values(array_filter(array_map('trim', explode(',', $this->tagsInput)))));

        // Update slug if title changed
        $slug = $this->post->slug;
        if (trim($this->title) !== trim($this->post->title)) {
            $base = Str::slug($this->title) ?: 'post';
            $slug = $base;
            $i = 0;
            while (Post::query()->where('slug', $slug)->where('id', '!=', $this->post->id)->exists()) {
                $slug = $base . '-' . (++$i);
            }
        }

        $wasDraft = $this->post->status !== 'published';
        $nowPublished = $this->status === 'published';

        $this->post->update([
            'category_id' => $this->category_id,
            'title' => $this->title,
            'slug' => $slug,
            'excerpt' => $this->excerpt ?: null,
            'content' => $this->content,
            'status' => $this->status,
            'published_at' => ($wasDraft && $nowPublished) ? ($this->post->published_at ?: now()) : $this->post->published_at,
            'tags' => $tagsString ?: null,
        ]);

        if ($this->featuredImage) {
            $this->post->clearMediaCollection('featured');
            $this->post->clearMediaCollection('default');
            $this->post->attachMedia($this->featuredImage, 'featured');
        }

        if ($this->featuredVideo) {
            $this->post->clearMediaCollection('featured_video');
            $this->post->attachMedia($this->featuredVideo, 'featured_video');
        }

        session()->flash('status', 'Article updated successfully.');

        return redirect()->route('admin.blog.show', $this->post);
    }

    public function render()
    {
        $existingImage = $this->post->featured_image_url;
        $existingVideo = $this->post->featured_video_url;

        return view('livewire.admin.admin-blog-post-form', [
            'heading' => 'Edit Article: ' . Str::limit($this->post->title, 40),
            'submitLabel' => 'Save Changes',
            'categories' => Category::query()->orderBy('name', 'asc')->get(['id', 'name']),
            'isEdit' => true,
            'existingImage' => $existingImage,
            'existingVideo' => $existingVideo,
        ]);
    }
}
