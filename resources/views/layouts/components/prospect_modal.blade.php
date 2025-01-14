<div class="modal fade" id="createprospect" tabindex="-1" aria-labelledby="createprospectLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="createprospectLabel">Ajouter un nouveau prospect</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="{{ route('prospect.create')}}" method="post">
          @csrf
          <label>Prenom : </label>
          <input type="text" name="firstname" class="form-control">
          <br>
          <label>Nom :</label>
          <input type="text" name="lastname" class="form-control">
          <br>
          <label>Civilié :</label>
          <input type="text" name="gender" class="form-control">
          <br>
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
          <input type="text" name="city" class="form-control" required>
          <br>
          <label>Code postal :</label>
          <input type="text" name="postal_code" class="form-control" required>
          <br>
          <label>Entreprise :</label>
          <input type="text" name="company" class="form-control">
          <br>
          <label>Siret :</label>
          <input type="text" name="siret" class="form-control">
          <br>
          <button type="submit" class="btn" style="background-color: #362258; color: white;">Valider</button>
        </form>
      </div>
    </div>
  </div>
</div>

  @foreach ($prospects as $prospect)
<!-- modal d'édition d'un client -->
<div class="modal fade" id="editprospect{{ $prospect->id }}" tabindex="-1" aria-labelledby="editprospectLabel{{ $prospect->id }}" aria-hidden="true">
  <div class="modal-dialog">
      <div class="modal-content">
          <div class="modal-header">
              <h1 class="modal-title fs-5" id="editprospectLabel{{ $prospect->id }}">Modifier {{ $prospect->firstname }}</h1>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
          </div>
          <div class="modal-body">
              <form action="{{ route('prospect.edit', $prospect->id) }}" method="post">
                  @csrf
                  @method('PUT')
                  
                  <label>Prénom :</label>
                  <input type="text" name="firstname" value="{{ old('firstname', $prospect->firstname) }}" class="form-control">
                  <br>
                  
                  <label>Nom :</label>
                  <input type="text" name="lastname" value="{{ old('lastname', $prospect->lastname) }}" class="form-control">
                  <br>
                  
                  <label>Civilité :</label>
                  <input type="text" name="gender" value="{{ old('gender', $prospect->gender) }}" class="form-control">
                  <br>
                  
                  <label>Adresse mail :</label>
                  <input type="email" name="email" value="{{ old('email', $prospect->email) }}" class="form-control" required>
                  <br>
                  
                  <label>Numéro de téléphone :</label>
                  <input type="text" name="phone_number" value="{{ old('phone_number', $prospect->phone_number) }}" class="form-control" required>
                  <br>
                  
                  <label>Adresse :</label>
                  <input type="text" name="address" value="{{ old('address', $prospect->address) }}" class="form-control" required>
                  <br>
                  
                  <label>Code postal :</label>
                  <input type="text" name="postal_code" value="{{ old('postal_code', $prospect->postal_code) }}" class="form-control" required>
                  <br>
                  
                  <label>Nom de l'Entreprise :</label>
                  <input type="text" name="company" value="{{ old('entreprise', $prospect->company) }}" class="form-control">
                  <br>
                  
                  <label>Siret :</label>
                  <input type="text" name="siret" value="{{ old('siret', $prospect->siret) }}" class="form-control">
                  <br>
                  
                  <button type="submit" class="btn btn-primary">Valider</button>
              </form>
          </div>
      </div>
  </div>
</div>
  
    <!-- modal de suppression d'un client -->
  <div class="modal fade" id="deleteprospect{{ $prospect->id }}" tabindex="-1" aria-labelledby="deleteprospectLabel{{ $prospect->id }}" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="deleteprospectLabel{{ $prospect->id }}">Supprimer {{ $prospect->name }} ?</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form action="{{ route('prospect.delete', $prospect->id)}}" method="post">
              @csrf
              @method('DELETE') 
              <button type="submit" class="btn btn-danger">Supprimer</button>
            </form>
          </div>
        </div>
      </div>
    </div>
    @endforeach