<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #362258;">
  <div class="container-fluid">
    <a class="navbar-brand text-white" href="{{ route('orders.index')}}">Panel</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link text-white" href="{{ route('orders.index')}}">
            Commandes
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white" href="{{ route('user.index')}}">Gestion Utilisateur</a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white" href="{{ route('prospect.index')}}">Gestion Prospect</a>
        </li>
      </ul>

      @if (Auth::check())
      <div class="d-flex align-items-center ms-auto">
        <span class="me-3 my-2 my-lg-0 text-white">{{ Auth::user()->name }}</span>
        <form class="d-flex my-2 my-lg-0" action="{{ route('auth.logout') }}" method="POST">
          @csrf
          <button class="btn btn-outline-light border-0" type="submit">
              <i class="bi bi-box-arrow-right"></i>
          </button>
      </form>
      </div>
      @endif 
    </div>
  </div>
</nav>
