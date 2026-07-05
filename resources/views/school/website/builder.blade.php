<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Website Builder - {{ $page->title }}</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <!-- GrapesJS Stylesheets -->
    <link href="https://unpkg.com/grapesjs/dist/css/grapes.min.css" rel="stylesheet">
    
    <style>
        body, html {
            height: 100%;
            margin: 0;
            overflow: hidden;
            font-family: 'Inter', sans-serif;
            background-color: #1e1e24;
            color: #ffffff;
        }

        /* Top Header Bar */
        .builder-header {
            height: 60px;
            background-color: #151518;
            border-bottom: 1px solid #2b2b35;
            display: flex;
            align-items: center;
            justify-content: justify;
            padding: 0 20px;
            z-index: 10;
        }

        .builder-title {
            font-weight: 700;
            font-size: 1.1rem;
            color: #ffffff;
        }

        .builder-status {
            font-size: 0.8rem;
            color: #8b8b9f;
        }

        /* Main Builder Wrapper */
        .builder-wrapper {
            height: calc(100% - 60px);
            position: relative;
            display: flex;
        }

        /* GrapesJS Canvas container */
        #gjs {
            border: none;
            width: 100%;
            height: 100%;
        }

        /* Style tweaks for GrapesJS panels */
        .gjs-one-bg {
            background-color: #151518 !important;
        }
        .gjs-two-color {
            color: #a5a5b5 !important;
        }
        .gjs-three-bg {
            background-color: #2b2b35 !important;
        }
        .gjs-four-color, .gjs-four-color-h:hover {
            color: #2563eb !important;
        }
        .gjs-pn-panels {
            border-bottom: 1px solid #2b2b35 !important;
        }
    </style>
</head>
<body>

    <!-- Header Toolbar -->
    <header class="builder-header d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <a href="{{ route('school.website.pages.index') }}" class="btn btn-sm btn-outline-light me-3">
                <i class="bi bi-chevron-left"></i> Exit Editor
            </a>
            <div>
                <span class="builder-title">{{ $page->title }}</span>
                <span class="ms-2 badge bg-secondary text-uppercase" style="font-size:0.65rem;">{{ $page->page_type }} page</span>
                <div class="builder-status mt-1">
                    <span id="save-status-indicator" class="text-success"><i class="bi bi-cloud-check-fill"></i> Loaded draft revision #{{ $revision->revision_number }}</span>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="button" class="btn btn-sm btn-outline-light px-3" id="save-draft-btn">
                <i class="bi bi-save me-1"></i> Save Draft
            </button>

            <form action="{{ route('school.website.pages.publish', $page->id) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-sm btn-success px-4 fw-bold">
                    <i class="bi bi-send-check me-1"></i> Publish Live
                </button>
            </form>
        </div>
    </header>

    <!-- GrapesJS Workspace -->
    <div class="builder-wrapper">
        <div id="gjs"></div>
    </div>

    <!-- GrapesJS Scripts CDN -->
    <script src="https://unpkg.com/grapesjs"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const saveBtn = document.getElementById('save-draft-btn');
            const saveIndicator = document.getElementById('save-status-indicator');

            // Initialize GrapesJS editor
            const editor = grapesjs.init({
                container: '#gjs',
                height: '100%',
                fromElement: false,
                components: {!! $revision->components_json !!},
                style: `{!! $revision->css_content !!}`,
                canvas: {
                    styles: [
                        'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css',
                        'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css',
                        '{{ route("school.website.branding-css", $page->school_id) }}'
                    ]
                },
                storageManager: false, // Handle storage via custom API triggers
                blockManager: {
                    appendTo: '#blocks',
                    blocks: [
                        @foreach($blocks as $block)
                        {
                            id: '{{ $block->slug }}',
                            label: '{{ $block->name }}',
                            category: '{{ $block->category }}',
                            content: `{!! trim($block->html_template) !!}`,
                            attributes: { class: 'gjs-block-custom' }
                        },
                        @endforeach
                    ]
                }
            });

            // Handle manual save
            saveBtn.addEventListener('click', saveDraft);

            // Auto-save every 60 seconds
            setInterval(saveDraft, 60000);

            function saveDraft() {
                saveIndicator.className = 'text-warning';
                saveIndicator.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Saving draft...';

                const html = editor.getHtml();
                const css = editor.getCss();
                const components = JSON.stringify(editor.getComponents());

                fetch('{{ route("school.website.pages.save", $page->id) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        html: html,
                        css: css,
                        components: components
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        saveIndicator.className = 'text-success';
                        saveIndicator.innerHTML = '<i class="bi bi-cloud-check-fill"></i> Draft saved successfully (' + new Date().toLocaleTimeString() + ')';
                    } else {
                        saveIndicator.className = 'text-danger';
                        saveIndicator.innerHTML = '<i class="bi bi-exclamation-triangle-fill"></i> Save failed: ' + (data.error || 'Server error');
                    }
                })
                .catch(err => {
                    saveIndicator.className = 'text-danger';
                    saveIndicator.innerHTML = '<i class="bi bi-exclamation-triangle-fill"></i> Network error during save';
                });
            }
        });
    </script>
</body>
</html>
