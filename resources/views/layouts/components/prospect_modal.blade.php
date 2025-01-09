<!-- modal de création d'un utilisateur -->
<div class="modal fade" id="createUser" tabindex="-1" aria-labelledby="createUserLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="createUserLabel">Ajouter une nouvelle entreprise</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form action="{{ route('prospect.create')}}" method="post">
            @csrf
            <label>Prenom : </label>
            <input type="text" name="firstname" class="form-control" required>
            <br>
            <label>Nom :</label>
            <input type="text" name="lastname" class="form-control" required>
            <br>
            <label>Civilié :</label>
            <select name="gender"class="form-control" required>
                <option value="homme">Homme</option>
                <option value="femme">Femme</option>
            </select>
            <label>Adresse mail : </label>
            <input type="email" name="email" class="form-control" required>
            <br>
            <label>Numéro de Téléphone : </label>
            <input type="text" name="phone_number" class="form-control" required>
            <br>
            <label>Adresse :</label>
            <input type="text" name="address" class="form-control" required>
            <br>
            <label>Ville : </label>
            <input type="text" name="city" class="form-control">
            <br>
            <label>Code postal :</label>
            <input type="text" name="postal_code" class="form-control" required>
            <br>
            <label>Entreprise :</label>
            <input type="text" name="company" class="form-control" required>
            <br>
            <label>Siret :</label>
            <input type="text" name="siret" class="form-control" required>
            <br>
            <button type="submit" class="btn btn-primary">Valider</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  @foreach ($users as $user)
<!-- modal d'édition d'un client -->
<div class="modal fade" id="editUser{{ $user->id }}" tabindex="-1" aria-labelledby="editUserLabel{{ $user->id }}" aria-hidden="true">
  <div class="modal-dialog">
      <div class="modal-content">
          <div class="modal-header">
              <h1 class="modal-title fs-5" id="editUserLabel{{ $user->id }}">Modifier {{ $user->name }}</h1>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
          </div>
          <div class="modal-body">
              <form action="{{ route('prospect.edit', $user->id) }}" method="post">
                  @csrf
                  @method('PUT')
                  
                  <label>Prénom :</label>
                  <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control" required>
                  <br>
                  
                  <label>Nom :</label>
                  <input type="text" name="lastname" value="{{ old('lastname', $user->lastname) }}" class="form-control" required>
                  <br>
                  
                  <label>Civilité :</label>
                  <select name="civilite" class="form-control" required>
                      <option value="homme" {{ old('civilite', $user->civilite) == 'homme' ? 'selected' : '' }}>Homme</option>
                      <option value="femme" {{ old('civilite', $user->civilite) == 'femme' ? 'selected' : '' }}>Femme</option>
                  </select>
                  <br>
                  
                  <label>Adresse mail :</label>
                  <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control" required>
                  <br>
                  
                  <label>Numéro de téléphone :</label>
                  <input type="text" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}" class="form-control">
                  <br>
                  
                  <label>Adresse :</label>
                  <input type="text" name="address" value="{{ old('address', $user->address) }}" class="form-control">
                  <br>
                  
                  <label>Code postal :</label>
                  <input type="text" name="postal_code" value="{{ old('postal_code', $user->postal_code) }}" class="form-control">
                  <br>
                  
                  <label>Entreprise :</label>
                  <input type="text" name="entreprise" value="{{ old('entreprise', $user->entreprise) }}" class="form-control">
                  <br>
                  
                  <label>Siret :</label>
                  <input type="text" name="siret" value="{{ old('siret', $user->siret) }}" class="form-control">
                  <br>
                  
                  <button type="submit" class="btn btn-primary">Valider</button>
              </form>
          </div>
      </div>
  </div>
</div>
  
    <!-- modal de suppression d'un client -->
  <div class="modal fade" id="deleteUser{{ $user->id }}" tabindex="-1" aria-labelledby="deleteUserLabel{{ $user->id }}" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="deleteUserLabel{{ $user->id }}">Supprimer {{ $user->name }} ?</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form action="{{ route('prospect.delete', $user->id)}}" method="post">
              @csrf
              @method('DELETE') 
              <button type="submit" class="btn btn-danger">Supprimer</button>
            </form>
          </div>
        </div>
      </div>
    </div>
    @endforeach