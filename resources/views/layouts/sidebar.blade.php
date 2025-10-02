            <!-- Sidebar -->
            <div class="toggleSidebar">
            <svg xmlns="http://www.w3.org/2000/svg"  viewBox="0 0 72 72" width="128px" height="128px"><path d="M56 48c2.209 0 4 1.791 4 4 0 2.209-1.791 4-4 4-1.202 0-38.798 0-40 0-2.209 0-4-1.791-4-4 0-2.209 1.791-4 4-4C17.202 48 54.798 48 56 48zM56 32c2.209 0 4 1.791 4 4 0 2.209-1.791 4-4 4-1.202 0-38.798 0-40 0-2.209 0-4-1.791-4-4 0-2.209 1.791-4 4-4C17.202 32 54.798 32 56 32zM56 16c2.209 0 4 1.791 4 4 0 2.209-1.791 4-4 4-1.202 0-38.798 0-40 0-2.209 0-4-1.791-4-4 0-2.209 1.791-4 4-4C17.202 16 54.798 16 56 16z"/></svg>
            </div>
            <div class="sidebar dashSidebar">
              <div class="logo">
               <a href="{{url('/')}}"> <img src="{{url('/assets/img/Logo.png')}}" alt="Login image" class="img-fluid logo-img mb-5"></a>
              </div>
              <nav class="nav flex-column">
                <a href="{{ url('/dashboard') }}" class="nav-link active">
                    <svg width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M0 0H8.36841V8.36841H0V0ZM10.4605 0H18.8289V8.36841H10.4605V0ZM0 10.4605H8.36841V18.8289H0V10.4605ZM13.5987 10.4605H15.6908V13.5987H18.8289V15.6908H15.6908V18.8289H13.5987V15.6908H10.4605V13.5987H13.5987V10.4605ZM12.5526 2.0921V6.27631H16.7368V2.0921H12.5526ZM2.0921 2.0921V6.27631H6.27631V2.0921H2.0921ZM2.0921 12.5526V16.7368H6.27631V12.5526H2.0921Z" fill="#8C8C8C"/>
                    </svg>Dashboard</a>
                <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="nav-link">
                    <svg width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M2.22754 18.7828C1.67754 18.7828 1.20671 18.5869 0.815039 18.1953C0.423372 17.8036 0.227539 17.3328 0.227539 16.7828V2.78278C0.227539 2.23278 0.423372 1.76194 0.815039 1.37028C1.20671 0.978609 1.67754 0.782776 2.22754 0.782776H9.22754V2.78278H2.22754V16.7828H9.22754V18.7828H2.22754ZM13.2275 14.7828L11.8525 13.3328L14.4025 10.7828H6.22754V8.78278H14.4025L11.8525 6.23278L13.2275 4.78278L18.2275 9.78278L13.2275 14.7828Z" fill="#8C8C8C"/>
                    </svg>

                    Logout</a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
              </nav>
            </div>
