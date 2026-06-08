<div class="col-lg-10">
    <!-- Displays the page title variable, or defaults to 'Dashboard' -->
    <h2>{{ $pageTitle ?? 'Dashboard' }}</h2>
    
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ url('/') }}">Home</a>
        </li>
        
        <!-- Loop through the breadcrumbs array if it exists -->
        @if(isset($breadcrumbs) && is_array($breadcrumbs))
            @foreach($breadcrumbs as $label => $url)
                @if($loop->last || $url === '#')
                    <li class="breadcrumb-item active">
                        <strong>{{ $label }}</strong>
                    </li>
                @else
                    <li class="breadcrumb-item">
                        <a href="{{ $url }}">{{ $label }}</a>
                    </li>
                @endif
            @endforeach
        @endif
    </ol>
</div>
<div class="col-lg-2"></div>