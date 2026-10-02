<div class="space-y-6">

    <!-- SUMMERNOTE STYLES & SCRIPTS -->
    @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
        <style>
            .note-editor.note-frame {
                border-radius: 1rem !important;
                border-color: #e2e8f0 !important;
                overflow: hidden !important;
                background-color: #ffffff !important;
            }
            .note-toolbar {
                background-color: #f8fafc !important;
                border-bottom: 1px solid #e2e8f0 !important;
                padding: 0.5rem !important;
            }
            .note-statusbar {
                background-color: #f8fafc !important;
                border-top: 1px solid #e2e8f0 !important;
            }
        </style>
    @endpush

    <!-- PAGE HEADER -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-pp-600 transition">Admin Console</a>
                <span>/</span>
                <a href="{{ route('admin.blog') }}" class="hover:text-pp-600 transition">Blog</a>
                <span>/</span>
                <span class="text-slate-600 dark:text-slate-300">{{ $isEdit ? 'Edit Article' : 'Compose' }}</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-950 dark:text-white tracking-tight">
                {{ $heading }}
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                Draft rich tutorial content, automotive &amp; tech advice, and manage featured multimedia assets.
            </p>
        </div>

        <div class="flex items-center gap-3 self-start sm:self-auto">
            <a
                href="{{ route('admin.blog') }}"
                class="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 font-extrabold text-xs transition flex items-center gap-2 shadow-2xs"
            >
                <i class="fas fa-arrow-left text-slate-400"></i>
                <span>Back to Articles</span>
            </a>
        </div>
    </div>

    <!-- FLASH NOTIFICATIONS -->
    @if (session('status'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-semibold text-emerald-800 flex items-center gap-3">
            <i class="fas fa-check-circle text-emerald-500 text-base"></i>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    <form wire:submit.prevent="save" class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- LEFT COLUMN: ARTICLE CONTENT -->
        <div class="lg:col-span-8 space-y-6">

            <!-- MAIN CONTENT CARD -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 p-6 space-y-5 shadow-soft">
                
                <!-- TITLE -->
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-200">
                        Article Title <span class="text-rose-500">*</span>
                    </label>
                    <input
                        type="text"
                        wire:model="title"
                        placeholder="e.g. How to Inspect a Tokunbo Automatic Gearbox Before Purchase"
                        class="w-full p-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-sm font-extrabold text-slate-900 dark:text-white outline-none focus:border-pp-500 transition"
                    />
                    @error('title') <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- EXCERPT -->
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-200 flex items-center justify-between">
                        <span>Short Summary / Excerpt</span>
                        <span class="text-[10px] text-slate-400">Displayed on blog index and search snippets</span>
                    </label>
                    <textarea
                        wire:model="excerpt"
                        rows="3"
                        placeholder="Brief summary of the article..."
                        class="w-full p-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-xs font-medium text-slate-800 dark:text-slate-200 outline-none focus:border-pp-500 transition resize-none"
                    ></textarea>
                    @error('excerpt') <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- SUMMERNOTE RICH TEXT EDITOR -->
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-200 flex items-center justify-between">
                        <span>Content &amp; Article Body <span class="text-rose-500">*</span></span>
                        <span class="text-[10px] text-pp-600 font-bold">Rich Text Enabled (Summernote)</span>
                    </label>

                    <div wire:ignore>
                        <textarea id="summernote-editor">{!! $content !!}</textarea>
                    </div>
                    @error('content') <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- TAGS -->
                <div class="space-y-1 pt-2 border-t border-slate-100 dark:border-slate-800">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-200 flex items-center justify-between">
                        <span>Article Tags</span>
                        <span class="text-[10px] text-slate-400">Comma-separated</span>
                    </label>
                    <input
                        type="text"
                        wire:model="tagsInput"
                        placeholder="e.g. guides, how tos, automotive, gearbox, maintenance"
                        class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-xs font-medium text-slate-900 dark:text-white outline-none focus:border-pp-500 transition"
                    />
                    @error('tagsInput') <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p> @enderror
                </div>

            </div>

        </div>

        <!-- RIGHT COLUMN: SIDEBAR CONTROLS & MEDIA -->
        <div class="lg:col-span-4 space-y-6">

            <!-- PUBLISH ACTION CARD -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 p-6 space-y-4 shadow-soft">
                <h3 class="text-xs font-extrabold text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-2 border-b border-slate-100 dark:border-slate-800 pb-3">
                    <i class="fas fa-paper-plane text-pp-600"></i> Publishing Details
                </h3>

                <!-- STATUS -->
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-200">Publication Status</label>
                    <select wire:model="status" class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-xs font-bold text-slate-900 dark:text-white outline-none focus:border-pp-500 cursor-pointer">
                        <option value="draft">Draft (Private)</option>
                        <option value="published">Published (Live to Marketplace)</option>
                        <option value="archived">Archived</option>
                    </select>
                    @error('status') <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- CATEGORY -->
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-200">Category <span class="text-rose-500">*</span></label>
                    <select wire:model="category_id" class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-xs font-bold text-slate-900 dark:text-white outline-none focus:border-pp-500 cursor-pointer">
                        <option value="">-- Select Category --</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="pt-2 border-t border-slate-100 dark:border-slate-800 flex flex-col gap-2">
                    <button
                        type="submit"
                        class="w-full py-3 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-black text-xs shadow-soft transition cursor-pointer flex items-center justify-center gap-2"
                    >
                        <i class="fas fa-check"></i>
                        <span>{{ $submitLabel }}</span>
                    </button>

                    <a
                        href="{{ route('admin.blog') }}"
                        class="w-full py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold text-xs transition text-center cursor-pointer"
                    >
                        Cancel
                    </a>
                </div>
            </div>

            <!-- FEATURED MEDIA: IMAGE -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 p-6 space-y-4 shadow-soft">
                <h3 class="text-xs font-extrabold text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-2 border-b border-slate-100 dark:border-slate-800 pb-3">
                    <i class="fas fa-image text-pp-600"></i> Featured Image
                </h3>

                <!-- PREVIEW -->
                <div class="relative w-full aspect-16/9 rounded-2xl bg-slate-100 dark:bg-slate-800 overflow-hidden border border-slate-200 dark:border-slate-700 grid place-items-center">
                    @if ($featuredImage)
                        <img src="{{ $featuredImage->temporaryUrl() }}" class="w-full h-full object-cover">
                    @elseif ($existingImage)
                        <img src="{{ $existingImage }}" class="w-full h-full object-cover">
                    @else
                        <div class="text-center p-4 text-slate-400">
                            <i class="fas fa-image text-3xl mb-1 block"></i>
                            <span class="text-[11px] font-bold">No Image Selected</span>
                        </div>
                    @endif

                    <div wire:loading wire:target="featuredImage" class="absolute inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center text-white text-xs font-bold">
                        <i class="fas fa-circle-notch fa-spin text-lg mr-2"></i> Uploading...
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="w-full py-2.5 px-3 rounded-xl border border-pp-200 bg-pp-50 hover:bg-pp-100 text-pp-700 font-extrabold text-xs transition cursor-pointer flex items-center justify-center gap-1.5 shadow-2xs">
                        <i class="fas fa-upload text-pp-600"></i>
                        <span>Upload Featured Image</span>
                        <input type="file" wire:model="featuredImage" accept="image/*" class="hidden">
                    </label>

                    @if ($existingImage || $featuredImage)
                        @if ($isEdit && $existingImage)
                            <button
                                type="button"
                                wire:click="removeImage"
                                class="w-full py-1.5 rounded-xl border border-slate-200 text-rose-600 hover:bg-rose-50 font-bold text-[11px] transition cursor-pointer"
                            >
                                <i class="fas fa-trash-alt mr-1"></i> Remove Current Image
                            </button>
                        @endif
                    @endif

                    @error('featuredImage') <p class="text-xs text-rose-600 font-semibold">{{ $message }}</p> @enderror
                    <p class="text-[10px] text-slate-400">Recommended size: 1200x630. Max size: 5MB.</p>
                </div>
            </div>

            <!-- FEATURED MEDIA: VIDEO -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 p-6 space-y-4 shadow-soft">
                <h3 class="text-xs font-extrabold text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-2 border-b border-slate-100 dark:border-slate-800 pb-3">
                    <i class="fas fa-video text-pp-600"></i> Featured Video (Optional)
                </h3>

                @if ($existingVideo)
                    <div class="rounded-xl overflow-hidden bg-black aspect-16/9">
                        <video src="{{ $existingVideo }}" controls class="w-full h-full object-contain"></video>
                    </div>
                    @if ($isEdit)
                        <button
                            type="button"
                            wire:click="removeVideo"
                            class="w-full py-1.5 rounded-xl border border-slate-200 text-rose-600 hover:bg-rose-50 font-bold text-[11px] transition cursor-pointer"
                        >
                            <i class="fas fa-trash-alt mr-1"></i> Remove Video
                        </button>
                    @endif
                @endif

                <div class="space-y-2">
                    <label class="w-full py-2.5 px-3 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700 font-extrabold text-xs transition cursor-pointer flex items-center justify-center gap-1.5">
                        <i class="fas fa-file-video text-slate-500"></i>
                        <span>Upload MP4 / WebM Video</span>
                        <input type="file" wire:model="featuredVideo" accept="video/mp4,video/webm,video/ogg" class="hidden">
                    </label>

                    <div wire:loading wire:target="featuredVideo" class="text-xs text-pp-600 font-bold flex items-center gap-1.5">
                        <i class="fas fa-circle-notch fa-spin"></i> Uploading video file...
                    </div>

                    @error('featuredVideo') <p class="text-xs text-rose-600 font-semibold">{{ $message }}</p> @enderror
                    <p class="text-[10px] text-slate-400">Supported formats: MP4, WebM. Max size: 50MB.</p>
                </div>
            </div>

        </div>

    </form>

    <!-- SUMMERNOTE JAVASCRIPT INITIALIZATION -->
    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
        <script>
            document.addEventListener('livewire:navigated', initSummernote);
            document.addEventListener('DOMContentLoaded', initSummernote);

            function initSummernote() {
                if (typeof $ === 'undefined' || typeof $.fn.summernote === 'undefined') {
                    return;
                }

                const editorEl = $('#summernote-editor');
                if (editorEl.length === 0) return;

                // Destroy any previous instance if already initialized
                if (editorEl.next('.note-editor').length > 0) {
                    editorEl.summernote('destroy');
                }

                editorEl.summernote({
                    placeholder: 'Write your post content, step-by-step diagnostic guide, or technical breakdown...',
                    tabsize: 2,
                    height: 420,
                    toolbar: [
                        ['style', ['style']],
                        ['font', ['bold', 'italic', 'underline', 'clear']],
                        ['color', ['color']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['table', ['table']],
                        ['insert', ['link', 'picture', 'video']],
                        ['view', ['fullscreen', 'codeview', 'help']]
                    ],
                    callbacks: {
                        onChange: function(contents, $editable) {
                            @this.set('content', contents);
                        }
                    }
                });

                // Sync initial content
                let initial = @this.get('content');
                if (initial && editorEl.summernote('code') !== initial) {
                    editorEl.summernote('code', initial);
                }
            }
        </script>
    @endpush

</div>
