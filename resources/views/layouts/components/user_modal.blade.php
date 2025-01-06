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
            <input type="text" name="password" class="form-control">
            <br>
            <button type="submit" class="btn btn-primary">Valider</button>
          </form>
        </div>
      </div>
    </div>
  </div>
