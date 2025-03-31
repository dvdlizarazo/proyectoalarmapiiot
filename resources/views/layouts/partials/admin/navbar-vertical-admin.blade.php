 <!-- Sidebar -->
 <nav class="navbar-vertical navbar">
     <div class="nav-scroller">
         <!-- Brand logo -->
         <a class="navbar-brand">
             <img src="{{ asset('admin_assets/images/layouts/logo_principal.png') }}" alt="" />
         </a>
         <!-- Navbar nav -->
         <ul class="navbar-nav flex-column" id="sideNavbar">
             <li class="nav-item">
                 <a class="nav-link has-arrow" href="{{ route('admin.home') }}">
                     <i data-feather="home" class="nav-icon icon-xs me-2"></i>Admin Dashboard
                 </a>

             </li>


             <li class="nav-item">
                <a class="nav-link has-arrow" href="{{ route('admin.users.index') }}">
                    <i class="nav-icon icon-xs me-2" data-feather="users"></i>Gestión usuarios
                </a>
            </li>

             <!-- Nav item -->
             <li class="nav-item">
                 <div class="navbar-heading"></div>
             </li>
             <!-- Nav item -->
             
         </ul>
     </div>
 </nav>
