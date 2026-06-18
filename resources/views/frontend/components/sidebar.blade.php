<div class="sidebar-collapse">
    <ul class="nav metismenu" id="side-menu">
        <li class="nav-header">
            <div class="dropdown profile-element">
                <h4 style="color: white;">Hi! Welcome to EC+</h4>
            </div>
            <div class="logo-element">EC+</div>
        </li>

        @if(Auth::check() && Auth::user()->user_type === 'admin')
        <!-- Categories Section -->
        <li class="{{ request()->routeIs('categories.*') ? 'active' : '' }}">
            <a href="#"><i class="fa fa-tags"></i> <span class="nav-label">Categories</span> <span class="fa arrow"></span></a>
            <ul class="nav nav-second-level collapse">
                <li class="{{ request()->routeIs('categories.create') ? 'active' : '' }}"><a href="{{ route('categories.create') }}">Add Category</a></li>
                <li class="{{ request()->routeIs('categories.index') ? 'active' : '' }}"><a href="{{ route('categories.index') }}">List Category</a></li>
            </ul>
        </li>

        <!-- Brands Section -->
        <li class="{{ request()->routeIs('brands.*') ? 'active' : '' }}">
            <a href="#"><i class="fa fa-shop"></i> <span class="nav-label">Brands</span><span class="fa arrow"></span></a>
            <ul class="nav nav-second-level collapse">
                <li class="{{ request()->routeIs('brands.create') ? 'active' : '' }}"><a href="{{ route('brands.create') }}">Add Brand</a></li>
                <li class="{{ request()->routeIs('brands.index') ? 'active' : '' }}"><a href="{{ route('brands.index') }}">List Brand</a></li>
            </ul>
        </li>

        <!-- Colors Section -->
        <li class="{{ request()->routeIs('colors.*') ? 'active' : '' }}">
            <a href="#"><i class="fa fa-palette"></i> <span class="nav-label">Colors</span><span class="fa arrow"></span></a>
            <ul class="nav nav-second-level collapse">
                <li class="{{ request()->routeIs('colors.create') ? 'active' : '' }}"><a href="{{ route('colors.create') }}">Add Color</a></li>
                <li class="{{ request()->routeIs('colors.index') ? 'active' : '' }}"><a href="{{ route('colors.index') }}">List Color</a></li>
            </ul>
        </li>

        <!-- Sizes Section -->
        <li class="{{ request()->routeIs('sizes.*') ? 'active' : '' }}">
            <a href="#"><i class="fa fa-ruler-combined"></i> <span class="nav-label">Sizes</span><span class="fa arrow"></span></a>
            <ul class="nav nav-second-level collapse">
                <li class="{{ request()->routeIs('sizes.create') ? 'active' : '' }}"><a href="{{ route('sizes.create') }}">Add Size</a></li>
                <li class="{{ request()->routeIs('sizes.index') ? 'active' : '' }}"><a href="{{ route('sizes.index') }}">List Size</a></li>
            </ul>
        </li>
        @endif

        <!-- Products Section -->
        <li class="{{ request()->routeIs('products.*') ? 'active' : '' }}">
            <a href="#"><i class="fa fa-edit"></i> <span class="nav-label">Products</span><span class="fa arrow"></span></a>
            <ul class="nav nav-second-level collapse">
                <li class="{{ request()->routeIs('products.create') ? 'active' : '' }}"><a href="{{ route('products.create') }}">Add Product</a></li>
                <li class="{{ request()->routeIs('products.index') ? 'active' : '' }}"><a href="{{ route('products.index') }}">List Product</a></li>
            </ul>
        </li>
    </ul>
</div>