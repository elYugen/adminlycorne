<nav class="navbar navbar-expand-lg bg-body-tertiary">
    <div class="container-fluid">
      <a class="navbar-brand" href="{{ route('dashboard.index')}}">Panel</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              Commandes
            </a>
            <ul class="dropdown-menu">
             <li><a class="dropdown-item" href="{{ route('orders.index')}}">Gestion des commandes</a></li>
            </ul>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ route('user.index')}}">Gestion Utilisateur</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ route('prospect.index')}}">Gestion Prospect</a>
          </li>
          <li class="nav-item">
            <a class="nav-link disabled" aria-disabled="true">Disabled</a>
          </li>
        </ul>

        @if (Auth::check())
        <div class="d-flex align-items-center ms-auto">
          <span class="me-3 my-2 my-lg-0">{{ Auth::user()->name }}</span>
          <form class="d-flex my-2 my-lg-0" action="{{ route('auth.logout') }}" method="POST">
              @csrf
            <button class="btn btn-outline-danger" type="submit">Deconnexion</button>
          </form>
        </div>
        @endif 
      </div>
    </div>
</nav>
<br><br>