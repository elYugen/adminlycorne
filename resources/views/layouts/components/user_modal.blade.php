<!-- modal de création d'un utilisateur -->
<div class="modal fade" id="createUser" tabindex="-1" aria-labelledby="createUserLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="createUserLabel">Ajouter un nouvel utilisateur</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form action="{{ route('user.create')}}" method="post">
            @csrf
            <label>Nom : </label>
            <input type="text" name="name" class="form-control">
            <br>
            <label>Adresse mail : </label>
            <input type="email" name="email" class="form-control">
            <br>
            <label>Mot de passe :</label>
            <input type="password" name="password" class="form-control">
            <br>
            <select name="role" class="form-select">
              <option value="revendeur">Revendeur</option>
              <option value="administrateur">Administrateur</option>          
            </select>
            <br>
            <button type="submit" class="btn" style="background-color: #362258; color: white;">Valider</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  @foreach ($users as $user)
<!-- modal de modification d'un utilisateur -->
<div class="modal fade" id="editUser{{ $user->id }}" tabindex="-1" aria-labelledby="editUserLabel{{ $user->id }}" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="editUserLabel{{ $user->id }}">Modifier {{ $user->name }}</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form action="{{ route('user.edit', $user->id)}}" method="post">
            @csrf
            @method('PUT') 
            <label>Nom d'utilisateur :</label>
            <input type="text" name="name" value="{{ $user->name }}" class="form-control" required>
            <br>
            <label>Adresse mail :</label>
            <input type="email" name="email" value="{{ $user->email }}" class="form-control" required>
            <br>
            <label for="role{{ $user->id }}" class="form-label">Rôle :</label>
            <select name="role" id="role{{ $user->id }}" class="form-select" required>
              <option value="revendeur" {{ $user->role === 'revendeur' ? 'selected' : '' }}>Revendeur</option>
              <option value="administrateur" {{ $user->role === 'administrateur' ? 'selected' : '' }}>Administrateur</option>          
            </select>
            <br>
            <button type="submit" class="btn btn-primary">Valider</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- modal de suppression d'un utilisateur -->
<div class="modal fade" id="deleteUser{{ $user->id }}" tabindex="-1" aria-labelledby="deleteUserLabel{{ $user->id }}" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="deleteUserLabel{{ $user->id }}">Supprimer {{ $user->name }} ?</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form action="{{ route('user.delete', $user->id)}}" method="post">
            @csrf
            @method('DELETE') 
            <button type="submit" class="btn btn-danger">Supprimer</button>
          </form>
        </div>
      </div>
    </div>
  </div>
  @endforeach