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
          <div class="row mb-3">
            <div class="col-md-6">
              <label for="firstname" class="form-label">Prénom</label>
              <input type="text" id="firstname" name="firstname" placeholder="Prénom" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label for="lastname" class="form-label">Nom</label>
              <input type="text" id="lastname" name="lastname" placeholder="Nom" class="form-control" required>
            </div>
          </div>
      
          <div class="row mb-3">
            <div class="col-md-6">
              <label for="gender" class="form-label">Civilité *</label>
              <input type="text" id="gender" name="gender" placeholder="Civilité" class="form-control">
            </div>
            <div class="col-md-6">
              <label for="email" class="form-label">Adresse email *</label>
              <input type="email" id="email" name="email" placeholder="Adresse email" class="form-control" required>
            </div>
          </div>
      
          <div class="row mb-3">
            <div class="col-md-6">
              <label for="phone_number" class="form-label">Numéro de téléphone *</label>
              <input type="text" id="phone_number" name="phone_number" placeholder="Numéro de téléphone" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label for="address" class="form-label">Adresse *</label>
              <input type="text" id="address" name="address" placeholder="Adresse" class="form-control" required>
            </div>
          </div>
      
          <div class="row mb-3">
            <div class="col-md-6">
              <label for="postal_code" class="form-label">Code postal *</label>
              <input type="text" id="postal_code" name="postal_code" placeholder="Code postal" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label for="city" class="form-label">Ville *</label>
              <input type="text" id="city" name="city" placeholder="Ville" class="form-control" required>
            </div>
          </div>
      
          <div class="row mb-3">
            <div class="col-md-6">
              <label for="company" class="form-label">Entreprise</label>
              <input type="text" id="company" name="company" placeholder="Entreprise" class="form-control">
            </div>
            <div class="col-md-6">
              <label for="siret" class="form-label">Numéro de SIRET</label>
              <input type="text" id="siret" name="siret" placeholder="SIRET" class="form-control">
            </div>
          </div>
      
          <div class="text-end">
            <button type="submit" class="btn" style="background-color: #133a3f; color: white;">
              Valider
            </button>
          </div>
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
                  <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="firstname{{ $prospect->id }}" class="form-label">Prénom</label>
                        <input type="text" id="firstname{{ $prospect->id }}" name="firstname" value="{{ old('firstname', $prospect->firstname) }}" placeholder="Votre prénom" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label for="lastname{{ $prospect->id }}" class="form-label">Nom</label>
                        <input type="text" id="lastname{{ $prospect->id }}" name="lastname" value="{{ old('lastname', $prospect->lastname) }}" placeholder="Votre nom" class="form-control">
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="gender{{ $prospect->id }}" class="form-label">Civilité</label>
                        <input type="text" id="gender{{ $prospect->id }}" name="gender" value="{{ old('gender', $prospect->gender) }}" placeholder="Civilité" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label for="email{{ $prospect->id }}" class="form-label">Adresse email</label>
                        <input type="email" id="email{{ $prospect->id }}" name="email" value="{{ old('email', $prospect->email) }}" placeholder="Votre adresse email" class="form-control" required>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="phone_number{{ $prospect->id }}" class="form-label">Numéro de téléphone</label>
                        <input type="text" id="phone_number{{ $prospect->id }}" name="phone_number" value="{{ old('phone_number', $prospect->phone_number) }}" placeholder="Numéro de téléphone" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label for="address{{ $prospect->id }}" class="form-label">Adresse</label>
                        <input type="text" id="address{{ $prospect->id }}" name="address" value="{{ old('address', $prospect->address) }}" placeholder="Adresse" class="form-control" required>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="postal_code{{ $prospect->id }}" class="form-label">Code postal</label>
                        <input type="text" id="postal_code{{ $prospect->id }}" name="postal_code" value="{{ old('postal_code', $prospect->postal_code) }}" placeholder="Code postal" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                      <label for="postal_code{{ $prospect->id }}" class="form-label">Ville</label>
                      <input type="text" id="postal_code{{ $prospect->id }}" name="postal_code" value="{{ old('city', $prospect->postal_code) }}" placeholder="Ville" class="form-control" required>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="siret{{ $prospect->id }}" class="form-label">Siret</label>
                        <input type="text" id="siret{{ $prospect->id }}" name="siret" value="{{ old('siret', $prospect->siret) }}" placeholder="Siret" class="form-control">
                    </div>
                    <div class="col-md-6">
                      <label for="company{{ $prospect->id }}" class="form-label">Entreprise</label>
                      <input type="text" id="company{{ $prospect->id }}" name="company" value="{{ old('company', $prospect->company) }}" placeholder="Entreprise" class="form-control">
                  </div>
                </div>
                  
                  <button type="submit" class="btn" style="background-color: #133a3f; color: white;">Valider</button>
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